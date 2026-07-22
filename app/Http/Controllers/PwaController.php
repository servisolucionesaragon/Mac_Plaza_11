<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PwaController extends Controller
{
    public function manifest()
    {
        $config = Configuracion::actual();
        $nombre = $config->nombre_tienda ?: 'CRM Celulares';

        return response()->json([
            'name'             => $nombre,
            'short_name'       => Str::limit($nombre, 15, ''),
            'start_url'        => url('/'),
            'scope'            => url('/'),
            'display'          => 'standalone',
            'background_color' => '#ffffff',
            'theme_color'      => $config->color_primario ?: '#a855f7',
            'icons' => [
                ['src' => route('pwa.icon', ['size' => 192]), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'],
                ['src' => route('pwa.icon', ['size' => 512]), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ],
        ])->header('Content-Type', 'application/manifest+json');
    }

    public function icon(int $size)
    {
        abort_unless(in_array($size, [192, 512]), 404);

        $config = Configuracion::actual();
        $version = $config->updated_at?->timestamp ?? 0;
        $cachePath = "pwa/icon-{$size}-{$version}.png";

        if (! Storage::disk('public')->exists($cachePath)) {
            Storage::disk('public')->put($cachePath, $this->generarIcono($config, $size));
        }

        return response(Storage::disk('public')->get($cachePath), 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=604800');
    }

    /** Redimensiona el logo configurado a un ícono cuadrado; si no hay logo, genera un cuadrado sólido con el color primario. */
    private function generarIcono(Configuracion $config, int $size): string
    {
        $lienzo = imagecreatetruecolor($size, $size);
        imagesavealpha($lienzo, true);
        $transparente = imagecolorallocatealpha($lienzo, 0, 0, 0, 127);
        imagefill($lienzo, 0, 0, $transparente);

        $origen = ($config->logo && Storage::disk('public')->exists($config->logo))
            ? $this->cargarImagen(Storage::disk('public')->path($config->logo))
            : null;

        if ($origen) {
            $anchoOrigen = imagesx($origen);
            $altoOrigen = imagesy($origen);
            $escala = min($size / $anchoOrigen, $size / $altoOrigen);
            $anchoDestino = (int) round($anchoOrigen * $escala);
            $altoDestino = (int) round($altoOrigen * $escala);
            $x = (int) (($size - $anchoDestino) / 2);
            $y = (int) (($size - $altoDestino) / 2);

            imagecopyresampled($lienzo, $origen, $x, $y, 0, 0, $anchoDestino, $altoDestino, $anchoOrigen, $altoOrigen);
            imagedestroy($origen);
        } else {
            imagefill($lienzo, 0, 0, $this->hexAColor($lienzo, $config->color_primario ?: '#a855f7'));
        }

        ob_start();
        imagepng($lienzo);
        $datos = ob_get_clean();
        imagedestroy($lienzo);

        return $datos;
    }

    private function cargarImagen(string $ruta)
    {
        $info = @getimagesize($ruta);
        if (! $info) {
            return null;
        }

        return match ($info[2]) {
            IMAGETYPE_PNG  => imagecreatefrompng($ruta),
            IMAGETYPE_JPEG => imagecreatefromjpeg($ruta),
            IMAGETYPE_GIF  => imagecreatefromgif($ruta),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($ruta) : null,
            IMAGETYPE_BMP  => function_exists('imagecreatefrombmp') ? imagecreatefrombmp($ruta) : null,
            default => null,
        };
    }

    private function hexAColor($lienzo, string $hex)
    {
        $hex = ltrim($hex, '#');
        [$r, $g, $b] = array_map('hexdec', str_split($hex, 2));

        return imagecolorallocate($lienzo, $r, $g, $b);
    }
}
