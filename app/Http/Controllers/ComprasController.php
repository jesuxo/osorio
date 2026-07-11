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
            \Log::info('Texto extraído', ['texto' => substr($texto, 0, 500)]);
            file_put_contents(storage_path('logs/texto_extraido_' . date('Y-m-d_H-i-s') . '.txt'), $texto);

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
            // Configurar opciones para mejor extracción
            $text = Pdf::getText($path, null, [
                '--layout',
                '-f 1',
                '-l 1'
            ]);

            if (empty(trim($text))) {
                $text = $this->extractWithOCR($path, 'pdf');
            }

            return $text;
        } catch (\Exception $e) {
            Log::warning('Error al leer PDF con Spatie: ' . $e->getMessage());
            return $this->extractWithOCR($path, 'pdf');
        }
    }

    private function extractFromImage($path)
    {
        return $this->extractWithOCR($path, 'image');
    }

    private function extractWithOCR($path, $type)
    {
        try {
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
            // Usar español e inglés para mejor reconocimiento
            $command = "tesseract \"{$inputFile}\" \"{$outputFile}\" -l spa+eng --psm 6 2>&1";
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('Error al ejecutar OCR: ' . implode("\n", $output));
            }

            $text = file_get_contents($outputFile . '.txt');

            // Limpiar archivos temporales
            @unlink($tempFile);
            @unlink($outputFile . '.txt');
            if (isset($imageFile)) {
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
        if (extension_loaded('imagick')) {
            $imagick = new \Imagick();
            $imagick->setResolution(300, 300);
            $imagick->readImage($pdfPath . '[0]');
            $imagick->setImageFormat('png');
            $imagick->writeImage($imagePath);
            $imagick->clear();
            $imagick->destroy();
        } else {
            $command = "gs -dNOPAUSE -dBATCH -sDEVICE=png16m -r300 -dFirstPage=1 -dLastPage=1 -sOutputFile=\"{$imagePath}\" \"{$pdfPath}\" 2>&1";
            exec($command, $output, $returnCode);
            if ($returnCode !== 0) {
                throw new \Exception('Error al convertir PDF a imagen: ' . implode("\n", $output));
            }
        }
    }

    private function extractSerialNumbers($text)
    {
        $seriales = [];

        // Debug: Ver el texto
        \Log::info('Iniciando extracción de seriales', ['text_length' => strlen($text)]);



        return $seriales;
    }
}
