<?php

namespace App\Http\Controllers;

use App\Models\NewSaexis;
use App\Models\Sainsta;
use App\Models\Saprod;
use App\Models\Sasucursal;
use App\Models\ChatConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{
    /**
     * Página principal de la tienda - Optimizada y moderna
     */
    public function index(Request $request)
    {
        $comercialId = session('comercialid') ?? 1;

        // ============================================================
        // 1. CATEGORÍAS PRINCIPALES (con caché)
        // ============================================================
        $categorias = Cache::remember("categorias_{$comercialId}", 3600, function() use ($comercialId) {
            return Sainsta::where('nivel', 1)
                ->where('insPadre', 0)
                ->where('tipoIns', 0)
                ->where('comercial', $comercialId)
                ->orderBy('descrip')
                ->get();
        });

        // ============================================================
        // 2. SUCURSALES (con caché)
        // ============================================================
        $sucursales = Cache::remember("sucursales_{$comercialId}", 3600, function() use ($comercialId) {
            return Sasucursal::where('fk_comercial', $comercialId)
                ->where('activo', 1)
                ->orderBy('descrip')
                ->get();
        });

        // ============================================================
        // 3. SUCURSALES CON COORDENADAS PARA EL MAPA
        // ============================================================
        $sucursalesMapa = $sucursales->map(function($sucursal) {
            return [
                'id' => $sucursal->id,
                'nombre' => $sucursal->descrip,
                'direccion' => $sucursal->direccion ?? '',
                'telefono' => $sucursal->telefono ?? '',
                'lat' => $sucursal->latitud ?? 10.4806,
                'lng' => $sucursal->longitud ?? -66.9036,
            ];
        });

        // ============================================================
        // 4. PRODUCTOS DESTACADOS (solo 6 con stock)
        // ============================================================
        $productosDestacados = Cache::remember("destacados_{$comercialId}", 1800, function() use ($comercialId) {
            $productos = Saprod::where('comercial', $comercialId)
                ->where('activo', 1)
                ->whereExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('newsaexis')
                        ->whereColumn('newsaexis.codprod', 'saprod.codprod')
                        ->where('newsaexis.existen', '>', 0);
                })
                ->with(['instancia'])
                ->inRandomOrder()
                ->limit(6)
                ->get();

            // Cargar existencias
            $this->cargarExistencias($productos, $comercialId);
            return $productos;
        });

        // ============================================================
        // 5. NUEVOS PRODUCTOS (últimos 4 agregados)
        // ============================================================
        $productosNuevos = Cache::remember("nuevos_{$comercialId}", 1800, function() use ($comercialId) {
            $productos = Saprod::where('comercial', $comercialId)
                ->where('activo', 1)
                ->orderBy('created_at', 'desc')
                ->limit(4)
                ->get();

            $this->cargarExistencias($productos, $comercialId);
            return $productos;
        });

        // ============================================================
        // 6. SOLO BUSCAR SI HAY QUERY
        // ============================================================
        $productos = collect();
        $resultadosBusqueda = null;

        if ($request->has('q') && $request->q) {
            $term = $request->q;
            $query = Saprod::where('comercial', $comercialId)
                ->where('activo', 1)
                ->with(['instancia']);

            $query->where(function($q) use ($term) {
                $q->where('descrip', 'LIKE', "%{$term}%")
                    ->orWhere('codprod', 'LIKE', "%{$term}%")
                    ->orWhere('marca', 'LIKE', "%{$term}%")
                    ->orWhere('refere', 'LIKE', "%{$term}%");
            });

            if ($request->has('categoria') && $request->categoria) {
                $query->where('codinst', $request->categoria);
            }

            $resultadosBusqueda = $query->orderBy('descrip')
                ->paginate(24)
                ->appends($request->all());

            $this->cargarExistencias($resultadosBusqueda, $comercialId);
            $productos = $resultadosBusqueda;
        }

        // ============================================================
        // 7. DATOS PARA EL CHAT
        // ============================================================
        $sessionId = session()->getId();
        $conversation = ChatConversation::where('session_id', $sessionId)
            ->where('status', 'active')
            ->latest()
            ->first();

        // ============================================================
        // 8. ESTADÍSTICAS
        // ============================================================
        $stats = [
            'total_productos' => Cache::remember("total_productos_{$comercialId}", 3600, function() use ($comercialId) {
                return Saprod::where('comercial', $comercialId)->where('activo', 1)->count();
            }),
            'total_sucursales' => $sucursales->count(),
            'total_categorias' => $categorias->count(),
            'total_marcas' => Cache::remember("total_marcas_{$comercialId}", 3600, function() use ($comercialId) {
                return Saprod::where('comercial', $comercialId)
                    ->where('activo', 1)
                    ->whereNotNull('marca')
                    ->distinct('marca')
                    ->count('marca');
            }),
        ];

        // ============================================================
        // 9. TESTIMONIOS (estáticos para el diseño)
        // ============================================================
        $testimonios = [
            [
                'nombre' => 'Carlos Méndez',
                'rol' => 'Mecánico',
                'comentario' => 'Los repuestos de Osorio Group son de la mejor calidad. He trabajado con ellos por años y nunca me han fallado.',
                'avatar' => 'avatar-1.jpg',
                'rating' => 5
            ],
            [
                'nombre' => 'María Rodríguez',
                'rol' => 'Concesionario',
                'comentario' => 'Excelente servicio y atención. Siempre tienen los productos que necesito en stock.',
                'avatar' => 'avatar-2.jpg',
                'rating' => 5
            ],
            [
                'nombre' => 'Luis Pérez',
                'rol' => 'Taller Mecánico',
                'comentario' => 'La mejor variedad de motos y repuestos en todo el país. Los recomiendo ampliamente.',
                'avatar' => 'avatar-3.jpg',
                'rating' => 5
            ],
        ];

        return view('shop.index', compact(
            'productos',
            'categorias',
            'sucursales',
            'sucursalesMapa',
            'productosDestacados',
            'productosNuevos',
            'resultadosBusqueda',
            'conversation',
            'stats',
            'testimonios'
        ));
    }

    /**
     * Cargar existencias por sucursal para una colección de productos
     */
    private function cargarExistencias($productos, $comercialId)
    {
        if ($productos->isEmpty()) {
            return;
        }

        $codprods = $productos->pluck('codprod')->toArray();

        $existencias = NewSaexis::whereIn('codprod', $codprods)
            ->where('existen', '>', 0)
            ->with(['sucursal', 'deposito'])
            ->get()
            ->groupBy('codprod');

        foreach ($productos as $producto) {
            $producto->existencias_por_sucursal = $existencias->get($producto->codprod, collect());
        }
    }

    /**
     * API para obtener sucursales en formato JSON (para el mapa)
     */
    public function sucursalesJson(Request $request)
    {
        $comercialId = session('comercialid') ?? 1;

        $sucursales = Sasucursal::where('fk_comercial', $comercialId)
            ->where('activo', 1)
            ->select('id', 'descrip as nombre', 'direccion', 'telefono', 'latitud as lat', 'longitud as lng')
            ->get()
            ->map(function($sucursal) {
                if (!$sucursal->lat || !$sucursal->lng) {
                    $sucursal->lat = 10.4806;
                    $sucursal->lng = -66.9036;
                }
                return $sucursal;
            });

        return response()->json($sucursales);
    }

    /**
     * Detalle de producto
     */
    public function show($codprod)
    {
        $comercialId = session('comercialid') ?? 1;

        $producto = Saprod::where('codprod', $codprod)
            ->where('comercial', $comercialId)
            ->with(['instancia'])
            ->firstOrFail();

        $this->cargarExistencias(collect([$producto]), $comercialId);

        $relacionados = Saprod::where('comercial', $comercialId)
            ->where('codinst', $producto->codinst)
            ->where('codprod', '!=', $codprod)
            ->where('activo', 1)
            ->limit(6)
            ->get();

        $this->cargarExistencias($relacionados, $comercialId);

        return view('shop.product-detail', compact('producto', 'relacionados'));
    }

    /**
     * Productos por categoría
     */
    public function category($categoriaId)
    {
        $comercialId = session('comercialid') ?? 1;

        $categoria = Sainsta::where('codinst', $categoriaId)
            ->where('comercial', $comercialId)
            ->firstOrFail();

        // Datos básicos
        $categorias = Sainsta::where('nivel', 1)
            ->where('insPadre', 0)
            ->where('tipoIns', 0)
            ->where('comercial', $comercialId)
            ->orderBy('descrip')
            ->get();

        $sucursales = Sasucursal::where('fk_comercial', $comercialId)
            ->where('activo', 1)
            ->orderBy('descrip')
            ->get();

        $sucursalesMapa = $sucursales->map(function($sucursal) {
            return [
                'id' => $sucursal->id,
                'nombre' => $sucursal->descrip,
                'direccion' => $sucursal->direccion ?? '',
                'telefono' => $sucursal->telefono ?? '',
                'lat' => $sucursal->latitud ?? 10.4806,
                'lng' => $sucursal->longitud ?? -66.9036,
            ];
        });

        // Productos de esta categoría (paginar)
        $productos = Saprod::where('comercial', $comercialId)
            ->where('codinst', $categoriaId)
            ->where('activo', 1)
            ->with(['instancia'])
            ->orderBy('descrip')
            ->paginate(24);

        $this->cargarExistencias($productos, $comercialId);

        // Stats
        $stats = [
            'total_productos' => Saprod::where('comercial', $comercialId)->where('activo', 1)->count(),
            'total_sucursales' => $sucursales->count(),
            'total_categorias' => $categorias->count(),
            'total_marcas' => Saprod::where('comercial', $comercialId)->where('activo', 1)->whereNotNull('marca')->distinct('marca')->count('marca'),
        ];

        $sessionId = session()->getId();
        $conversation = ChatConversation::where('session_id', $sessionId)
            ->where('status', 'active')
            ->latest()
            ->first();

        // Productos destacados (solo 6)
        $productosDestacados = Saprod::where('comercial', $comercialId)
            ->where('activo', 1)
            ->where('codinst', $categoriaId)
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('newsaexis')
                    ->whereColumn('newsaexis.codprod', 'saprod.codprod')
                    ->where('newsaexis.existen', '>', 0);
            })
            ->limit(6)
            ->get();

        $this->cargarExistencias($productosDestacados, $comercialId);

        $productosNuevos = collect();

        return view('shop.index', compact(
            'productos',
            'categorias',
            'sucursales',
            'sucursalesMapa',
            'productosDestacados',
            'productosNuevos',
            'stats',
            'categoria',
            'conversation'
        ));
    }

    /**
     * Búsqueda AJAX para autocompletado
     */
    public function searchAjax(Request $request)
    {
        $term = $request->get('q', '');
        $comercialId = session('comercialid') ?? 1;

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $productos = Saprod::where('comercial', $comercialId)
            ->where('activo', 1)
            ->where(function($q) use ($term) {
                $q->where('descrip', 'LIKE', "%{$term}%")
                    ->orWhere('codprod', 'LIKE', "%{$term}%")
                    ->orWhere('marca', 'LIKE', "%{$term}%");
            })
            ->limit(10)
            ->get();

        $this->cargarExistencias($productos, $comercialId);

        return response()->json($productos);
    }

    /**
     * Limpiar caché (para administradores)
     */
    public function clearCache()
    {
        Cache::flush();
        return redirect()->back()->with('success', 'Caché limpiada correctamente');
    }
}
