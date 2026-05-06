<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\TipoClase;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TipoClaseController extends Controller
{
    public function index()
    {
        $tipoClases = TipoClase::withCount('asientos')->orderBy('id', 'asc')->paginate(10);
        return view('operador.tipo_clases.index', compact('tipoClases'));
    }

    public function create()
    {
        return view('operador.tipo_clases.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:tipo_clases',
            'descripcion' => 'nullable|string|max:200',
            'caracteristicas' => 'nullable|string',
        ], [
            'nombre.unique' => 'Este tipo de clase ya existe.',
        ]);

        TipoClase::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'caracteristicas' => $request->caracteristicas,
        ]);

        return redirect()->route('operador.tipo-clases.index')
            ->with('success', 'Tipo de clase registrado exitosamente.');
    }

    public function edit(TipoClase $tipo_clase)
    {
        return view('operador.tipo_clases.edit', compact('tipo_clase'));
    }

    public function update(Request $request, TipoClase $tipo_clase)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:50', Rule::unique('tipo_clases')->ignore($tipo_clase->id)],
            'descripcion' => 'nullable|string|max:200',
            'caracteristicas' => 'nullable|string',
        ], [
            'nombre.unique' => 'Este tipo de clase ya existe.',
        ]);

        $tipo_clase->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'caracteristicas' => $request->caracteristicas,
        ]);

        return redirect()->route('operador.tipo-clases.index')
            ->with('success', 'Tipo de clase actualizado exitosamente.');
    }

    public function destroy(TipoClase $tipo_clase)
    {
        if ($tipo_clase->asientos()->count() > 0) {
            return redirect()->route('operador.tipo-clases.index')
                ->with('error', "No se puede eliminar '{$tipo_clase->nombre}' porque tiene asientos asociados.");
        }

        $tipo_clase->delete();

        return redirect()->route('operador.tipo-clases.index')
            ->with('success', 'Tipo de clase eliminado exitosamente.');
    }
}