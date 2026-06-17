<?php

namespace App\Helpers;

class FormatoHelper
{
    /**
     * Formatear número al estilo venezolano: puntos para miles, coma para decimales
     * Ejemplo: 1234567.89 -> 1.234.567,89
     */
    public static function moneda($monto, $moneda = 'Bs', $decimales = 2)
    {
        if (is_null($monto) || $monto === '') {
            return $moneda . ' 0,00';
        }

        $monto = floatval($monto);
        $partes = explode('.', number_format($monto, $decimales, '.', ''));
        $entero = $partes[0];
        $decimal = $partes[1] ?? str_repeat('0', $decimales);

        // Agregar puntos para miles
        $entero_formateado = preg_replace('/(\d)(?=(\d{3})+(?!\d))/', '$1.', $entero);

        return $moneda . ' ' . $entero_formateado . ',' . $decimal;
    }

    /**
     * Solo formatear número sin símbolo de moneda
     * Ejemplo: 1234567.89 -> 1.234.567,89
     */
    public static function numero($numero, $decimales = 2)
    {
        if (is_null($numero) || $numero === '') {
            return '0,' . str_repeat('0', $decimales);
        }

        $numero = floatval($numero);
        $partes = explode('.', number_format($numero, $decimales, '.', ''));
        $entero = $partes[0];
        $decimal = $partes[1] ?? str_repeat('0', $decimales);

        $entero_formateado = preg_replace('/(\d)(?=(\d{3})+(?!\d))/', '$1.', $entero);

        return $entero_formateado . ',' . $decimal;
    }

    /**
     * Formatear porcentaje
     */
    public static function porcentaje($valor, $decimales = 1)
    {
        return self::numero($valor, $decimales) . '%';
    }

    /**
     * Convertir string formateado a float para operaciones
     * Ejemplo: "1.234.567,89" -> 1234567.89
     */
    public static function aFloat($valor_formateado)
    {
        if (is_null($valor_formateado) || $valor_formateado === '') {
            return 0;
        }

        // Quitar símbolo de moneda si existe
        $valor = preg_replace('/[^\d,.-]/', '', $valor_formateado);

        // Reemplazar puntos (miles) y coma (decimal)
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);

        return floatval($valor);
    }
}
