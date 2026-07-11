<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SerialesExtractorController extends Controller
{
    /**
     * Vista para subir factura
     */
    public function index()
    {
        return view('extractor-seriales');
    }

    /**
     * Procesar el archivo y extraer seriales
     */
    public function extract(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf,jpeg,png,jpg|max:10240',
        ]);

        try {
            $archivo = $request->file('archivo');

            // Guardar archivo temporal
            $path = $archivo->store('temp', 'public');
            $fullPath = Storage::path($path);

            // Extraer texto
            $texto = $this->extractText($fullPath);

            // Si no hay texto, error
            if (strlen(trim($texto)) < 10) {
                Storage::delete($path);
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo extraer texto. Verifica que el archivo sea legible.'
                ], 400);
            }

            // Extraer seriales
            $serials = $this->extractSerials($texto);

            // Limpiar archivo temporal
            Storage::delete($path);

            return response()->json([
                'success' => true,
                'serials' => $serials,
                'seriales_string' => implode('|', $serials),
                'total' => count($serials),
                'texto' => substr($texto, 0, 500)
            ]);

        } catch (\Exception $e) {
            if (isset($path)) {
                Storage::delete($path);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extraer texto usando Tesseract
     */
    private function extractText($rutaArchivo)
    {
        // Verificar Tesseract
        $check = shell_exec('which tesseract 2>/dev/null');
        if (empty($check)) {
            throw new \Exception('Tesseract no está instalado');
        }

        $extension = strtolower(pathinfo($rutaArchivo, PATHINFO_EXTENSION));
        $tempImage = null;
        $texto = '';

        // Si es PDF, convertir a imagen
        if ($extension === 'pdf') {
            $tempImage = $this->pdfToImage($rutaArchivo);
            if ($tempImage) {
                $rutaArchivo = $tempImage;
            } else {
                // Si no se pudo convertir, intentar extraer texto directamente
                $texto = $this->extractTextFromPdf($rutaArchivo);
                if (!empty($texto)) {
                    return $texto;
                }
            }
        }

        // Ejecutar Tesseract
        $outputFile = tempnam(sys_get_temp_dir(), 'ocr_');

        // Probar diferentes configuraciones
        $configs = [
            ['spa', '--psm 6'],
            ['spa', '--psm 3'],
            ['eng', '--psm 6'],
            ['spa+eng', '--psm 3']
        ];

        foreach ($configs as $config) {
            list($lang, $psm) = $config;
            $cmd = "tesseract \"{$rutaArchivo}\" \"{$outputFile}\" -l {$lang} {$psm} 2>/dev/null";
            shell_exec($cmd);

            $txtFile = $outputFile . '.txt';
            if (file_exists($txtFile)) {
                $content = file_get_contents($txtFile);
                if (strlen(trim($content)) > 50) {
                    $texto = $content;
                    break;
                }
            }
        }

        // Limpiar archivos
        if ($tempImage && file_exists($tempImage)) {
            unlink($tempImage);
        }
        if (file_exists($outputFile . '.txt')) {
            unlink($outputFile . '.txt');
        }
        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        return $texto;
    }

    /**
     * Convertir PDF a imagen
     */
    private function pdfToImage($rutaArchivo)
    {
        $tempImage = tempnam(sys_get_temp_dir(), 'pdf_img_') . '.png';

        // Método 1: pdftoppm
        $cmd = "pdftoppm -png -r 300 -singlefile \"{$rutaArchivo}\" \"" . str_replace('.png', '', $tempImage) . "\" 2>/dev/null";
        shell_exec($cmd);

        // Si no funciona, probar con Ghostscript
        if (!file_exists($tempImage) || filesize($tempImage) < 1000) {
            $cmd = "gs -dNOPAUSE -dBATCH -sDEVICE=png16m -r300 -sOutputFile=\"{$tempImage}\" \"{$rutaArchivo}\" 2>/dev/null";
            shell_exec($cmd);
        }

        // Si no funciona, probar con ImageMagick
        if (!file_exists($tempImage) || filesize($tempImage) < 1000) {
            $cmd = "convert -density 300 \"{$rutaArchivo}[0]\" -quality 100 \"{$tempImage}\" 2>/dev/null";
            shell_exec($cmd);
        }

        // Verificar si se creó la imagen
        if (file_exists($tempImage) && filesize($tempImage) > 1000) {
            return $tempImage;
        }

        if (file_exists($tempImage)) {
            unlink($tempImage);
        }

        return null;
    }

    /**
     * Extraer texto directamente de PDF (para PDFs con texto)
     */
    private function extractTextFromPdf($rutaArchivo)
    {
        $outputFile = tempnam(sys_get_temp_dir(), 'pdf_txt_');
        $cmd = "pdftotext \"{$rutaArchivo}\" \"{$outputFile}\" 2>/dev/null";
        shell_exec($cmd);

        $txtFile = $outputFile . '.txt';
        $texto = file_exists($txtFile) ? file_get_contents($txtFile) : '';

        if (file_exists($txtFile)) {
            unlink($txtFile);
        }
        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        return $texto;
    }

    /**
     * Extraer seriales del texto
     */
    private function extractSerials($texto)
    {
        $serials = [];

        // Limpiar texto
        $texto = str_replace(["\r\n", "\r", "\n"], ' ', $texto);
        $texto = preg_replace('/\s+/', ' ', $texto);

        // 1. Buscar CHASIS (17 caracteres alfanuméricos)
        preg_match_all('/\b[A-Z0-9]{17}\b/', $texto, $matches);
        $chasis = array_unique($matches[0] ?? []);

        // 2. Buscar MOTOR (con * * )
        preg_match_all('/\b[A-Z0-9]+\*[0-9]{8}\*\b/', $texto, $matches);
        $motores = array_unique($matches[0] ?? []);

        // 3. Buscar PLACA (formato venezolano: AA0A00A)
        preg_match_all('/\b[A-Z]{2}[0-9][A-Z][0-9]{2}[A-Z]\b/', $texto, $matches);
        $placas = array_unique($matches[0] ?? []);

        // 4. Si hay CHASIS, formatear cada uno
        if (count($chasis) > 0) {
            foreach ($chasis as $index => $ch) {
                $serial = "SC " . substr($ch, -4);

                // Buscar motor correspondiente
                if (isset($motores[$index])) {
                    preg_match('/\*([0-9]{8})\*/', $motores[$index], $m);
                    if (isset($m[1])) {
                        $serial .= " SM " . substr($m[1], -4);
                    }
                }

                // Buscar placa correspondiente
                if (isset($placas[$index])) {
                    $serial .= " PLACA " . $placas[$index];
                }

                $serials[] = $serial;
            }
        } else {
            // Buscar patrones genéricos (seriales alfanuméricos largos)
            preg_match_all('/\b[A-Z0-9]{8,20}\b/', $texto, $matches);
            $genericos = array_unique($matches[0] ?? []);

            // Palabras comunes a ignorar
            $ignorar = ['FACTURA', 'SERIE', 'NOMBRE', 'TELEFONO', 'TOTAL', 'SUBTOTAL', 'IVA', 'RIF', 'RFC', 'DIRECCION'];

            foreach ($genericos as $serial) {
                if (!in_array($serial, $ignorar) && !is_numeric($serial) && strlen($serial) >= 8) {
                    $serials[] = $serial;
                }
            }

            // Si no hay seriales, buscar números largos
            if (empty($serials)) {
                preg_match_all('/\b[0-9]{10,15}\b/', $texto, $matches);
                $numeros = array_unique($matches[0] ?? []);
                foreach ($numeros as $num) {
                    if (strlen($num) >= 10) {
                        $serials[] = $num;
                    }
                }
            }
        }

        return array_values(array_unique($serials));
    }
}
