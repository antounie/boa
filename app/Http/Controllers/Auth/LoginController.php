<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::where('username', $request->username)->first();

        // Verificar si el usuario existe
        if (!$usuario) {
            return back()->withErrors([
                'username' => 'El usuario no existe.',
            ])->withInput();
        }

        // Verificar si está bloqueado permanentemente
        if ($usuario->estado === 'Bloqueado' && !$usuario->bloqueado_hasta) {
            return back()->withErrors([
                'username' => 'Su cuenta está bloqueada permanentemente. Contacte al Administrador.',
            ])->withInput();
        }

        // Verificar si está bloqueado temporalmente
        if ($usuario->bloqueado_hasta && now()->lt($usuario->bloqueado_hasta)) {
            $tiempoRestante = now()->diff($usuario->bloqueado_hasta);
            $mensaje = 'Cuenta bloqueada temporalmente. Intente de nuevo en ';

            if ($tiempoRestante->h > 0) {
                $mensaje .= $tiempoRestante->h . ' hora(s) y ' . $tiempoRestante->i . ' minuto(s).';
            } else {
                $mensaje .= $tiempoRestante->i . ' minuto(s) y ' . $tiempoRestante->s . ' segundo(s).';
            }

            return back()->withErrors([
                'username' => $mensaje,
            ])->withInput();
        }

        // Si el bloqueo temporal ya expiró, desbloquear
        if ($usuario->bloqueado_hasta && now()->gte($usuario->bloqueado_hasta)) {
            $usuario->update([
                'estado' => 'Activo',
                'intentos_fallidos' => 0,
                'bloqueado_hasta' => null,
            ]);
        }

        // Intentar autenticación
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            // Login exitoso: resetear todo
            $usuario->update([
                'intentos_fallidos' => 0,
                'veces_bloqueado' => 0,
                'bloqueado_hasta' => null,
            ]);
            $request->session()->regenerate();

            // Redirigir según rol
            return redirect()->route($this->obtenerDashboard($usuario));
        }

        // Login fallido: incrementar intentos
        $usuario->increment('intentos_fallidos');

        // Bloquear después de 3 intentos fallidos
        if ($usuario->intentos_fallidos >= 3) {
            $usuario->increment('veces_bloqueado');

            // Tiempo de bloqueo progresivo
            $minutos = match ($usuario->veces_bloqueado) {
                1 => 5,        // Primera vez: 5 minutos
                2 => 30,       // Segunda vez: 30 minutos
                3 => 60,       // Tercera vez: 1 hora
                4 => 180,      // Cuarta vez: 3 horas
                default => null, // Quinta vez o más: bloqueo permanente
            };

            if ($minutos) {
                $usuario->update([
                    'estado' => 'Bloqueado',
                    'bloqueado_hasta' => now()->addMinutes($minutos),
                ]);

                return back()->withErrors([
                    'username' => "Cuenta bloqueada por {$minutos} minutos por exceder 3 intentos fallidos.",
                ])->withInput();
            } else {
                $usuario->update([
                    'estado' => 'Bloqueado',
                    'bloqueado_hasta' => null,
                ]);

                return back()->withErrors([
                    'username' => 'Cuenta bloqueada permanentemente por múltiples intentos fallidos. Contacte al Administrador.',
                ])->withInput();
            }
        }

        return back()->withErrors([
            'password' => 'Contraseña incorrecta. Intentos restantes: ' . (3 - $usuario->intentos_fallidos),
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:80',
            'apellido' => 'required|string|max:80',
            'username' => 'required|string|max:50|unique:usuarios',
            'email' => 'required|email|max:100|unique:usuarios',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/',
            'documento_identidad' => 'required|string|max:20|unique:clientes',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'nullable|string|max:20',
        ], [
            'password.regex' => 'La contraseña debe tener al menos una mayúscula, una minúscula, un número y un carácter especial.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'username.unique' => 'Este nombre de usuario ya está registrado.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'documento_identidad.unique' => 'Este documento de identidad ya está registrado.',
        ]);

        // Crear usuario con rol Cliente (id: 3)
        $usuario = \App\Models\Usuario::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'estado' => 'Activo',
            'intentos_fallidos' => 0,
            'rol_id' => 3,
        ]);

        // Crear registro de cliente vinculado al usuario
        \App\Models\Cliente::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'documento_identidad' => $request->documento_identidad,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'usuario_id' => $usuario->id,
        ]);

        return redirect()->route('login')
            ->with('success', 'Registro exitoso. Ahora puede iniciar sesión.');
    }

    private function obtenerDashboard($usuario)
    {
        // Roles base
        if ($usuario->rol_id === 1) return 'admin.dashboard';
        if ($usuario->rol_id === 2) return 'operador.dashboard';
        if ($usuario->rol_id === 3) return 'cliente.dashboard';

        // Roles personalizados: determinar dashboard según permisos
        $permisos = \App\Models\RolPermiso::where('rol_id', $usuario->rol_id)
            ->where('acceso', true)
            ->pluck('tabla')
            ->toArray();

        $tablasAdmin = ['usuarios', 'roles', 'permisos', 'clientes', 'ventas', 'devoluciones', 'ingresos', 'egresos', 'reportes'];
        $tablasOperador = ['aeropuertos', 'tipo_clases', 'aeronaves', 'rutas', 'vuelos', 'programacion_vuelos', 'asientos', 'empleados', 'tripulaciones', 'salidas'];
        $tablasCliente = ['reservas', 'tickets'];

        // Verificar a qué panel tiene más acceso
        $accesoAdmin = count(array_intersect($permisos, $tablasAdmin));
        $accesoOperador = count(array_intersect($permisos, $tablasOperador));
        $accesoCliente = count(array_intersect($permisos, $tablasCliente));

        if ($accesoAdmin >= $accesoOperador && $accesoAdmin >= $accesoCliente) {
            return 'admin.dashboard';
        } elseif ($accesoOperador >= $accesoCliente) {
            return 'operador.dashboard';
        } else {
            return 'cliente.dashboard';
        }
    }
}