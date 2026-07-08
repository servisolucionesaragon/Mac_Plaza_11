<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', "%{$request->buscar}%")
                  ->orWhere('apellido', 'like', "%{$request->buscar}%")
                  ->orWhere('email', 'like', "%{$request->buscar}%")
                  ->orWhere('telefono', 'like', "%{$request->buscar}%")
                  ->orWhere('dni', 'like', "%{$request->buscar}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $clientes = $query->withCount(['ventas', 'reparaciones'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'          => 'required|string|max:100',
            'apellido'        => 'required|string|max:100',
            'email'           => 'nullable|email|unique:clientes,email|max:150',
            'telefono'        => 'required|string|max:20',
            'celular'         => 'nullable|string|max:20',
            'dni'             => 'nullable|string|max:15|unique:clientes,dni',
            'tipo_documento'  => 'nullable|in:Tarjeta de identidad,Cédula de ciudadanía,Tarjeta de extranjería,Cédula de extranjería,NIT,Pasaporte',
            'direccion'       => 'nullable|string|max:255',
            'departamento'    => 'nullable|string|max:100',
            'ciudad'          => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'tipo'            => 'required|in:particular,empresa',
            'empresa'         => 'nullable|string|max:150',
            'ruc'             => 'nullable|string|max:15',
            'notas'           => 'nullable|string',
        ]);

        Cliente::create($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente registrado correctamente.');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load(['ventas.detalles.producto', 'reparaciones']);
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre'          => 'required|string|max:100',
            'apellido'        => 'required|string|max:100',
            'email'           => 'nullable|email|unique:clientes,email,' . $cliente->id . '|max:150',
            'telefono'        => 'required|string|max:20',
            'celular'         => 'nullable|string|max:20',
            'dni'             => 'nullable|string|max:15|unique:clientes,dni,' . $cliente->id,
            'tipo_documento'  => 'nullable|in:Tarjeta de identidad,Cédula de ciudadanía,Tarjeta de extranjería,Cédula de extranjería,NIT,Pasaporte',
            'direccion'       => 'nullable|string|max:255',
            'departamento'    => 'nullable|string|max:100',
            'ciudad'          => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'tipo'            => 'required|in:particular,empresa',
            'empresa'         => 'nullable|string|max:150',
            'ruc'             => 'nullable|string|max:15',
            'notas'           => 'nullable|string',
            'activo'          => 'boolean',
        ]);

        $cliente->update($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        if ($cliente->ventas()->count() > 0 || $cliente->reparaciones()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: el cliente tiene ventas o reparaciones registradas.');
        }

        $cliente->delete();
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
