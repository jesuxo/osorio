<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\TonerController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CwcuentasController;
use App\Http\Controllers\ImagenController;
use App\Http\Controllers\SaacxcwController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\SafactController;
use App\Http\Controllers\SasucursalController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\SerialesExtractorController;
use App\Http\Controllers\SaprodController;
use App\Http\Controllers\ChatConversationController;
use App\Http\Controllers\SavendController;
//use App\Http\Controllers\ShopController;
use App\Http\Controllers\IAController;
use App\Http\Controllers\CwtransferenciasController;
use App\Http\Controllers\ChoferController;
use App\Http\Controllers\CamionController;
use App\Http\Controllers\UserSucursalController;
use App\Http\Controllers\TipoGastoController;
use App\Http\Controllers\SadepoController;
use App\Http\Controllers\CwbancosController;
use App\Http\Controllers\ViajeController;
use App\Http\Controllers\SainstaController;
use App\Http\Controllers\SaclieController;
use App\Http\Controllers\SatarjController;
use App\Http\Controllers\EtapaController;
use App\Http\Controllers\SacompController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\ReporteGastosController;
use App\Http\Controllers\CwtokenController;
use App\Http\Controllers\CuentasCobrarController;
use App\Http\Controllers\ReporteProveedorController;
use App\Http\Controllers\SaprovController;
use App\Http\Controllers\Compras\CompraController;
use App\Http\Controllers\Seriales\SerialController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PagoProveedorController;
use App\Http\Controllers\Seriales\VerificacionSerialController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/index', [HomeController::class, 'index'])->name('index');

/*

Route::prefix('/')->name('shop.')->group(function () {

    // Página principal
    Route::get('/', [ShopController::class, 'index'])->name('index');

    // Detalle de producto
    Route::get('/producto/{codprod}', [ShopController::class, 'show'])->name('product');

    // Categoría
    Route::get('/categoria/{categoriaId}', [ShopController::class, 'category'])->name('category');

    // Búsqueda (página)
    Route::get('/buscar', [ShopController::class, 'index'])->name('search');

    // Búsqueda AJAX para autocompletado
    Route::get('/buscar-ajax', [ShopController::class, 'searchAjax'])->name('search.ajax');

});
*/
use App\Http\Controllers\PublicoSeguimientoController;

// Rutas públicas para el chofer (sin autenticación)
Route::prefix('publico/seguimiento')->name('publico.seguimiento.')->group(function () {
    Route::get('{token}', [PublicoSeguimientoController::class, 'index'])->name('index');
    Route::post('{token}/ubicacion', [PublicoSeguimientoController::class, 'actualizarUbicacion'])->name('ubicacion');
    Route::post('{token}/etapa/{etapaId}/estado', [PublicoSeguimientoController::class, 'cambiarEstadoEtapa'])->name('etapa.estado');
    Route::get('{token}/info', [PublicoSeguimientoController::class, 'getInfoViaje'])->name('info');
});

/* Auth Route::get('signup', 'App\Http\Controllers\Auth\RegisterController@signup')->name('signup');
*/
Route::get('/login', function () {
    return view('auth.login');
});

Route::get('index/{locale}', [HomeController::class, 'lang']);

\Illuminate\Support\Facades\Auth::routes();

   // Route::post('login', 'Auth\LoginController@login')->name('login');
   // Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::post('register', 'Auth\RegisterController@register')->name('register');
   // Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
   // Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');


\Illuminate\Support\Facades\Auth::routes(['verify' => true]);

Route::group(['prefix' => 'error'], function(){
    Route::get('404', function () { return view('error.404'); });
    Route::get('500', function () { return view('error.500'); });
});



use App\Http\Controllers\ComercialDashboardController;

// Rutas para el dashboard del comercial
Route::middleware(['auth', 'redirect.to.comercial'])->group(function () {
    Route::get('/comercial/{comercialId}/dashboard', [ComercialDashboardController::class, 'index'])
        ->name('comercial.dashboard');

    // Ruta para usuarios sin asignación
    Route::get('/sin-asignacion', function () {
        return view('errors.sin-asignacion');
    })->name('sin.asignacion');
});


Route::middleware(['auth'])->group(function () {

    Route::get('/pagos-proveedores/reporte-facturas', [PagoProveedorController::class, 'reporteFacturas'])->name('reporte-facturas');


    // Ruta para cambiar de comercial
    Route::get('/cambiarcomercial/{comercialId}', [ComercialDashboardController::class, 'cambiarComercial'])
        ->name('comercial.cambiar');

    // Ruta para obtener comerciales disponibles (API)
    Route::get('/comerciales/disponibles', [ComercialDashboardController::class, 'getComercialesDisponibles'])
        ->name('comerciales.disponibles');
});

Route::middleware(['auth'])->group(function () {


    Route::get('/comprasseriales', [ComprasController::class, 'index'])->name('compras.seriales');
    Route::post('/comprasextraer-seriales', [ComprasController::class, 'extraerSeriales'])->name('compras.extraer-seriales');

    Route::get('extractor-seriales', [SerialesExtractorController::class, 'index'])->name('seriales.index');
    Route::post('extractor-seriales', [SerialesExtractorController::class, 'extract'])->name('seriales.extract');

    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/conversations', [ChatConversationController::class, 'index'])->name('index');
        Route::post('/conversations/{id}', [ChatConversationController::class, 'show'])->name('show');
        Route::put('/conversations/{id}/status', [ChatConversationController::class, 'updateStatus'])->name('update-status');
        Route::delete('/conversations/{id}', [ChatConversationController::class, 'destroy'])->name('destroy');
        Route::get('/export', [ChatConversationController::class, 'export'])->name('export');
        Route::get('/stats', [ChatConversationController::class, 'stats'])->name('stats');
    });

    Route::resource('iaknowledge', IAController::class);
    Route::get('iaknowledge-search', [IAController::class, 'search'])->name('iaknowledge.search');

// routes/web.php

    Route::prefix('pagos-proveedores')->name('pagos-proveedores.')->middleware(['auth'])->group(function () {
        Route::get('/', [PagoProveedorController::class, 'index'])->name('index');
        Route::get('/create', [PagoProveedorController::class, 'create'])->name('create');

        Route::get('/detalle-facturadas', [PagoProveedorController::class, 'detalleFacturadas'])->name('detallefacturadas');


        Route::post('/', [PagoProveedorController::class, 'store'])->name('store');
        Route::get('/{id}', [PagoProveedorController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PagoProveedorController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PagoProveedorController::class, 'update'])->name('update');
        Route::delete('/{id}', [PagoProveedorController::class, 'destroy'])->name('destroy');


        Route::get('/productos/{id}/precio', [PagoProveedorController::class, 'getPrecio'])->name('getPrecio');

        Route::get('/{id}/productos', [PagoProveedorController::class, 'getProductos'])->name('productos.get');
        Route::put('/{id}/productos', [PagoProveedorController::class, 'updateProductos'])->name('productos.update');

        Route::match(['GET', 'POST'], '/resumen-general', [PagoProveedorController::class, 'resumenGeneral'])->name('resumen-general');
        Route::get('/exportar-resumen', [PagoProveedorController::class, 'exportarResumen'])->name('exportar-resumen');

        // Productos
        Route::get('/{id}/productos/form', [PagoProveedorController::class, 'productosForm'])->name('productos.form');
        Route::post('/{id}/productos', [PagoProveedorController::class, 'agregarProducto'])->name('productos.agregar');
        Route::delete('/{id}/productos/{detalleId}', [PagoProveedorController::class, 'eliminarProducto'])->name('productos.eliminar');

        // Comprobantes
        Route::get('/{id}/comprobantes', [PagoProveedorController::class, 'getComprobantes'])->name('comprobantes.index');
        Route::get('/{id}/comprobantes/form', [PagoProveedorController::class, 'comprobantesForm'])->name('comprobantes.form');
        Route::post('/{id}/comprobantes', [PagoProveedorController::class, 'agregarComprobante'])->name('comprobantes.agregar');
        Route::delete('/{id}/comprobantes/{comprobanteId}', [PagoProveedorController::class, 'eliminarComprobante'])->name('comprobantes.eliminar');

        // Despachos
        Route::get('/{id}/despachos', [PagoProveedorController::class, 'getDespachos'])->name('despachos.index');
        Route::get('/{id}/despachos/form', [PagoProveedorController::class, 'despachosForm'])->name('despachos.form');
        Route::post('/{id}/despachos', [PagoProveedorController::class, 'registrarDespacho'])->name('despachos.registrar');
        Route::delete('/{id}/despachos/{despachoId}', [PagoProveedorController::class, 'eliminarDespacho'])->name('despachos.eliminar');

        // Aprobación
        Route::post('/{id}/asignar-aprobacion', [PagoProveedorController::class, 'asignarAprobacion'])->name('asignar-aprobacion');
        Route::post('/{id}/editar-aprobacion', [PagoProveedorController::class, 'editarAprobacion'])->name('editar-aprobacion');

        Route::get('/{id}/facturas', [PagoProveedorController::class, 'getFacturas'])->name('facturas.index');
        Route::post('/detalles/{detalleId}/facturas', [PagoProveedorController::class, 'agregarFactura'])->name('facturas.agregar');
        Route::delete('/facturas/{facturaId}', [PagoProveedorController::class, 'eliminarFactura'])->name('facturas.eliminar');


        Route::get('/exportar-facturas', [PagoProveedorController::class, 'exportarReporteFacturas'])->name('exportar-facturas');
    });


    Route::get('/motosporfecha', [PagoProveedorController::class, 'motosPorFecha'])->name('motosPorFecha');

// Búsqueda de productos
    Route::get('/buscar-productos', [PagoProveedorController::class, 'buscarProductos'])->name('buscar.productos');

    Route::prefix('cxctransporte')->name('cxctransporte.')->group(function () {
        Route::get('/', [CuentasCobrarController::class, 'index'])->name('index');
        Route::get('cliente/{codclie}/historial', [CuentasCobrarController::class, 'historialCliente'])->name('cliente.historial');
        Route::post('facturar-moto/{id}', [CuentasCobrarController::class, 'facturarMoto'])->name('facturar.moto');
        Route::post('facturar-cliente/{codclie}', [CuentasCobrarController::class, 'facturarCliente'])->name('facturar.cliente');
        Route::post('revertir-factura/{id}', [CuentasCobrarController::class, 'revertirFactura'])->name('revertir');
        Route::get('cliente/{codclie}/resumen', [CuentasCobrarController::class, 'resumenCliente'])->name('cliente.resumen');
    });

    Route::prefix('usersucursal')->group(function () {
        Route::get('/', [UserSucursalController::class, 'index'])->name('usersucursal.index');
        Route::get('/usuarios', [UserSucursalController::class, 'getUsersConSucursales'])->name('usersucursal.usuarios');
        Route::get('/sucursales', [UserSucursalController::class, 'getAllSucursales'])->name('usersucursal.sucursales');
        Route::get('/sucursales-asignadas/{userId}', [UserSucursalController::class, 'getSucursalesAsignadasPorUsuario']);
        Route::get('/usuarios-por-sucursal/{sucursalId}', [UserSucursalController::class, 'getUsuariosPorSucursal']);
        Route::post('/asignar', [UserSucursalController::class, 'asignarSucursal'])->name('usersucursal.asignar');
        Route::post('/quitar', [UserSucursalController::class, 'quitarSucursal'])->name('usersucursal.quitar');
    });

    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('proveedor', [ReporteProveedorController::class, 'index'])->name('proveedor.index');
        Route::post('proveedor/marcar-pagado/{id}', [ReporteProveedorController::class, 'marcarPagado'])->name('proveedor.marcar-pagado');
        Route::get('proveedor/resumen', [ReporteProveedorController::class, 'resumen'])->name('proveedor.resumen');
        Route::get('proveedor/exportar', [ReporteProveedorController::class, 'exportarExcel'])->name('proveedor.exportar');
    });

    Route::get('viajes/{id}/ver', [ViajeController::class, 'verViaje'])->name('viajes.ver');

    Route::post('viajes/{id}/motos/{motoId}/facturar', [ViajeController::class, 'facturarMoto'])->name('viajes.motos.facturar');
    Route::post('viajes/{id}/cliente/{codclie}/facturar', [ViajeController::class, 'facturarCliente'])->name('viajes.cliente.facturar');

        Route::get('reportesgastos', [ReporteGastosController::class, 'index'])->name('reportesgastos.index');
        Route::get('reportesgastos/datos', [ReporteGastosController::class, 'datos'])->name('reportesgastos.datos');
        Route::get('reportesgastos/exportar/excel', [ReporteGastosController::class, 'exportarExcel'])->name('reportesgastos.exportar.excel');
        Route::get('reportesgastos/exportar/pdf', [ReporteGastosController::class, 'exportarPdf'])->name('reportesgastos.exportar.pdf');

    Route::post('viajes/{id}/gastos', [ViajeController::class, 'agregarGasto'])->name('viajes.gastos.store');
    Route::put('viajes/{id}/gastos/{gastoId}', [ViajeController::class, 'updateGasto'])->name('viajes.gastos.update');
    Route::delete('viajes/{id}/gastos/{gastoId}', [ViajeController::class, 'deleteGasto'])->name('viajes.gastos.delete');
    Route::get('viajes/{id}/gastos/admin', [ViajeController::class, 'adminGastos'])->name('viajes.gastos.admin');
    Route::post('viajes/{id}/anticipo', [ViajeController::class, 'guardarAnticipo'])->name('viajes.anticipo');

    Route::get('generar-token-seguimiento/{id}', [ViajeController::class, 'generarTokenSeguimiento'])->name('generar.token.seguimiento');

    // Rutas para tipos de gasto (catálogo)
    Route::resource('tipo-gastos', TipoGastoController::class);
    Route::patch('tipo-gastos/{tipoGasto}/toggle-activo', [TipoGastoController::class, 'toggleActivo'])->name('tipo-gastos.toggle-activo');
    Route::get('api/tipo-gastos/lista', [TipoGastoController::class, 'lista'])->name('api.tipo-gastos.lista');

    Route::get('viajes/{id}/puntos-seguimiento', [ViajeController::class, 'puntosSeguimiento'])->name('viajes.puntos-seguimiento');

// Rutas para gastos
    Route::resource('gastos', GastoController::class);
    Route::get('gastos/{gasto}/download-comprobante', [GastoController::class, 'downloadComprobante'])->name('gastos.download-comprobante');

    Route::resource('viajes', ViajeController::class);
    Route::get('viajes/{id}/motos/form', [ViajeController::class, 'formMotos'])->name('viajes.motos.form');
    Route::match(['get','post'],'viajes/{id}/motos', [ViajeController::class, 'agregarMotos'])->name('viajes.motos.store');
    Route::delete('viajes/{id}/motos/{moto}', [ViajeController::class, 'eliminarMoto'])->name('viajes.motos.destroy');


    // Rutas para gastos en viajes
    //Route::get('viajes/{id}/gastos/form', [ViajeController::class, 'formGastos'])->name('viajes.gastos.form');
    //Route::post('viajes/{id}/gastos', [ViajeController::class, 'agregarGasto'])->name('viajes.gastos.store');

    // Rutas para etapas
    Route::get('viajes/{id}/etapas/admin', [ViajeController::class, 'adminEtapas'])->name('viajes.etapas.admin');
    Route::get('viajes/{id}/etapas', [ViajeController::class, 'gestionarEtapas'])->name('viajes.etapas');
    Route::get('api/viajes/{id}/etapas', [EtapaController::class, 'index'])->name('api.etapas.index');
    Route::post('viajes/{id}/etapas', [EtapaController::class, 'store'])->name('viajes.etapas.store');
    Route::put('etapas/{id}', [EtapaController::class, 'update'])->name('etapas.update');
    Route::patch('etapas/{id}/estado', [EtapaController::class, 'cambiarEstado'])->name('etapas.estado');
    Route::delete('etapas/{id}', [EtapaController::class, 'destroy'])->name('etapas.destroy');
    Route::post('etapas/{id}/evidencia', [EtapaController::class, 'uploadEvidencia'])->name('etapas.evidencia');
    Route::post('viajes/{id}/etapas/reordenar', [EtapaController::class, 'reordenar'])->name('etapas.reordenar');

    Route::get('viajes/{id}/motos/admin', [ViajeController::class, 'adminMotos'])->name('viajes.motos.admin');
    Route::put('viajes/{id}/motos/{motoId}', [ViajeController::class, 'updateMoto'])->name('viajes.motos.update');

    Route::get('test-seguimiento/{id}', [ViajeController::class, 'testSeguimiento']);

    // Rutas para seguimiento
    Route::get('viajes/{id}/seguimiento', [ViajeController::class, 'seguimiento'])->name('viajes.seguimiento');
    Route::post('viajes/{id}/seguimiento', [SeguimientoController::class, 'store'])->name('viajes.seguimiento.store');
    Route::get('viajes/{id}/ubicacion-actual', [SeguimientoController::class, 'ubicacionActual'])->name('viajes.ubicacion');

    // ========== RUTAS PARA ACCIONES RÁPIDAS ==========

    // Cambiar estado del viaje
    Route::patch('viajes/{id}/cambiar-estado', [ViajeController::class, 'cambiarEstado'])->name('viajes.cambiar-estado');

    // Completar viaje
    Route::post('viajes/{id}/completar', [ViajeController::class, 'completar'])->name('viajes.completar');

    // Cancelar viaje
    Route::post('viajes/{id}/cancelar', [ViajeController::class, 'cancelar'])->name('viajes.cancelar');

    // ========== RUTAS PARA REPORTES Y ESTADÍSTICAS ==========

    // Estadísticas generales (para el dashboard)
    Route::get('viajes/estadisticas/resumen', [ViajeController::class, 'estadisticas'])->name('viajes.estadisticas');

    // Reporte de viajes por período
    Route::get('viajes/reporte/periodo', [ViajeController::class, 'reportePeriodo'])->name('viajes.reporte.periodo');

    // Reporte de rentabilidad
    Route::get('viajes/reporte/rentabilidad', [ViajeController::class, 'reporteRentabilidad'])->name('viajes.reporte.rentabilidad');

    // Exportar viajes a Excel/PDF
    Route::get('viajes/exportar/excel', [ViajeController::class, 'exportarExcel'])->name('viajes.exportar.excel');
    Route::get('viajes/exportar/pdf', [ViajeController::class, 'exportarPdf'])->name('viajes.exportar.pdf');

    // ========== RUTAS PARA BÚSQUEDAS ESPECÍFICAS ==========

    // Búsqueda rápida (para autocomplete)
    Route::get('viajes/buscar/rapido', [ViajeController::class, 'buscarRapido'])->name('viajes.buscar.rapido');

    // Búsqueda por chofer
    Route::get('viajes/por-chofer/{chofer}', [ViajeController::class, 'porChofer'])->name('viajes.por-chofer');

    // Búsqueda por camión
    Route::get('viajes/por-camion/{camion}', [ViajeController::class, 'porCamion'])->name('viajes.por-camion');

    // Búsqueda por fecha
    Route::get('viajes/por-fecha/{fecha}', [ViajeController::class, 'porFecha'])->name('viajes.por-fecha');
/*
    Route::get('viajes/{id}/detalles', [ViajeController::class, 'apiDetalles'])->name('viajes.detalles');
    Route::get('viajes/{id}/etapas', [ViajeController::class, 'apiEtapas'])->name('viajes.etapas');
    Route::get('viajes/{id}/gastos', [ViajeController::class, 'apiGastos'])->name('viajes.gastos');
    Route::get('viajes/{id}/motos', [ViajeController::class, 'apiMotos'])->name('viajes.motos');*/

    // Endpoint para seguimiento en tiempo real (para mapa)
    Route::get('seguimiento/{id}/ultima-ubicacion', [SeguimientoController::class, 'apiUltimaUbicacion'])->name('seguimiento.ultima');
    Route::get('seguimiento/{id}/historial', [SeguimientoController::class, 'apiHistorial'])->name('seguimiento.historial');
    Route::get('seguimiento/{id}/resumen', [SeguimientoController::class, 'resumenRuta'])->name('api.seguimiento.resumen');
    Route::post('seguimiento/{id}', [SeguimientoController::class, 'store'])->name('api.seguimiento.store');


    Route::get('/buscar-modelos-moto', [SainstaController::class, 'buscarModelos'])->name('buscar.modelos.moto');


    Route::get('/buscarproducto/{codprod}/{comercial}', [SaprodController::class, 'buscarproductoget'])->name('buscarproductoget');

    Route::get('/verpermisos/{id?}', [PermissionController::class, 'showForm'])->name('permissions.assign');
    Route::post('/verpermisos', [PermissionController::class, 'assign']);
    Route::post('/create/permissions', [PermissionController::class, 'create'])->name('permissions.create');
    Route::get('/revoke/{user}/{permiso}', [PermissionController::class, 'revokePermission'])->name('permissions.revoke');

    Route::resource('instpago', SatarjController::class);
    Route::controller(SatarjController::class)->group(function () {
        Route::get('satarj/json', 'json');
    });

    Route::match(['get','post'],'/reporte/instpagobs',      [App\Http\Controllers\SatarjController::class, 'instpagobs'])->name('instpagobs');
    Route::match(['get','post'],'/reporte/instpagodolares', [App\Http\Controllers\SatarjController::class, 'instpagodolares'])->name('instpagodolares');

    Route::resource('vendedores', SavendController::class);
    Route::controller(SavendController::class)->group(function () {
        Route::get('savend/json', 'json');
    });

    Route::resource('compras', SacompController::class);
    Route::controller(SacompController::class)->group(function () {
        Route::get('compra/{id}', 'documentoSacomp');
        Route::get('compra/seriales/{id}', 'documentoSerialesSacomp');
        Route::post('compra/cambiar-status', 'cambiarStatus'); // Nueva ruta
    });

    Route::resource('instancias', SainstaController::class);
    Route::controller(SainstaController::class)->group(function () {
        Route::get('sainsta/json', 'json');
        Route::post('/sainsta/check/lastprod', 'lastprod');
    });

    Route::get('/productos/plantilla', [SaprodController::class, 'descargarPlantilla'])->name('productos.plantilla');
    Route::post('/productos/importarcrear', [SaprodController::class, 'importarcrear'])->name('productos.importarcrear');
    Route::post('saprod/update', [SaprodController::class, 'updateSaprodData']);
    Route::get('saprod/export/{codalte}', [SaprodController::class, 'saprodexport']);
    Route::post('/productos/validar-codigo', [SaprodController::class, 'validarCodigo'])->name('productos.validar-codigo');


    Route::resource('proveedores', SaprovController::class);
    Route::controller(SaprovController::class)->group(function () {
        Route::match(['get','post'],'/proveedores/{codprov?}/{tab?}', 'index')->name('proveedores.index');
        Route::post('proveedoresupdate', 'proveedoresupdate')->name('proveedoresupdate');
        Route::post('/proveedores/marcar-pagado', 'marcarPagado')->name('proveedores.marcar-pagado');
    });

    Route::get('proveedores/pagos/pendientes', [SaprovController::class, 'pagosPendientes'])->name('proveedores.pagos-pendientes');
    Route::get('proveedores-json', [SaprovController::class, 'json'])->name('proveedores.json');

    Route::resource('cliente', SaclieController::class);
    Route::controller(SaclieController::class)->group(function () {

        Route::match(['get','post'],'/clientes/{codclie?}/{tab?}', 'index')->name('buscarclientes');

        Route::match(['get','post'],'/financiamientos', 'financiamientos')->name('financiamientos');

        Route::match(['get','post'],'/verInstPago',     'verInstPago')    ->name('verInstPago');
        Route::post('/itemCxc', 'itemCxc')->name('itemCxc');

        Route::post('updatecliente', 'updatecliente')->name('updatecliente');
        Route::post('buscarclienteajax', 'buscarclienteajax')->name('buscarclienteajax');
        Route::post('newfinanciamiento', 'newfinanciamiento')->name('cliente.newfinanciamiento');
        Route::get('/financiamiento/view/{id}', 'financiamientoview');
        Route::get('/reserva/view/{id}', 'reservaview');

        Route::post('newletra', 'newletra')->name('cliente.newletra');
        Route::get('/letra/view/{codclie}/{id}', 'letraview');
        Route::get('/letra/delete/{codclie}/{id}', 'letradelete');

        Route::post('newpagare', 'newpagare')->name('cliente.newpagare');
        Route::get('/pagare/view/{codclie}/{id}', 'pagareview');
        Route::get('/pagare/delete/{codclie}/{id}', 'pagaredelete');

    });

    Route::resource('bancos', CwbancosController::class);

    Route::controller(CwbancosController::class)->group(function () {
        Route::match(['get','post'],'/simulacionFinanciamiento', 'simulacionFinanciamiento')->name('simulacionFinanciamiento');

        Route::get('/bancos/padrebancos/{padrebancos}', 'index')->name('bancos.padrebancos');
        Route::match(['get','post'],'/verbanco', 'verbanco')->name('verbanco');
        Route::get('/eliminarTr/{id}/{fkbanco}', 'eliminarTr')->name('bancos.eliminarTr');
        Route::get('/montotr', 'montotr')->name('montotr');
        Route::get('/imprimirReciboBanco/{id}', 'imprimirReciboBanco')->name('bancos.imprimirReciboBanco');
        Route::get('/verbanco/impresion', 'printBancoFecha')->name('bancos.printBancoFecha');
    });


    Route::resource('productos', SaprodController::class);
    Route::controller(SaprodController::class)->group(function () {
        Route::get('saprod/json', 'json');
        Route::post('saprod/check/codprod/{codprod}', 'checkcodprod');
        Route::post('saprod/home/busqueda', 'busquedaHomeProd');

        Route::match(['get','post'],'newexistencias', 'newexistencias');

        Route::get( '/existencia/motos', 'existenciasMotos');
        Route::post( '/existencia/motos/modelos', 'existenciasMotosModelos');
        Route::get( '/existencia/motos/modelos/{inspadre}', 'existenciasMotosModelos');

        Route::get( '/existencia/motos/consignacion', 'existenciasMotosConsignacion');
        Route::post( '/existencia/motos/modelos/consignacion', 'existenciasMotosModelosConsignacion');
        Route::get( '/existencia/motos/modelos/consignacion/{inspadre}', 'existenciasMotosModelosConsignacion');

        Route::post('reporte/newexisten/php', 'newexistenciasphp');
        Route::match(['get','post'],'/operaciones/{codprod?}', 'index');
        Route::match(['get','post'],'existencias', 'existencias');
        Route::post('reporte/existen/php', 'existenciasphp');
        Route::post('saprod/upload', 'upload');
        Route::match(['get','post'],'ventas/productos/sucursales', 'productossucursales');
        Route::post('saprod/viewprodinstsanciascodalte', 'viewprodinstsanciascodalte');
        Route::post('saprod/newviewprodinstsanciascodalte', 'newviewprodinstsanciascodalte');
        Route::match(['get','post'],'mermas/sucursales', 'mermassucursales');
        //Route::get('operaciones/{codprod}/{serial}', 'operacionesSerial');
    });


// Rutas de compras


    Route::prefix('compras')->name('compras.')->group(function () {
        Route::match(['get', 'post'], '/reporte', [CompraController::class, 'reporte'])->name('reporte');
        Route::get('/reporte/descargados', [CompraController::class, 'reporteDescargados'])->name('reporte-descargados');
        Route::match(['get', 'post'], '/reporte', [CompraController::class, 'reporte'])->name('reporte');
        Route::post('/procesar-documento', [CompraController::class, 'procesarDocumento'])->name('procesar-documento');
        Route::get('/documento/{id}', [CompraController::class, 'verDocumento'])->name('documento');
        Route::get('/seriales/{id}', [CompraController::class, 'verSeriales'])->name('seriales');
        Route::post('/cambiar-status', [CompraController::class, 'cambiarStatus'])->name('cambiar-status');
    });

// Rutas de seriales
    Route::prefix('seriales')->name('seriales.')->group(function () {

        Route::get('/historial', [SerialController::class, 'historial'])->name('historial');

        Route::post('/buscar-ajax', [SerialController::class, 'buscarSeriales'])->name('buscar.ajax');

        Route::post('/historial-ajax', [SerialController::class, 'getHistorialAjax'])->name('historial.ajax');

        // Rutas existentes
        Route::get('/historial-json/{codprod}/{serial}', [SerialController::class, 'historialJson'])->name('historial.json');
        Route::get('/buscar', [SerialController::class, 'buscar'])->name('buscar');
        Route::get('/estadisticas-compra/{compraId}', [SerialController::class, 'estadisticasCompra'])->name('estadisticas-compra');
        Route::get('/{id}/comentario', [VerificacionSerialController::class, 'getComentario'])->name('get-comentario');
        Route::post('/{id}/verificar', [VerificacionSerialController::class, 'verificar'])->name('verificar');
        Route::get('/estadisticas-compra/{compraId}', [VerificacionSerialController::class, 'estadisticasVerificacion'])->name('estadisticas-verificacion');
    });

    Route::get('/seriales/{id}/data', [VerificacionSerialController::class, 'getSerial']);
    Route::get('/facturas/detalle-vista/{tipofac}/{numerod}/{fksucu}', [App\Http\Controllers\Facturas\FacturaController::class, 'detalleVista']);
    Route::get('/facturas/detalle/{tipo}/{numero}/{sucursal}', [App\Http\Controllers\Facturas\FacturaController::class, 'detalle']);

// Mantener compatibilidad con rutas antiguas
    Route::get('operaciones/{codprod}/{serial}', [SerialController::class, 'historial']);
    Route::match(['get','post'],'/reporte/compra', [CompraController::class, 'reporte'])->name('reportecompra');


    Route::get('/bancos/{id}/moneda', function($id) {
        $banco = \App\Models\Cwbancos::find($id);
        if ($banco->bs)      return response()->json(['moneda' => 'BS']);
        if ($banco->dolares) return response()->json(['moneda' => 'USD']);
        if ($banco->pesos)   return response()->json(['moneda' => 'COP']);
        return response()->json(['moneda' => 'BS']);
    });

    Route::post('/sascursal/bancos', [SasucursalController::class, 'getBancos'])->name('sucursal.bancos');

    Route::resource('transferencias', CwtransferenciasController::class);
    Route::controller( CwtransferenciasController::class)->group(function () {

        Route::match(['get','post'],'reporte/transferencias', 'reportetransferencias')->name('reportetransferencias');
        Route::post('transferencias/json/{busquedatransf}/{status}/{fechas}', 'json');
        Route::get('transferencias/status/{status}', 'filtrarstatus');
        Route::post('transferencias/pendienteAgain', 'pendienteAgain');
        Route::post('transferencias/DescargarAgain', 'DescargarAgain');
        Route::post('transferencias/verificar', 'verificar');
        Route::match(['get','post'],'transferencia/informacion', 'informacion');
        Route::post('/transferencias/verificar-tiempo-real', 'verificarTiempoReal')->name('transferencias.verificar.tiemporeal');
        Route::post('/transferencias/buscar-numeros-similares', 'buscarNumerosSimilares')->name('transferencias.buscar.numeros');
    });

    Route::get('transferencias/exportar/excel', [CwtransferenciasController::class, 'exportarExcel'])->name('transferencias.exportar.excel');
    Route::get('transferencias/exportar/estadisticas', [CwtransferenciasController::class, 'exportarEstadisticas'])->name('transferencias.exportar.estadisticas');
    Route::get('/transferencias/data', [CwtransferenciasController::class, 'getTransferenciasData'])->name('transferencias.data');
    Route::get('imagen/transferencia/{id}', [ImagenController::class, 'transferencia'])->name('imagen.transferencia');
    Route::get('transferencias/categorias/{q}', [CwtransferenciasController::class, 'getCategorias'])->name('transferencias.categorias');

    Route::match(['get','post'],'/resumenVentas', [HomeController::class, 'resumenVentas'])->name('resumenVentas');

    Route::resource('tokens',  CwtokenController::class);
    Route::prefix('tokens')->group(function () {
        Route::get('/', [CwtokenController::class, 'reportetokens'])->name('reportetokens');
        Route::post('/', [CwtokenController::class, 'reportetokens']);
        Route::post('/store', [CwtokenController::class, 'store'])->name('tokens.store');
        Route::post('/update', [CwtokenController::class, 'tokenupdate'])->name('token.update');
        Route::post('/generar-auto', [CwtokenController::class, 'generarTokenAuto']);
        Route::post('/new', [CwtokenController::class, 'newtoken']);
        Route::get('/export', [CwtokenController::class, 'export']);
        Route::delete('/{id}', [CwtokenController::class, 'destroy']);
        Route::post('/bulk-delete', [CwtokenController::class, 'bulkDelete']);
    });

    Route::resource('depositos', SadepoController::class);
    Route::controller(SadepoController::class)->group(function () {
        Route::get('sadepo/json', 'json');
    });

    Route::controller(SaacxcwController::class)->group(callback: function () {
        Route::match(['get','post'],'cxc/{id?}', 'saacxcw');
        Route::post('/cxclist', 'cxclist');
    });

    Route::controller(CwcuentasController::class)->group(function () {
        Route::post('buscarcuentaajax', 'buscarcuentaajax')->name('buscarcuentaajax');
        Route::get('cuentassucu', 'cuentassucu')->name('cuentassucu');
    });

    Route::resource('camiones', CamionController::class);
    Route::patch('camiones/{camion}/toggle-activo', [CamionController::class, 'toggleActivo'])->name('camiones.toggle-activo');
    Route::patch('camiones/{id}/restore', [CamionController::class, 'restore'])->name('camiones.restore');
    Route::delete('camiones/{id}/force-delete', [CamionController::class, 'forceDelete'])->name('camiones.force-delete');


    Route::resource('choferes', ChoferController::class);
    Route::patch('choferes/{chofer}/toggle-activo', [ChoferController::class, 'toggleActivo'])->name('choferes.toggle-activo');
    Route::patch('choferes/{id}/restore', [ChoferController::class, 'restore'])->name('choferes.restore');
    Route::delete('choferes/{id}/force-delete', [ChoferController::class, 'forceDelete'])->name('choferes.force-delete');

    Route::get('/reporte/ventas-vendedor', [HomeController::class, 'reporteVentasVendedor'])->name('reporte.ventas.vendedor');
    Route::post('/reporte/ventas-vendedor', [HomeController::class, 'reporteVentasVendedor']);


    Route::controller(SafactController::class)->group(function () {
        Route::get('doc/{tipofac}/{numerod}/{fksucu}', 'documentoSafact');
        Route::post('openDoc', 'documentoAjax');
    });

    Route::get('/buscar-factura/{tipo}/{numero}', [SafactController::class, 'buscarFacturaPorNumero'])
        ->name('documento.buscar.ajax');

    Route::get('/buscar-factura/{tipo}/{numero}', [SafactController::class, 'buscarFacturaPorNumero'])
        ->name('documento.buscar.ajax');

    Route::resource('instpago', SatarjController::class);
    Route::controller(SatarjController::class)->group(function () {
        Route::get('satarj/json', 'json');
    });

    Route::get('/fechasistema/{d}/{m}/{y}', [HomeController::class, 'fechasistema'])->name('fechasistema');

    Route::match(['get','post'],'/reporte/instpagobs',      [SatarjController::class, 'instpagobs'])->name('instpagobs');
    Route::match(['get','post'],'/reporte/instpagodolares', [SatarjController::class, 'instpagodolares'])->name('instpagodolares');

    Route::match(['get','post'],'/reporte/compra/motos',[HomeController::class, 'reporteCompraMotos'])->name('reporteCompraMotos');
    Route::match(['get','post'],'/reporte/motos',       [HomeController::class, 'reportemotos'])      ->name('reportemotos');
    Route::match(['get','post'],'/reporte/lubricantes', [HomeController::class, 'reportelubricantes'])->name('reportelubricantes');
    Route::match(['get','post'],'/reporte/repuestos',   [HomeController::class, 'reporterepuestos'])  ->name('reporterepuestos');

    //Route::match(['get','post'],'/reporte/compra', [SacompController::class, 'reportecompra'])->name('reportecompra');

    Route::match(['get','post'],'/reporte/venta', [HomeController::class, 'reporteventa'])->name('reporteventa');
    Route::post('/reporte/venta/sucu', [HomeController::class, 'reporteventasucu'])->name('reporteventasucu');

});
/*
// Para acceder al panel admin desde la tienda
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', function () {
        return redirect('/index'); // o a donde quieras
    });
});
*/
Route::post('/chat/initialize', [ChatbotController::class, 'initialize'])->name('chat.initialize');
Route::post('/chat/message', [ChatbotController::class, 'message'])->name('chatbot.message');
Route::post('/chat/end', [ChatbotController::class, 'endConversation'])->name('chat.end');


Route::controller(CwtransferenciasController::class)->group(function () {
    Route::get('transferencias/cambiarstatus/{Cwtransferencia}', 'cambiarstatus');
    Route::get('transferencias/validar/{Cwtransferencia}', 'validar')->name('transferencias.validar');
});

