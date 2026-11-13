<?php
// app/Http/Controllers/WebController.php

namespace App\Http\Controllers;

use App\Models\Sainsta;
use App\Models\Saprod;
use App\Models\Sasucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebController extends Controller
{
    public function home()
    {
        // Hero: 1 moto estrella
        $motoHero = Saprod::where('comercial', 1)
            ->where('activo', 1)
            ->whereHas('instancia', fn($q) => $q->where('codalte', 'LIKE', '01.%'))
            ->where('costod3', '>', 0)
            ->orderByDesc('updated_at')
            ->first();

        // Departamentos (nivel 1, solo productos)
        $departamentos = Sainsta::where('comercial', 1)
            ->where('nivel', 1)
            ->where('insPadre', 0)
            ->where('tipoIns', 0)
            ->whereNotIn('codalte', ['153.', '164.', '252.']) // seguros, placas, uso interno
            ->orderBy('codalte')
            ->get()
            ->map(function ($dep) {
                $dep->marcas = Sainsta::where('comercial', 1)
                    ->where('insPadre', $dep->codinst)
                    ->where('nivel', 2)
                    ->where('tipoIns', 0)
                    ->orderBy('descrip')
                    ->get();
                return $dep;
            })
            ->filter(fn($d) => $d->marcas->count() > 0 || $d->codalte === '45') // 45 no tiene marcas nivel 2 pero sí productos
            ->values();

        // Motos destacadas (para la sección del home)
        $motosDestacadas = Saprod::where('comercial', 1)
            ->where('activo', 1)
            ->unicosPorGrupo(1)
            ->whereHas('instancia', fn($q) => $q->where('codalte', 'LIKE', '01.%'))
            ->orderByDesc('grupo_destacado')
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get();

        $motosDestacadas->each(fn($m) => $m->variantes = $m->variantesDelGrupo());


// 1. Traer las marcas (nivel 2 bajo MOTOS)
        $marcasMotos = Sainsta::where('comercial', 1)
            ->where('nivel', 2)
            ->where('insPadre', 1)
            ->where('tipoIns', 0)
            ->whereNotIn('codalte', ['01.104', '01.47.', '01.112.'])
            ->orderBy('descrip')
            ->get();

// 2. Contar productos con stock agrupados por marca (una sola query)
        $conteos = DB::table('saprod as p')
            ->join('sainsta as i3', function ($j) {
                $j->on('i3.codinst', '=', 'p.codinst')
                    ->where('i3.nivel', '=', 3)
                    ->where('i3.comercial', '=', 1);
            })
            ->join('sainsta as i2', function ($j) {
                $j->on('i2.codinst', '=', 'i3.insPadre')
                    ->where('i2.nivel', '=', 2)
                    ->where('i2.insPadre', '=', 1)
                    ->where('i2.comercial', '=', 1);
            })
            ->join('sasucursal as suc', function ($j) {
                $j->on('suc.fk_comercial', '=', DB::raw('1'));
            })
            ->join('newsaexis as e', function ($j) {
                $j->on('e.codprod', '=', 'p.codprod')
                    ->on('e.fk_sucursal', '=', 'suc.id')
                    ->where('e.existen', '>', 0);
            })

            ->where('p.comercial', 1)
            ->where('p.activo', 1)
            ->groupBy('i2.codinst')
            ->select('i2.codinst', DB::raw('COUNT(DISTINCT p.codprod) as total'))
            ->pluck('total', 'i2.codinst');

// 3. Asignar el conteo a cada marca y filtrar
        $marcasMotos = $marcasMotos
            ->map(function ($m) use ($conteos) {
                $m->nombre_corto = $m->descrip;
                $m->imagen       = $m->imagen;
                $m->total_motos  = $conteos[$m->codinst] ?? 0;
                return $m;
            })
            ->filter(fn($m) => $m->total_motos > 0)
            ->values();

        session(['marcasMotos'=>$marcasMotos]);

        $sucursales = $this->getSucursales();

        return view('web.home', compact('sucursales', 'motoHero', 'departamentos', 'motosDestacadas', 'marcasMotos'));
    }

    public function motos(Request $request)
    {
        $marcaCodalte = $request->get('marca', '');
        $estilo       = $request->get('estilo', '');
        $precioDesde  = $request->get('precio_desde');
        $precioHasta  = $request->get('precio_hasta');
        $ordenarPor   = $request->get('orden', 'reciente');
        $busqueda     = $request->get('q', '');

        // Marcas (nivel 2 bajo 01.)
        $marcas = Sainsta::where('comercial', 1)
            ->where('nivel', 2)
            ->where('insPadre', 1)
            ->where('tipoIns', 0)
            ->whereNotIn('codalte', ['01.104', '01.47.', '01.112.'])
            ->orderBy('descrip')
            ->get();

// Conteo de productos con stock agrupado por marca
        $conteos = DB::table('saprod as p')
            ->join('sainsta as i3', function ($j) {
                $j->on('i3.codinst', '=', 'p.codinst')
                    ->where('i3.nivel', '=', 3)
                    ->where('i3.comercial', '=', 1);
            })
            ->join('sainsta as i2', function ($j) {
                $j->on('i2.codinst', '=', 'i3.insPadre')
                    ->where('i2.nivel', '=', 2)
                    ->where('i2.insPadre', '=', 1)
                    ->where('i2.comercial', '=', 1);
            })
            ->join('sasucursal as suc', function ($j) {
                $j->on('suc.fk_comercial', '=', DB::raw('1'));
            })
            ->join('newsaexis as e', function ($j) {
                $j->on('e.codprod', '=', 'p.codprod')
                    ->on('e.fk_sucursal', '=', 'suc.id')
                    ->where('e.existen', '>', 0);
            })
            ->where('p.comercial', 1)
            ->where('p.activo', 1)
            ->whereIn('p.id', function ($sub) {
                $sub->selectRaw('MIN(s2.id)')
                    ->from('saprod as s2')
                    ->where('s2.comercial', 1)
                    ->where('s2.activo', 1)
                    ->groupByRaw("COALESCE(NULLIF(s2.grupo, ''), CONCAT('__individual_', s2.id))");
            })
            ->groupBy('i2.codinst')
            ->select('i2.codinst', DB::raw('COUNT(DISTINCT p.codprod) as total'))
            ->pluck('total', 'i2.codinst');

// Asignar total y filtrar solo marcas con stock
        $marcas = $marcas
            ->map(function ($m) use ($conteos) {
                $m->nombre_corto = $m->descrip;
                $m->total_motos  = $conteos[$m->codinst] ?? 0;
                return $m;
            })
            ->filter(fn($m) => $m->total_motos > 0)
            ->values();

        // 🔥 Estilos = valores únicos de saprod.clase en motos activas
        $estilos = Saprod::where('comercial', 1)
            ->where('activo', 1)
            ->whereNotNull('clase')
            ->where('clase', '<>', '')
            ->whereHas('instancia', fn($q) => $q->where('codalte', 'LIKE', '01.%'))
            ->distinct()
            ->orderBy('clase')
            ->pluck('clase')
            ->filter()
            ->values();

        $codalteBase = $marcaCodalte ?: '01.';

        $query = Saprod::where('comercial', 1)
            ->where('activo', 1)
            ->unicosPorGrupo(1)
            ->whereHas('instancia', fn($q) => $q->where('codalte', 'LIKE', $codalteBase . '%'));

        if ($estilo != '') {
            $query->where('clase', $estilo);
        }

        if ($precioDesde !== null && $precioDesde !== '') {
            $query->where('costod3', '>=', (float) $precioDesde);
        }
        if ($precioHasta !== null && $precioHasta !== '') {
            $query->where('costod3', '<=', (float) $precioHasta);
        }

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('descrip', 'LIKE', "%{$busqueda}%")
                    ->orWhere('descrip2',   'LIKE', "%{$busqueda}%")
                    ->orWhere('descrip3',   'LIKE', "%{$busqueda}%")
                    ->orWhere('marca',   'LIKE', "%{$busqueda}%")
                    ->orWhere('refere',  'LIKE', "%{$busqueda}%")
                    ->orWhere('codprod', 'LIKE', "%{$busqueda}%");
            });
            $h = session('historial_busquedas', []);
            if (!in_array($busqueda, $h)) {
                $h[] = $busqueda;
                session(['historial_busquedas' => array_slice($h, -10)]);
            }
        }

        switch ($ordenarPor) {
            case 'precio_asc':  $query->orderBy('costod3', 'asc'); break;
            case 'precio_desc': $query->orderBy('costod3', 'desc'); break;
            case 'nombre':      $query->orderBy('descrip', 'asc'); break;
            default:            $query->orderByDesc('updated_at'); break;
        }

        $motos = $query->paginate(12)->appends($request->query());

        $motos->getCollection()->transform(function ($moto) {
            $moto->variantes = $moto->variantesDelGrupo();
            return $moto;
        });

        $marcaActiva = null;
        if ($marcaCodalte) {
            // Buscar primero en la colección filtrada
            $marcaActiva = $marcas->firstWhere('codalte', $marcaCodalte);

            // Si no está (sin stock), cargarla directo desde sainsta para el breadcrumb
            if (!$marcaActiva) {
                $marcaActiva = Sainsta::where('comercial', 1)
                    ->where('codalte', $marcaCodalte)
                    ->first();

                if ($marcaActiva) {
                    $marcaActiva->nombre_corto = $marcaActiva->descrip;
                }
            }
        }

        $rango = Saprod::where('comercial', 1)
            ->where('activo', 1)
            ->whereHas('instancia', fn($q) => $q->where('codalte', 'LIKE', '01.%'))
            ->where('costod3', '>', 0)
            ->selectRaw('MIN(costod3) as min, MAX(costod3) as max')
            ->first();

        if ($request->ajax()) {
            return response()->json([
                'header'    => view('web.partials.header-motos', [
                    'marcaActiva' => $marcaActiva,
                    'estilo'      => $estilo,
                    'motos'       => $motos,
                ])->render(),
                'sidebar'   => view('web.partials.sidebar-filtros', [
                    'marcas'       => $marcas,
                    'estilos'      => $estilos,
                    'marcaCodalte' => $marcaCodalte,
                    'estilo'       => $estilo,
                    'precioDesde'  => $precioDesde,
                    'precioHasta'  => $precioHasta,
                    'rango'        => $rango,
                ])->render(),
                'filtrosActivos' => view('web.partials.filtros-activos', [
                    'marcaActiva'  => $marcaActiva,
                    'marcaCodalte' => $marcaCodalte,
                    'estilo'       => $estilo,
                    'precioDesde'  => $precioDesde,
                    'precioHasta'  => $precioHasta,
                    'busqueda'     => $busqueda,
                ])->render(),
                'html'      => view('web.partials.grid-motos', [
                    'motos'       => $motos,
                    'marcaActiva' => $marcaActiva,
                    'busqueda'    => $busqueda,
                ])->render(),
                'total'     => $motos->total(),
                'firstItem' => $motos->firstItem(),
                'lastItem'  => $motos->lastItem(),
            ]);
        }

        return view('web.motos', compact(
            'marcas', 'estilos', 'marcaActiva', 'marcaCodalte',
            'estilo', 'precioDesde', 'precioHasta', 'ordenarPor',
            'motos', 'busqueda', 'rango'
        ));
    }

    public function catalogo(Request $request)
    {
        $familia = $request->get('familia', 'motos');
        $linea   = $request->get('linea', '');      // ej. 'SR', 'NK', 'CL-X'
        $busqueda = $request->get('q', '');

        $codalteMap = [
            'motos'       => '01.%',
            'repuestos'   => '45.%',
            'lubricantes' => '03.%',
            'boutique'    => '99.%',
        ];
        $codalte = $codalteMap[$familia] ?? '01.%';

        $query = Saprod::where('comercial', 1)
            ->where('activo', 1)
            ->whereHas('instancia', function ($q) use ($codalte, $linea) {
                $q->where('codalte', 'LIKE', $codalte);
                if ($linea) {
                    $q->where('descrip', 'LIKE', "%{$linea}%");
                }
            });

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('descrip', 'LIKE', "%{$busqueda}%")
                    ->orWhere('marca',   'LIKE', "%{$busqueda}%")
                    ->orWhere('refere',  'LIKE', "%{$busqueda}%")
                    ->orWhere('codprod', 'LIKE', "%{$busqueda}%");
            });
            // historial en sesión
            $h = session('historial_busquedas', []);
            if (!in_array($busqueda, $h)) { $h[] = $busqueda; session(['historial_busquedas' => array_slice($h, -10)]); }
        }

        // Líneas disponibles dentro de la familia (para submenú)
        $lineas = Sainsta::where('comercial', 1)
            ->where('nivel', 2)
            ->where('codalte', 'LIKE', $codalte)
            ->orderBy('descrip')
            ->get();

        $productos = $query->orderBy('descrip')->paginate(12)->appends($request->query());

        return view('web.catalogo', compact('productos', 'codalteMap', 'familia', 'linea', 'lineas', 'busqueda'));
}

    public function producto($codprod)
    {
        $producto = Saprod::where('comercial', 1)
            ->where('codprod', $codprod)
            ->with('instancia')
            ->firstOrFail();

        // Cargar variantes del mismo grupo
        $variantes = $producto->variantesDelGrupo();

        // Nombre a mostrar (grupo o producto)
        $nombreCatalogo = $producto->grupo_nombre ?: $producto->descrip;

        // Relacionados: excluyendo el mismo grupo
        $relacionados = Saprod::where('comercial', 1)
            ->where('activo', 1)
            ->unicosPorGrupo(1)
            ->where('codinst', $producto->codinst)
            ->where('codprod', '!=', $producto->codprod)
            ->when($producto->grupo, fn($q) => $q->where('grupo', '!=', $producto->grupo))
            ->limit(4)
            ->get();

        // Vistos recientemente
        $vistos = session('productos_vistos', []);
        $vistos = array_diff($vistos, [$codprod]);
        array_unshift($vistos, $codprod);
        session(['productos_vistos' => array_slice($vistos, 0, 8)]);

        return view('web.producto', compact(
            'producto', 'variantes', 'nombreCatalogo', 'relacionados'
        ));
    }

    public function addToCart(Request $request)
    {
        $producto = Saprod::where('comercial', 1)->where('codprod', $request->codprod)->firstOrFail();

        $cart = session('cart', []);
        $id = $producto->codprod;

        if (isset($cart[$id])) {
            $cart[$id]['cantidad'] += $request->get('cantidad', 1);
        } else {
            $cart[$id] = [
                'codprod'  => $producto->codprod,
                'descrip'  => $producto->descrip,
                'precio'   => $producto->costod3,
                'cantidad' => $request->get('cantidad', 1),
            ];
        }

        session(['cart' => $cart]);

        return response()->json([
            'success'    => true,
            'cart_count' => array_sum(array_column($cart, 'cantidad')),
        ]);
    }

    public function viewCart()
    {
        $cart = session('cart', []);
        $total = collect($cart)->sum(fn($i) => $i['precio'] * $i['cantidad']);
        return view('web.cart', compact('cart', 'total'));
    }

    public function removeFromCart($id)
    {
        $cart = session('cart', []);
        unset($cart[$id]);
        session(['cart' => $cart]);
        return redirect()->route('web.cart.view')->with('success', 'Producto eliminado');
    }

    // app/Http/Controllers/WebController.php

    /**
     * Datos de sucursales agrupadas por ciudad.
     * Reutilizable por home() y sucursales().
     */
    private function getSucursales(): array
    {
        return [
            'lafria' => [
                'label'      => 'La Fría',
                'count'      => 3,
                'descripcion'=> 'Tres tiendas multimarca en el corazón de La Fría.',
                'sucursales' => [
                    [
                        'nombre'  => 'Osorio Group',
                        'tipo'    => 'Multimarca',
                        'dir'     => 'Av. Aeropuerto',
                        'horario' => 'Lun–sáb 8:30–17:00',
                        'tel'     => null,
                        'ig'      => '@osoriogroup',
                        'logo'    => 'osorio.png',
                        'maps'    => 'https://maps.google.com/?q=Av.+Aeropuerto+La+Fria+Tachira',
                    ],
                    [
                        'nombre'  => 'Bera La Fría',
                        'tipo'    => 'Bera',
                        'dir'     => 'Av. Aeropuerto',
                        'horario' => 'Lun–sáb 8:30–17:00',
                        'tel'     => null,
                        'ig'      => '@vera_tachira_lafria',
                        'logo'    => 'bera.png',
                        'maps'    => 'https://maps.google.com/?q=Av.+Aeropuerto+La+Fria+Tachira',
                    ],
                    [
                        'nombre'  => 'Bel Motos',
                        'tipo'    => 'Bel Motos',
                        'dir'     => 'Av. Aeropuerto',
                        'horario' => 'Lun–sáb 8:30–17:00',
                        'tel'     => null,
                        'ig'      => '@belmotoosorio',
                        'logo'    => 'bel.png',
                        'maps'    => 'https://maps.google.com/?q=Av.+Aeropuerto+La+Fria+Tachira',
                    ],
                ],
            ],
            'sancristobal' => [
                'label'      => 'San Cristóbal',
                'count'      => 4,
                'descripcion'=> 'Cuatro tiendas con todas nuestras marcas, incluyendo CFMOTO.',
                'sucursales' => [
                    [
                        'nombre'  => 'EK San Cristóbal',
                        'tipo'    => 'EK · QJMOTOR',
                        'dir'     => 'Av. Carabobo',
                        'horario' => 'Lun–sáb 8:30–17:00',
                        'tel'     => null,
                        'ig'      => '@osoriomotos',
                        'logo'    => 'ek.png',
                        'maps'    => 'https://maps.google.com/?q=Av.+Carabobo+San+Cristobal+Tachira',
                    ],
                    [
                        'nombre'  => 'CFMOTO Táchira',
                        'tipo'    => 'CFMOTO',
                        'dir'     => 'Av. Libertador, Edif. Kato Motor, local B-10',
                        'horario' => 'Lun–vie 8:30–17:00 · Sáb 9:00–14:00',
                        'tel'     => '0414-4180083',
                        'ig'      => '@cfmoto_tachira',
                        'logo'    => 'cfmoto.png',
                        'maps'    => 'https://maps.google.com/?q=Av.+Libertador+Kato+Motor+San+Cristobal',
                    ],
                    [
                        'nombre'  => 'Haojin Osorio',
                        'tipo'    => 'Haojin',
                        'dir'     => null,
                        'horario' => 'Lun–sáb 8:30–17:00',
                        'tel'     => null,
                        'ig'      => '@haojinosorio',
                        'logo'    => 'haojin.png',
                        'maps'    => null,
                    ],
                    [
                        'nombre'  => 'Bel Motos',
                        'tipo'    => 'Bel Motos',
                        'dir'     => 'Av. Carabobo',
                        'horario' => 'Lun–sáb 8:30–17:00',
                        'tel'     => null,
                        'ig'      => '@belmotoosorio',
                        'logo'    => 'bel.png',
                        'maps'    => 'https://maps.google.com/?q=Av.+Carabobo+San+Cristobal+Tachira',
                    ],
                ],
            ],
            'sanantonio' => [
                'label'      => 'San Antonio',
                'count'      => 4,
                'descripcion'=> 'Cuatro tiendas en la frontera, con envíos a toda Venezuela.',
                'sucursales' => [
                    [
                        'nombre'  => 'Osorio Group',
                        'tipo'    => 'Multimarca',
                        'dir'     => 'Av. Venezuela',
                        'horario' => 'Lun–sáb 8:30–17:00',
                        'tel'     => null,
                        'ig'      => null,
                        'logo'    => 'osorio.png',
                        'maps'    => 'https://maps.google.com/?q=Av.+Venezuela+San+Antonio+Tachira',
                    ],
                    [
                        'nombre'  => 'EK San Antonio',
                        'tipo'    => 'EK · QJMOTOR',
                        'dir'     => 'Av. Venezuela',
                        'horario' => 'Lun–sáb 8:30–17:00',
                        'tel'     => null,
                        'ig'      => '@eksanantonio',
                        'logo'    => 'ek.png',
                        'maps'    => 'https://maps.google.com/?q=Av.+Venezuela+San+Antonio+Tachira',
                    ],
                    [
                        'nombre'  => 'Haojin Osorio',
                        'tipo'    => 'Haojin',
                        'dir'     => null,
                        'horario' => 'Lun–sáb 8:30–17:00',
                        'tel'     => null,
                        'ig'      => '@haojinosorio',
                        'logo'    => 'haojin.png',
                        'maps'    => null,
                    ],
                    [
                        'nombre'  => 'Bel Motos',
                        'tipo'    => 'Bel Motos',
                        'dir'     => 'Av. Venezuela',
                        'horario' => 'Lun–sáb 8:30–17:00',
                        'tel'     => null,
                        'ig'      => '@belmotoosorio',
                        'logo'    => 'bel.png',
                        'maps'    => 'https://maps.google.com/?q=Av.+Venezuela+San+Antonio+Tachira',
                    ],
                ],
            ],
        ];
    }

    public function sucursales(Request $request)
    {
        $ciudades   = $this->getSucursales();
        $tabActivo  = $request->get('tab', 'todas');
        $totalTiendas = collect($ciudades)->sum('count');

        return view('web.sucursales', compact('ciudades', 'tabActivo', 'totalTiendas'));
    }
}
