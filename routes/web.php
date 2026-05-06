<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\RolController;
use App\Http\Controllers\Admin\PermisoController;
use App\Http\Controllers\Operador\AeropuertoController;
use App\Http\Controllers\Operador\TipoClaseController;
use App\Http\Controllers\Operador\AeronaveController;
use App\Http\Controllers\Operador\RutaController;
use App\Http\Controllers\Operador\VueloController;
use App\Http\Controllers\Operador\ProgramacionVueloController;
use App\Http\Controllers\Operador\AsientoController;
use App\Http\Controllers\Operador\EmpleadoController;
use App\Http\Controllers\Operador\TripulacionController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Cliente\BuscarVueloController;
use App\Http\Controllers\Cliente\ReservaController;
use App\Http\Controllers\Operador\SalidaController;
use App\Http\Controllers\Admin\DevolucionController;
use App\Http\Controllers\Admin\IngresoController;
use App\Http\Controllers\Admin\EgresoController;
use App\Http\Controllers\Admin\VentaController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\BuscadorController;
use App\Http\Controllers\Cliente\PagoController;
use App\Http\Controllers\WelcomeController;

// Rutas públicas
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::match(['GET', 'POST'], '/buscar-vuelos', [WelcomeController::class, 'buscarPublico'])->name('welcome.buscar');
Route::get('/seleccionar/{programacion}', [WelcomeController::class, 'seleccionar'])->name('welcome.seleccionar');

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [LoginController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Rutas protegidas por autenticación
Route::middleware('auth')->group(function () {

    // Buscador de información (accesible para todos los roles)
    Route::get('/buscar', [BuscadorController::class, 'buscar'])->name('buscar.info');

    // ===== PANEL ADMINISTRADOR =====
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // CRUD Usuarios
    Route::middleware('rol:usuarios')->group(function () {
        Route::resource('/admin/usuarios', UsuarioController::class)->names('admin.usuarios');
        Route::patch('/admin/usuarios/{usuario}/toggle-status', [UsuarioController::class, 'toggleStatus'])->name('admin.usuarios.toggle-status');
    });

    // CRUD Roles
    Route::middleware('rol:roles')->group(function () {
        Route::resource('/admin/roles', RolController::class)->names('admin.roles')->parameters(['roles' => 'rol']);
    });

    // Gestión de Permisos
    Route::middleware('rol:permisos')->group(function () {
        Route::get('/admin/permisos', [PermisoController::class, 'index'])->name('admin.permisos.index');
        Route::get('/admin/permisos/{rol}/edit', [PermisoController::class, 'edit'])->name('admin.permisos.edit');
        Route::put('/admin/permisos/{rol}', [PermisoController::class, 'update'])->name('admin.permisos.update');
    });

    // CRUD Clientes
    Route::middleware('rol:clientes')->group(function () {
        Route::resource('/admin/clientes', ClienteController::class)->names('admin.clientes')->parameters(['clientes' => 'cliente'])->only(['index', 'show', 'edit', 'update']);
    });

    // Gestionar Ventas (consulta)
    Route::middleware('rol:ventas')->group(function () {
        Route::get('/admin/ventas', [VentaController::class, 'index'])->name('admin.ventas.index');
        Route::get('/admin/ventas/{venta}', [VentaController::class, 'show'])->name('admin.ventas.show');
    });

    // Gestionar Devoluciones
    Route::middleware('rol:devoluciones')->group(function () {
        Route::get('/admin/devoluciones', [DevolucionController::class, 'index'])->name('admin.devoluciones.index');
        Route::get('/admin/devoluciones/create', [DevolucionController::class, 'create'])->name('admin.devoluciones.create');
        Route::get('/admin/devoluciones/confirmar/{venta}', [DevolucionController::class, 'confirmar'])->name('admin.devoluciones.confirmar');
        Route::post('/admin/devoluciones', [DevolucionController::class, 'store'])->name('admin.devoluciones.store');
        Route::get('/admin/devoluciones/{devolucion}', [DevolucionController::class, 'show'])->name('admin.devoluciones.show');
    });

    // Gestionar Ingresos
    Route::middleware('rol:ingresos')->group(function () {
        Route::get('/admin/ingresos', [IngresoController::class, 'index'])->name('admin.ingresos.index');
        Route::get('/admin/ingresos/{ingreso}', [IngresoController::class, 'show'])->name('admin.ingresos.show');
    });

    // Gestionar Egresos
    Route::middleware('rol:egresos')->group(function () {
        Route::get('/admin/egresos', [EgresoController::class, 'index'])->name('admin.egresos.index');
        Route::get('/admin/egresos/{egreso}', [EgresoController::class, 'show'])->name('admin.egresos.show');
    });

    // Reportes
    Route::middleware('rol:reportes')->group(function () {
        Route::get('/admin/reportes', [ReporteController::class, 'index'])->name('admin.reportes.index');
        Route::get('/admin/reportes/vuelos', [ReporteController::class, 'vuelos'])->name('admin.reportes.vuelos');
        Route::get('/admin/reportes/vuelos/pdf', [ReporteController::class, 'vuelosPdf'])->name('admin.reportes.vuelos.pdf');
        Route::get('/admin/reportes/ventas', [ReporteController::class, 'ventas'])->name('admin.reportes.ventas');
        Route::get('/admin/reportes/ventas/pdf', [ReporteController::class, 'ventasPdf'])->name('admin.reportes.ventas.pdf');
        Route::get('/admin/reportes/ingresos', [ReporteController::class, 'ingresos'])->name('admin.reportes.ingresos');
        Route::get('/admin/reportes/ingresos/pdf', [ReporteController::class, 'ingresosPdf'])->name('admin.reportes.ingresos.pdf');
        Route::get('/admin/reportes/egresos', [ReporteController::class, 'egresos'])->name('admin.reportes.egresos');
        Route::get('/admin/reportes/egresos/pdf', [ReporteController::class, 'egresosPdf'])->name('admin.reportes.egresos.pdf');
        Route::post('/admin/reportes/enviar-correo', [ReporteController::class, 'enviarPorCorreo'])->name('admin.reportes.enviar-correo');
    });

    // ===== PANEL OPERADOR =====
    Route::get('/operador/dashboard', function () {
        return view('operador.dashboard');
    })->name('operador.dashboard');

    // CRUD Aeropuertos
    Route::middleware('rol:aeropuertos')->group(function () {
        Route::resource('/operador/aeropuertos', AeropuertoController::class)->names('operador.aeropuertos')->parameters(['aeropuertos' => 'aeropuerto']);
    });

    // CRUD Tipo de Clases
    Route::middleware('rol:tipo_clases')->group(function () {
        Route::resource('/operador/tipo-clases', TipoClaseController::class)->names('operador.tipo-clases')->parameters(['tipo-clases' => 'tipo_clase']);
    });

    // CRUD Aeronaves
    Route::middleware('rol:aeronaves')->group(function () {
        Route::resource('/operador/aeronaves', AeronaveController::class)->names('operador.aeronaves')->parameters(['aeronaves' => 'aeronave']);
        Route::patch('/operador/aeronaves/{aeronave}/toggle-status', [AeronaveController::class, 'toggleStatus'])->name('operador.aeronaves.toggle-status');
    });

    // CRUD Rutas
    Route::middleware('rol:rutas')->group(function () {
        Route::resource('/operador/rutas', RutaController::class)->names('operador.rutas')->parameters(['rutas' => 'ruta']);
    });

    // CRUD Vuelos
    Route::middleware('rol:vuelos')->group(function () {
        Route::resource('/operador/vuelos', VueloController::class)->names('operador.vuelos')->parameters(['vuelos' => 'vuelo']);
        Route::patch('/operador/vuelos/{vuelo}/toggle-status', [VueloController::class, 'toggleStatus'])->name('operador.vuelos.toggle-status');
    });

    // CRUD Programación de Vuelos
    Route::middleware('rol:programacion_vuelos')->group(function () {
        Route::resource('/operador/programaciones', ProgramacionVueloController::class)->names('operador.programaciones')->parameters(['programaciones' => 'programacion']);
    });

    // CRUD Asientos
    Route::middleware('rol:asientos')->group(function () {
        Route::post('/operador/asientos/generar-masivo', [AsientoController::class, 'generarMasivo'])->name('operador.asientos.generar-masivo');
        Route::delete('/operador/asientos/eliminar-todos', [AsientoController::class, 'eliminarTodos'])->name('operador.asientos.eliminar-todos');
        Route::resource('/operador/asientos', AsientoController::class)->names('operador.asientos')->parameters(['asientos' => 'asiento']);
    });

    // CRUD Empleados
    Route::middleware('rol:empleados')->group(function () {
        Route::patch('/operador/empleados/{empleado}/toggle-status', [EmpleadoController::class, 'toggleStatus'])->name('operador.empleados.toggle-status');
        Route::resource('/operador/empleados', EmpleadoController::class)->names('operador.empleados')->parameters(['empleados' => 'empleado']);
    });

    // CRUD Tripulación
    Route::middleware('rol:tripulaciones')->group(function () {
        Route::resource('/operador/tripulaciones', TripulacionController::class)->names('operador.tripulaciones')->parameters(['tripulaciones' => 'tripulacion'])->except(['edit', 'update', 'show']);
    });

    // Gestionar Salidas
    Route::middleware('rol:salidas')->group(function () {
        Route::get('/operador/salidas', [SalidaController::class, 'index'])->name('operador.salidas.index');
        Route::post('/operador/salidas', [SalidaController::class, 'store'])->name('operador.salidas.store');
        Route::get('/operador/salidas/{salida}', [SalidaController::class, 'show'])->name('operador.salidas.show');
    });

    // ===== PANEL CLIENTE =====
    Route::get('/cliente/dashboard', function () {
        return view('cliente.dashboard');
    })->name('cliente.dashboard');

    Route::middleware('rol:reservas,ventas,tickets')->group(function () {

        // Buscar vuelos
        Route::get('/cliente/buscar', [BuscarVueloController::class, 'index'])->name('cliente.buscar');
        Route::post('/cliente/buscar', [BuscarVueloController::class, 'buscar'])->name('cliente.buscar.resultados');
        Route::get('/cliente/seleccionar-asiento/{programacion}', [BuscarVueloController::class, 'seleccionarAsiento'])->name('cliente.seleccionar.asiento');

        // Reservas
        Route::post('/cliente/confirmar-reserva', [ReservaController::class, 'confirmarReserva'])->name('cliente.confirmar.reserva');
        Route::post('/cliente/procesar-reserva', [ReservaController::class, 'procesarReserva'])->name('cliente.procesar.reserva');
        Route::get('/cliente/mis-reservas', [ReservaController::class, 'misReservas'])->name('cliente.mis.reservas');

        // Compras
        Route::post('/cliente/compra-directa', [ReservaController::class, 'compraDirecta'])->name('cliente.compra.directa');
        Route::post('/cliente/procesar-compra', [ReservaController::class, 'procesarCompra'])->name('cliente.procesar.compra');
        Route::get('/cliente/mis-compras', [ReservaController::class, 'misCompras'])->name('cliente.mis.compras');

        // Tickets
        Route::get('/cliente/mis-tickets', [ReservaController::class, 'misTickets'])->name('cliente.mis.tickets');

        // Pagos con QR
        Route::post('/cliente/procesar-pago', [PagoController::class, 'procesarPago'])->name('cliente.procesar.pago');
        Route::get('/cliente/pago/callback/{identificador}', [PagoController::class, 'callback'])->name('cliente.pago.callback');
        Route::get('/cliente/pago/resultado/{identificador}', [PagoController::class, 'resultado'])->name('cliente.pago.resultado');
        Route::post('/cliente/pago/confirmar-simulacion', [PagoController::class, 'confirmarSimulacion'])->name('cliente.pago.confirmar-simulacion');
    });
});