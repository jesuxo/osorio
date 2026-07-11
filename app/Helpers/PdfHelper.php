<?php

namespace App\Helpers;

use Spatie\PdfToText\Pdf;

class PdfHelper
{
    public static function extractSerialNumbers($pdfPath)
    {
        // Extraer texto del PDF
        $text = Pdf::getText($pdfPath);

        // Dividir en líneas
        $lines = explode("\n", $text);

        $vehicles = [];
        $currentVehicle = [];

        foreach ($lines as $line) {
            $line = trim($line);

            // Buscar líneas con CHASIS (VIN)
            if (strpos($line, '8Z5CATBN') !== false) {
                $currentVehicle['chasis'] = $line;
            }

            // Buscar líneas con MOTOR
            if (strpos($line, 'KW167FMM') !== false) {
                $currentVehicle['motor'] = $line;
            }

            // Buscar líneas con PLACA
            if (strpos($line, 'AY') !== false && strpos($line, 'G') !== false) {
                $currentVehicle['placa'] = $line;
            }

            // Si tenemos los 3 datos, guardamos el vehículo
            if (isset($currentVehicle['chasis']) && isset($currentVehicle['motor']) && isset($currentVehicle['placa'])) {
                $vehicles[] = $currentVehicle;
                $currentVehicle = [];
            }
        }

        return $vehicles;
    }

    public static function formatSerialNumbers($vehicles)
    {
        $formatted = [];

        foreach ($vehicles as $vehicle) {
            // Extraer los últimos 4 dígitos del chasis
            $chasisLast4 = substr($vehicle['chasis'], -4);

            // Extraer los últimos 4 dígitos del motor (solo números)
            preg_match('/\*(\d+)\*/', $vehicle['motor'], $matches);
            $motorLast4 = isset($matches[1]) ? substr($matches[1], -4) : '';

            // Formatear: SC [últimos 4 del chasis] SM [últimos 4 del motor] PLACA [placa]
            $formatted[] = "SC {$chasisLast4} SM {$motorLast4} PLACA {$vehicle['placa']}";
        }

        return implode('|', $formatted);
    }
}
