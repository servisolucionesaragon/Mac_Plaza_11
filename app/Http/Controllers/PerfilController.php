<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    public function edit()
    {
        return view('perfil.edit');
    }

    public function update(Request $request)
    {
        $usuario = Auth::user();

        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $usuario->id,
            'telefono' => 'nullable|string|max:20',
        ]);

        $usuario->update($validated);

        return back()->with('success', 'Tus datos se actualizaron correctamente.');
    }

    public function updatePassword(Request $request)
    {
        $usuario = Auth::user();

        $validated = $request->validate([
            'password_actual' => 'required|string',
            'password'        => ['required', 'string', 'confirmed', Password::min(8)],
        ], [
            'password_actual.required' => 'Debes ingresar tu contraseña actual.',
        ]);

        if (!Hash::check($validated['password_actual'], $usuario->password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta.'])->withInput();
        }

        $usuario->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Tu contraseña se actualizó correctamente.');
    }
}
