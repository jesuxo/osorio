<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\PdfToText\Pdf;
use Illuminate\Support\Facades\Log;

class ComprasController extends Controller
{
    public function index()
    {
        return view('compras.subir-factura-seriales');
    }

    public function extraerSeriales(Request $request)
    {
        try {
            $request->validate([
                'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
            ]);

            $archivo = $request->file('archivo');
            $extension = $archivo->getClientOriginalExtension();
            $path = $archivo->getPathname();

            // Extraer texto
            $texto = $this->extractText($path, $extension);

            // Debug: guardar texto extraído
            Log::info('Texto extraído', ['texto' => substr($texto, 0, 500)]);

            // Extraer seriales
            $seriales = $this->extractSerialNumbers($texto);

            if (empty($seriales)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron seriales en el documento. Por favor, verifica que el PDF tenga las placas, chasis y motores visibles.'
                ], 400);
            }

            $seriales_string = implode('|', $seriales);

            return response()->json([
                'success' => true,
                'message' => 'Seriales extraídos correctamente',
                'total' => count($seriales),
                'seriales' => $seriales,
                'seriales_string' => $seriales_string
            ]);

        } catch (\Exception $e) {
            Log::error('Error al extraer seriales: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    private function extractText($path, $extension)
    {
        if ($extension === 'pdf') {
            return $this->extractFromPdf($path);
        } else {
            return $this->extractFromImage($path);
        }
    }

    private function extractFromPdf($path)
    {
        try {
            // Intentar extraer texto directamente (más rápido y sin OCR)
            Log::info('Extrayendo texto del PDF con Spatie');

            $text = Pdf::getText($path, null, [
                '--layout',
                '-f 1',
                '-l 1'
            ]);

            Log::info('Texto extraído con Spatie', ['length' => strlen($text)]);

            if (empty(trim($text))) {
                Log::warning('No se obtuvo texto con Spatie, intentando con OCR');
                $text = $this->extractWithOCR($path, 'pdf');
            }

            return $text;
        } catch (\Exception $e) {
            Log::warning('Error al leer PDF con Spatie: ' . $e->getMessage());

            // Si falla Spatie, intentar con OCR
            try {
                return $this->extractWithOCR($path, 'pdf');
            } catch (\Exception $ocrException) {
                Log::error('OCR también falló: ' . $ocrException->getMessage());
                throw new \Exception('No se pudo extraer texto del PDF. Asegúrate de que el archivo no esté dañado.');
            }
        }
    }

    private function extractFromImage($path)
    {
        return $this->extractWithOCR($path, 'image');
    }

    private function extractWithOCR($path, $type)
    {
        try {
            // Verificar Tesseract
            if (!shell_exec('which tesseract')) {
                throw new \Exception('Tesseract OCR no está instalado. Instálalo con: sudo apt-get install tesseract-ocr tesseract-ocr-spa');
            }

            $tempFile = tempnam(sys_get_temp_dir(), 'ocr_');

            if ($type === 'pdf') {
                $imageFile = $tempFile . '.png';
                $this->convertPdfToImage($path, $imageFile);
                $inputFile = $imageFile;
            } else {
                $inputFile = $path;
            }

            $outputFile = $tempFile . '_output';
            $command = "tesseract \"{$inputFile}\" \"{$outputFile}\" -l spa+eng --psm 6 2>&1";
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('Error al ejecutar OCR: ' . implode("\n", $output));
            }

            $text = file_get_contents($outputFile . '.txt');

            // Limpiar archivos temporales
            @unlink($tempFile);
            @unlink($outputFile . '.txt');
            if (isset($imageFile) && file_exists($imageFile)) {
                @unlink($imageFile);
            }

            return $text ?: '';

        } catch (\Exception $e) {
            Log::error('Error en OCR: ' . $e->getMessage());
            throw new \Exception('No se pudo extraer texto del documento: ' . $e->getMessage());
        }
    }

    private function convertPdfToImage($pdfPath, $imagePath)
    {
        // Usar Ghostscript (más confiable y menos problemas de seguridad)
        Log::info('Convirtiendo PDF a imagen con Ghostscript');

        $command = "gs -dNOPAUSE -dBATCH -sDEVICE=png16m -r300 -dFirstPage=1 -dLastPage=1 -sOutputFile=\"{$imagePath}\" \"{$pdfPath}\" 2>&1";
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            $errorMsg = implode("\n", $output);
            Log::error('Error en Ghostscript: ' . $errorMsg);
            throw new \Exception('Error al convertir PDF a imagen: ' . $errorMsg);
        }

        // Verificar que la imagen se creó
        if (!file_exists($imagePath) || filesize($imagePath) === 0) {
            throw new \Exception('No se pudo crear la imagen del PDF');
        }

        Log::info('Imagen creada correctamente', ['path' => $imagePath, 'size' => filesize($imagePath)]);
    }

    private function extractSerialNumbers($text)
    {
        $seriales = [];

        Log::info('Iniciando extracción de seriales', ['text_length' => strlen($text)]);

        // Buscar todas las placas (AY...G)
        preg_match_all('/AY\w{1}X\d{2}G/', $text, $plates);
        $plates = $plates[0] ?? [];

        // Buscar todos los chasis
        preg_match_all('/8Z5CATBN\w{2}TM(\d{6})/', $text, $chassis);
        $chassis = $chassis[1] ?? [];

        // Buscar todos los motores
        preg_match_all('/KW167FMM\*(\d{8})\*/', $text, $motors);
        $motors = $motors[1] ?? [];

        Log::info('Cantidades encontradas', [
            'chassis' => count($chassis),
            'motors' => count($motors),
            'plates' => count($plates)
        ]);

        $count = min(count($chassis), count($motors), count($plates));

        for ($i = 0; $i < $count; $i++) {
            $chasisLast4 = substr($chassis[$i], -4);
            $motorLast4 = substr($motors[$i], -4);
            $placa = $plates[$i];

            $serial = "SC {$chasisLast4} SM {$motorLast4} PLACA {$placa}";
            $seriales[] = $serial;
        }

        return $seriales;
    }
}
