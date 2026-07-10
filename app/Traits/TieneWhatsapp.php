<?php

namespace App\Traits;

/**
 * Normaliza un número de teléfono a formato wa.me (indicativo +57 Colombia fijo por ahora)
 * y arma el link de click-to-chat de WhatsApp. Usado por Cliente y Configuracion.
 */
trait TieneWhatsapp
{
    protected function limpiarNumeroWhatsapp(?string $numero): ?string
    {
        if (!$numero) {
            return null;
        }

        $limpio = preg_replace('/\D+/', '', $numero);
        if (!$limpio) {
            return null;
        }

        if (!str_starts_with($limpio, '57') || strlen($limpio) === 10) {
            $limpio = '57' . ltrim($limpio, '0');
        }

        return $limpio;
    }

    protected function armarWhatsappUrl(?string $numero, string $mensaje = ''): ?string
    {
        if (!$numero) {
            return null;
        }

        // rawurlencode (RFC 3986, %20 para espacios) es lo que WhatsApp recomienda para
        // el parámetro ?text= de los links wa.me — no urlencode (usa "+" para espacios,
        // pensado para application/x-www-form-urlencoded, no para este caso).
        return 'https://wa.me/' . $numero . ($mensaje !== '' ? '?text=' . rawurlencode($mensaje) : '');
    }
}
