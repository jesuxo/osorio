<?php
// app/Models/Sacomp.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sacomp extends Model
{
    use HasFactory;
    protected $table    = 'sacomp';

    protected $fillable = ['tipocom', 'numerod', 'codprov', 'nrounico', 'nroctrol', 'numeror', 'otipo', 'onumero', '
                            numeron', 'fechat', 'fechai', 'fechae', 'fechav', 'codusua', 'codesta', 'signo', 'codoper', 'texento', 'porcret', 'credito', '
                            codubic', 'descrip', 'direc1', 'direc2', 'telef', 'id3', 'monto', 'totalprd', 'totalsrv', 'tgravable', 'fletes', 'mtotax', '
                            reteniva', 'descto1', 'descto2', 'totaldeuda', 'mtototal', 'notas1', 'notas2', 'notas3', 'notas5', 'notas8', 'fk_sucursal'];

    public function proveedor (){
        return $this->belongsTo(Saprov::class, 'codprov', 'codprov');
    }

    public function sucursal  (){
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

    public function items     (){
        return $this->hasMany(Saitemcom::class, 'numerod', 'numerod')
            ->whereColumn('tipocom','tipocom');
    }

    public function seriales     (){
        return $this->hasMany(Saseprcom::class, 'numerod', 'numerod')
            ->whereColumn('tipocom','tipocom');
    }

    protected $appends = [ 'fechaformat', 'createdformat', 'stats_verificacion' ];

    public function getFechaformatAttribute(){
        $date = $this->fechat;
        if(isset($date)){
            list($fecha,$horas) = explode(' ',$date);
            list($y,$m,$d) = explode('-',$fecha);
            return "$d/$m/$y";
        }
    }

    public function getCreatedformatAttribute(){
        $date = $this->fechat;
        if(isset($date)){
            list($fecha,$horas) = explode(' ',$date);
            list($y,$m,$d) = explode('-',$fecha);
            return "$d/$m/$y";
        }
    }

    /**
     * Obtener estadísticas de verificación de los seriales de la compra
     */
    public function getStatsVerificacionAttribute()
    {
        // Si no hay seriales, retornar estadísticas vacías
        if (!$this->seriales || $this->seriales->isEmpty()) {
            return [
                'total' => 0,
                'pendientes' => 0,
                'verificados' => 0,
                'descargados' => 0,
                'vendidos' => 0,
                'con_comentarios' => 0,
                'porcentaje_avance' => 0,
                'completado' => false
            ];
        }

        $total = $this->seriales->count();
        $pendientes = $this->seriales->where('checked', 0)->count();
        $verificados = $this->seriales->where('checked', 1)->count();
        $descargados = $this->seriales->where('checked', 2)->count();
        $vendidos = $this->seriales->where('checked', 3)->count();
        $conComentarios = $this->seriales->whereNotNull('check_comment')->count();

        // Calcular porcentaje de avance (considerando verificados, descargados y vendidos como "progreso")
        $progreso = $verificados + $descargados + $vendidos;
        $porcentaje = $total > 0 ? round(($progreso / $total) * 100, 0) : 0;

        return [
            'total' => $total,
            'pendientes' => $pendientes,
            'verificados' => $verificados,
            'descargados' => $descargados,
            'vendidos' => $vendidos,
            'con_comentarios' => $conComentarios,
            'porcentaje_avance' => $porcentaje,
            'completado' => $pendientes === 0,
            'progreso' => $progreso,
            'html' => $this->getStatsHtml($pendientes, $verificados, $descargados, $vendidos, $total, $porcentaje)
        ];
    }

    /**
     * Generar HTML para las estadísticas
     */
    private function getStatsHtml($pendientes, $verificados, $descargados, $vendidos, $total, $porcentaje)
    {
        $html = '<div class="stats-verificacion" style="min-width: 150px;">';

        // Barra de progreso
        $html .= '<div class="progress mb-2" style="height: 6px;">';
        $html .= '<div class="progress-bar bg-success" role="progressbar" style="width: ' . ($verificados/$total*100) . '%" title="Verificados: ' . $verificados . '"></div>';
        $html .= '<div class="progress-bar bg-danger" role="progressbar" style="width: ' . ($descargados/$total*100) . '%" title="Descargados: ' . $descargados . '"></div>';
        $html .= '<div class="progress-bar bg-primary" role="progressbar" style="width: ' . ($vendidos/$total*100) . '%" title="Vendidos: ' . $vendidos . '"></div>';
        $html .= '</div>';

        // Contadores con colores
        $html .= '<div class="d-flex flex-wrap gap-1 justify-content-center">';

        if ($pendientes > 0) {
            $html .= '<span class="badge bg-warning" title="Pendientes"><i class="bi bi-clock-history"></i> ' . $pendientes . '</span>';
        }
        if ($verificados > 0) {
            $html .= '<span class="badge bg-success" title="Verificados"><i class="bi bi-check-circle"></i> ' . $verificados . '</span>';
        }
        if ($descargados > 0) {
            $html .= '<span class="badge bg-danger" title="Descargados"><i class="bi bi-arrow-down-circle"></i> ' . $descargados . '</span>';
        }
        if ($vendidos > 0) {
            $html .= '<span class="badge bg-primary" title="Vendidos"><i class="bi bi-cart-check"></i> ' . $vendidos . '</span>';
        }

        $html .= '</div>';

        // Porcentaje de avance
        $html .= '<small class="text-muted d-block text-center mt-1">' . $porcentaje . '% completado</small>';

        $html .= '</div>';

        return $html;
    }

    /**
     * Verificar si la compra tiene todos los seriales verificados
     */
    public function verificacionCompleta()
    {
        return $this->seriales->isNotEmpty() && $this->seriales->where('checked', 0)->isEmpty();
    }

    /**
     * Obtener el color del estado de verificación
     */
    public function getVerificacionColorAttribute()
    {
        if ($this->seriales->isEmpty()) {
            return 'secondary';
        }

        $pendientes = $this->seriales->where('checked', 0)->count();

        if ($pendientes === 0) {
            return 'success'; // Todos verificados
        } elseif ($pendientes === $this->seriales->count()) {
            return 'danger'; // Ninguno verificado
        } else {
            return 'warning'; // Algunos verificados
        }
    }
}
