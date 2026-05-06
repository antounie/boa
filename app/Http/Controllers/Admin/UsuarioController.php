<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::with('rol');

        // Búsqueda
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('username', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%")
                  ->orWhere('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido', 'like', "%{$buscar}%");
            });
        }

        $usuarios = $query->orderBy('id', 'desc')->paginate(10);
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Rol::all();
        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:usuarios',
            'email' => 'required|email|max:100|unique:usuarios',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/',
            'nombre' => 'required|string|max:80',
            'apellido' => 'required|string|max:80',
            'rol_id' => 'required|exists:roles,id',
        ], [
            'password.regex' => 'La contraseña debe tener al menos una mayúscula, una minúscula, un número y un carácter especial (@$!%*?&).',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'username.unique' => 'Este nombre de usuario ya está registrado.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
        ]);

        Usuario::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'estado' => 'Activo',
            'intentos_fallidos' => 0,
            'rol_id' => $request->rol_id,
        ]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(Usuario $usuario)
    {
        $roles = Rol::all();
        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('usuarios')->ignore($usuario->id)],
            'email' => ['required', 'email', 'max:100', Rule::unique('usuarios')->ignore($usuario->id)],
            'nombre' => 'required|string|max:80',
            'apellido' => 'required|string|max:80',
            'rol_id' => 'required|exists:roles,id',
        ]);

        $datos = [
            'username' => $request->username,
            'email' => $request->email,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'rol_id' => $request->rol_id,
        ];

        // Solo actualizar contraseña si se envió una nueva
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/',
            ], [
                'password.regex' => 'La contraseña debe tener al menos una mayúscula, una minúscula, un número y un carácter especial.',
            ]);
            $datos['password'] = Hash::make($request->password);
        }

        $usuario->update($datos);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function toggleStatus(Usuario $usuario)
    {
        if ($usuario->estado === 'Activo') {
            $usuario->update([
                'estado' => 'Bloqueado',
            ]);
            $mensaje = "Usuario {$usuario->username} bloqueado exitosamente.";
        } else {
            $usuario->update([
                'estado' => 'Activo',
                'intentos_fallidos' => 0,
                'veces_bloqueado' => 0,
                'bloqueado_hasta' => null,
            ]);
            $mensaje = "Usuario {$usuario->username} desbloqueado exitosamente.";
        }

        return redirect()->route('admin.usuarios.index')
            ->with('success', $mensaje);
    }
}