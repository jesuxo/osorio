<?php
// app/Helpers/ImagenProductoHelper.php

namespace App\Helpers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImagenProductoHelper
{
    /** Máximo lado de la imagen "grande" (ficha de producto) */
    const MAX_GRANDE = 1400;
    /** Máximo lado del thumb (OG image, cards, carrito) */
    const MAX_THUMB  = 600;

    const CALIDAD_GRANDE = 82;
    const CALIDAD_THUMB  = 75;

    /**
     * Procesa una imagen subida y genera:
     *  - imagen grande (webp si es posible, si no jpg/png)
     *  - thumb optimizado
     *
     * @return array{imagen:string, thumb:string, original:string, ancho:int, alto:int}|null
     */
    public static function procesar($archivo, string $codprod): ?array
    {
        try {
            $baseDir = public_path('uploads/productos');
            if (!is_dir($baseDir)) {
                mkdir($baseDir, 0777, true);
            }
            $thumbDir = $baseDir . '/thumbs';
            if (!is_dir($thumbDir)) {
                mkdir($thumbDir, 0777, true);
            }

            $slug     = preg_replace('/[^A-Za-z0-9_-]/', '', $codprod);
            $uniq     = time() . '_' . bin2hex(random_bytes(3));
            $manager  = new ImageManager(new Driver());
            $img      = $manager->read($archivo->getPathname());

            // ---- Imagen grande ----
            $imgGrande = clone $img;
            $ratio = min(1, self::MAX_GRANDE / max($imgGrande->width(), $imgGrande->height()));
            if ($ratio < 1) {
                $imgGrande->resize(
                    (int) round($imgGrande->width() * $ratio),
                    (int) round($imgGrande->height() * $ratio)
                );
            }

            $nombreGrande = "{$slug}_{$uniq}.jpg";
            $rutaGrande   = $baseDir . '/' . $nombreGrande;
            $imgGrande->toJpeg(self::CALIDAD_GRANDE)->save($rutaGrande);

            // ---- Thumb ----
            $imgThumb = clone $img;
            $ratioT   = min(1, self::MAX_THUMB / max($imgThumb->width(), $imgThumb->height()));
            if ($ratioT < 1) {
                $imgThumb->resize(
                    (int) round($imgThumb->width() * $ratioT),
                    (int) round($imgThumb->height() * $ratioT)
                );
            }
            $nombreThumb = "{$slug}_{$uniq}_thumb.jpg";
            $rutaThumb   = $thumbDir . '/' . $nombreThumb;
            $imgThumb->toJpeg(self::CALIDAD_THUMB)->save($rutaThumb);

            return [
                'imagen'          => 'uploads/productos/' . $nombreGrande,
                'thumb'           => 'uploads/productos/thumbs/' . $nombreThumb,
                'original'        => $archivo->getClientOriginalName(),
                'ancho'           => $imgGrande->width(),
                'alto'            => $imgGrande->height(),
            ];
        } catch (\Throwable $e) {
            \Log::error('Error procesando imagen producto: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Borra los archivos físicos de un producto (grande + thumb).
     */
    public static function borrar(?string $imagen, ?string $thumb): void
    {
        foreach ([$imagen, $thumb] as $rel) {
            if ($rel && file_exists(public_path($rel))) {
                @unlink(public_path($rel));
            }
        }
    }
}
