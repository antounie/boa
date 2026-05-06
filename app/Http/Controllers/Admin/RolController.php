<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RolController extends Controller
{
    public function index(Request $request)
    {
        $query = Rol::withCount('usuarios');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        $roles = $query->orderBy('id', 'asc')->paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:roles',
            'descripcion' => 'nullable|string|max:200',
        ], [
            'nombre.unique' => 'Este nombre de rol ya existe.',
        ]);

        Rol::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol creado exitosamente.');
    }

    public function edit(Rol $rol)
    {
        return view('admin.roles.edit', compact('rol'));
    }

    public function update(Request $request, Rol $rol)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:50', Rule::unique('roles')->ignore($rol->id)],
            'descripcion' => 'nullable|string|max:200',
        ], [
            'nombre.unique' => 'Este nombre de rol ya existe.',
        ]);

        $rol->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol actualizado exitosamente.');
    }

    public function destroy(Rol $rol)
    {
        // Proteger roles base
        if (in_array($rol->nombre, ['Administrador', 'Operador', 'Cliente'])) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'No se pueden eliminar los roles base del sistema.');
        }

        // Verificar si tiene usuarios asignados
        if ($rol->usuarios()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', "No se puede eliminar el rol '{$rol->nombre}' porque tiene usuarios asignados.");
        }

        $rol->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol eliminado exitosamente.');
    }
}