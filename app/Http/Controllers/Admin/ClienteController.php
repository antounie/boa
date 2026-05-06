<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::withCount(['reservas', 'ventas']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido', 'like', "%{$buscar}%")
                  ->orWhere('documento_identidad', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%");
            });
        }

        $clientes = $query->orderBy('id', 'desc')->paginate(10);
        return view('admin.clientes.index', compact('clientes'));
    }

    public function show(Cliente $cliente)
    {
        $cliente->load([
            'reservas.programacionVuelo.vuelo',
            'reservas.programacionVuelo.ruta.aeropuertoOrigen',
            'reservas.programacionVuelo.ruta.aeropuertoDestino',
            'reservas.asiento.tipoClase',
            'ventas.programacionVuelo.vuelo',
            'ventas.programacionVuelo.ruta.aeropuertoOrigen',
            'ventas.programacionVuelo.ruta.aeropuertoDestino',
            'ventas.asiento.tipoClase',
            'ventas.ticket',
        ]);

        return view('admin.clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('admin.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => 'required|string|max:80',
            'apellido' => 'required|string|max:80',
            'documento_identidad' => ['required', 'string', 'max:20', Rule::unique('clientes')->ignore($cliente->id)],
            'fecha_nacimiento' => 'required|date',
            'email' => 'required|email|max:100',
            'telefono' => 'nullable|string|max:20',
        ], [
            'documento_identidad.unique' => 'Este documento de identidad ya está registrado.',
        ]);

        $cliente->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'documento_identidad' => $request->documento_identidad,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'email' => $request->email,
            'telefono' => $request->telefono,
        ]);

        return redirect()->route('admin.clientes.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }
}