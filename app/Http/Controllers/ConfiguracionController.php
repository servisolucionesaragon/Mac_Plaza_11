<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
    public function index()
    {
        return view('configuracion.index');
    }

    public function updateGeneral(Request $request)
    {
        $config = Configuracion::actual();

        $validated = $request->validate([
            'nombre_tienda'   => 'required|string|max:255',
            'ruc'             => 'nullable|string|max:20',
            'telefono'        => 'nullable|string|max:20',
            'departamento'    => 'nullable|string|max:100',
            'ciudad'          => 'nullable|string|max:100',
            'direccion'       => 'nullable|string|max:255',
            'email'           => 'nullable|email|max:150',
            'pagina_web'      => 'nullable|string|max:150',
            'timezone'        => 'required|string|max:64',
            'moneda'          => 'required|string|max:10',
            'simbolo_moneda'  => 'required|string|max:10',
            'igv'             => 'required|numeric|min:0|max:100',
            'logo'            => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,bmp|max:4096',
            'color_primario'    => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_secundario'  => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_acento'      => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_sidebar'     => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_menu_texto'  => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_menu_activo' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_boton_texto' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_boton_fondo' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_grafico_1'   => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_grafico_2'   => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_grafico_3'   => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_login_fondo'          => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_login_tarjeta'        => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_login_texto_modulos'  => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_paginacion_texto'         => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_paginacion_activo_fondo'  => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_paginacion_activo_texto'  => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'descuento_distribuidor'     => 'required|numeric|min:0|max:100',
        ], [
            'logo.image'  => 'El archivo debe ser una imagen (JPG, PNG, GIF, BMP o WEBP). Fotos en formato HEIC de iPhone no son compatibles: conviértelas a JPG o PNG antes de subirlas.',
            'logo.mimes'  => 'El archivo debe ser una imagen (JPG, PNG, GIF, BMP o WEBP). Fotos en formato HEIC de iPhone no son compatibles: conviértelas a JPG o PNG antes de subirlas.',
            'logo.max'    => 'El logo no puede pesar más de 4 MB.',
        ]);

        if ($request->hasFile('logo')) {
            if ($config->logo) {
                Storage::disk('public')->delete($config->logo);
            }
            $validated['logo'] = $request->file('logo')->store('configuracion', 'public');
        }

        $config->update($validated);

        return redirect()->route('configuracion.index')->with('success', 'Configuración general actualizada correctamente.');
    }

}
