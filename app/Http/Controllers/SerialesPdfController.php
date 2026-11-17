<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SerialesPdfController extends Controller
{
    public function index()
    {
        return view('seriales-pdf');
    }

    public function extraer(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:10240',
        ]);

        $path = $request->file('pdf')->store('temp_pdf');
        $full = Storage::path($path);

        try {
            $texto = $this->extraerTextoNativo($full);

            if (strlen(trim($texto)) < 100) {
                $texto = $this->extraerTextoOCR($full);
            }

            $filas = $this->parsearSeriales($texto);

            if (empty($filas)) {
                return response()->json([
                    'success' => false,
                    'error'   => 'No se detectaron seriales. Asegúrate de que el PDF sea legible.',
                ], 422);
            }

            $partes = [];
            foreach ($filas as $f) {
                $partes[] = "SC {$f['sc']} SM {$f['sm']} {$f['placa']}";
            }

            return response()->json([
                'success' => true,
                'filas'   => $filas,
                'cadena'  => implode('|', $partes),
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => 'Error procesando el PDF: ' . $e->getMessage(),
            ], 500);
        } finally {
            Storage::delete($path);
        }
    }

    private function extraerTextoNativo(string $pdfPath): string
    {
        $out = tempnam(sys_get_temp_dir(), 'txt_');
        $cmd = sprintf('pdftotext -layout %s %s 2>&1', escapeshellarg($pdfPath), escapeshellarg($out));
        @exec($cmd, $salida, $code);

        if ($code !== 0 || !file_exists($out)) {
            @unlink($out);
            return '';
        }

        $texto = file_get_contents($out);
        @unlink($out);
        return $texto ?: '';
    }

    private function extraerTextoOCR(string $pdfPath): string
    {
        $tmpDir = sys_get_temp_dir() . '/ocr_' . uniqid();
        @mkdir($tmpDir, 0777, true);

        try {
            $cmd = sprintf(
                'pdftoppm -r 200 -png %s %s/page 2>&1',
                escapeshellarg($pdfPath),
                escapeshellarg($tmpDir)
            );
            @exec($cmd, $o1, $c1);

            $texto = '';
            foreach (glob($tmpDir . '/page-*.png') as $img) {
                $txtOut = $img . '.txt';
                $cmd = sprintf(
                    'tesseract %s %s --psm 6 -l spa 2>&1',
                    escapeshellarg($img),
                    escapeshellarg($img)
                );
                @exec($cmd, $o2, $c2);
                if (file_exists($txtOut)) {
                    $texto .= file_get_contents($txtOut) . "\n";
                    @unlink($txtOut);
                }
            }
            return $texto;
        } finally {
            array_map('unlink', glob($tmpDir . '/page-*.png') ?: []);
            @rmdir($tmpDir);
        }
    }

    private function parsearSeriales(string $texto): array
    {
        $filas = [];

        $lineas = preg_split('/\r\n|\r|\n/', $texto);
        $buffer = '';
        foreach ($lineas as $linea) {
            $linea = trim($linea);
            if ($linea === '') continue;
            $buffer .= ' ' . $linea;
        }

        $patron = '/([A-Z0-9]{17})\s+(KW\d+FMJ\*?\d+\*?)\s+([A-Z0-9]{6,7})/i';

        if (preg_match_all($patron, $buffer, $m, PREG_SET_ORDER)) {
            foreach ($m as $match) {
                $chasis = strtoupper(trim($match[1]));
                $motor  = strtoupper(trim($match[2]));
                $placa  = strtoupper(trim($match[3]));

                $sc = substr(preg_replace('/\D/', '', $chasis), -4);
                if (preg_match('/FMJ\*?(\d+)/i', $motor, $mm)) {
                    $sm = substr($mm[1], -4);
                } else {
                    $sm = preg_replace('/\D/', '', $motor);
                }

                $filas[] = compact('chasis', 'motor', 'placa', 'sc', 'sm');
            }
        }

        if (empty($filas)) {
            $chasisRe = '/\b([A-Z0-9]{17})\b/i';
            $motorRe  = '/\b(KW\d+FMJ\*?\d+\*?)\b/i';
            $placaRe  = '/\b(A[O0]?[A-Z0-9]{5,6}|[O0][A-Z0-9]{6})\b/i';

            preg_match_all($chasisRe, $buffer, $mc);
            preg_match_all($motorRe, $buffer, $mm);
            preg_match_all($placaRe, $buffer, $mp);

            $n = min(count($mc[1]), count($mm[1]));
            for ($i = 0; $i < $n; $i++) {
                $chasis = strtoupper($mc[1][$i]);
                $motor  = strtoupper($mm[1][$i]);
                $placa  = isset($mp[1][$i]) ? strtoupper($mp[1][$i]) : '';

                $sc = substr(preg_replace('/\D/', '', $chasis), -4);
                preg_match('/FMJ\*?(\d+)/i', $motor, $x);
                $sm = substr($x[1] ?? preg_replace('/\D/', '', $motor), -4);

                $filas[] = compact('chasis', 'motor', 'placa', 'sc', 'sm');
            }
        }

        return $filas;
    }
}
