@extends('layouts.master')
@section('title') Reporte Venta Anual @endsection

@section('css')
    <style>
        .table-anual th, .table-anual td { white-space: nowrap; }
        .mes-cell { cursor: pointer; transition: background-color 0.2s; }
        .mes-cell:hover { background-color: #f0f7ff !important; }
        .table-anual tbody tr:hover { background-color: #f8f9fa; }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0" style="color: #fff !important;">Reporte de Ventas Anual</h4>
                        <p class="text-white-50 mb-0 small">Año {{ $anio }} — Matriz Mensual de Instancias</p>
                    </div>
                    <form method="get" action="{{ route('reporteventa.anual') }}" class="d-flex gap-2">
                        <select name="anio" class="form-select form-select-sm border-0" onchange="this.form.submit()">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $y == $anio ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                        <table class="table table-hover table-anual align-middle mb-0 text-center" style="font-size: 13px;">
                            <thead class="table-light sticky-top">
                            <tr class="text-uppercase small fw-semibold text-muted">
                                <th class="ps-3 text-start" style="min-width: 200px;">Instancia</th>
                                @foreach(['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'] as $m)
                                    <th>{{ $m }}</th>
                                @endforeach
                                <th class="text-end bg-light">Total</th>
                                <th class="text-center pe-3" style="width: 60px;">Acción</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($arrayinsta as $key => $row)
                                @if($row['total'] != 0)
                                    <tr>
                                        <td class="ps-3 text-start fw-medium">{{ $row['descrip'] }}</td>

                                        {{-- Columnas de Meses --}}
                                        @for($i = 1; $i <= 12; $i++)
                                            <td class="mes-cell"
                                                data-codinst="{{ $key }}"
                                                data-descrip="{{ $row['descrip'] }}"
                                                data-mes="{{ $i }}"
                                                title="Ver sucursales de {{ $row['descrip'] }} en {{ date('F', mktime(0,0,0,$i,1)) }}">
                                                {{ $row['meses'][$i] > 0 ? number_format($row['meses'][$i], 0, ',', '.') : '-' }}
                                            </td>
                                        @endfor

                                        {{-- Total Anual --}}
                                        <td class="text-end fw-bold text-primary bg-light">
                                            {{ number_format($row['total'], 0, ',', '.') }}
                                        </td>

                                        {{-- Botón Ver Todo el Año --}}
                                        <td class="text-center pe-3">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary btn-ver-detalle"
                                                    data-codinst="{{ $key }}"
                                                    data-descrip="{{ $row['descrip'] }}"
                                                    data-mes="0"
                                                    title="Ver distribución anual por sucursal">
                                                <i class="bi bi-diagram-3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr><td colspan="15" class="text-center text-muted py-4">Sin ventas en el año {{ $anio }}</td></tr>
                            @endforelse
                            </tbody>
                            <tfoot class="table-light fw-bold sticky-bottom">
                            <tr>
                                <td class="ps-3 text-start">TOTALES</td>
                                @for($i = 1; $i <= 12; $i++)
                                    <td>{{ $totalesMes[$i] > 0 ? number_format($totalesMes[$i], 0, ',', '.') : '-' }}</td>
                                @endfor
                                <td class="text-end text-primary fs-6">{{ number_format($granTotal, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Distribución por Sucursal (Reutilizado) -->
    <div class="modal fade" id="modalDistribucion" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" style="margin-top: 80px;">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <div>
                        <h5 class="modal-title mb-0" id="modalDistribucionTitle" style="color: #fff !important;">Distribución por Sucursal</h5>
                        <small class="text-white-50" id="modalDistribucionSubtitle"></small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase text-muted">
                            <tr>
                                <th class="ps-3">Sucursal</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Total Venta</th>
                                <th class="text-end pe-3">% Part.</th>
                            </tr>
                            </thead>
                            <tbody id="modalDistribucionBody">
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status"></div>
                                    <div class="small text-muted mt-2">Cargando...</div>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot class="table-light fw-semibold" id="modalDistribucionFoot"></tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        $(document).on('click', '.mes-cell, .btn-ver-detalle', function () {
            const codinst = $(this).data('codinst');
            const descrip = $(this).data('descrip');
            const mes     = $(this).data('mes') || 0; // 0 = Todo el año

            let tituloMes = mes > 0 ? ` - ${getMesNombre(mes)}` : ' - Todo el Año';
            $('#modalDistribucionTitle').text('Distribución: ' + descrip + tituloMes);
            $('#modalDistribucionSubtitle').text('Año {{ $anio }}');

            $('#modalDistribucionBody').html(
                '<tr><td colspan="4" class="text-center py-4">' +
                '<div class="spinner-border text-primary" role="status"></div>' +
                '<div class="small text-muted mt-2">Cargando...</div></td></tr>'
            );
            $('#modalDistribucionFoot').empty();

            const scrollActual = window.scrollY;
            const modalEl = document.getElementById('modalDistribucion');

            $('body').css({
                position: 'fixed',
                top: -scrollActual + 'px',
                width: '100%',
                overflow: 'hidden'
            });

            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();

            modalEl.addEventListener('hidden.bs.modal', function handler() {
                $('body').css({ position: '', top: '', width: '', overflow: '' });
                window.scrollTo(0, scrollActual);
                modalEl.removeEventListener('hidden.bs.modal', handler);
            }, { once: true });

            // AJAX a la misma ruta, pero le pasamos el año y el mes
            $.ajax({
                url: '{{ route("reporteventa.instancia-detalle") }}',
                method: 'GET',
                data: {
                    codinst: codinst,
                    anio: '{{ $anio }}',
                    mes: mes
                },
                success: function (res) {
                    if (!res.detalle || res.detalle.length === 0) {
                        $('#modalDistribucionBody').html('<tr><td colspan="4" class="text-center text-muted py-4">Sin ventas registradas</td></tr>');
                        return;
                    }

                    let html = '';
                    const totalVenta = res.totales.venta;

                    res.detalle.forEach(function (r) {
                        const partPct = totalVenta > 0 ? (r.precioventa / totalVenta) * 100 : 0;
                        html += '<tr>' +
                            '<td class="ps-3 fw-medium">' + r.sucursal + '</td>' +
                            '<td class="text-center"><span class="badge bg-primary-subtle text-primary">' +
                            Number(r.cant).toLocaleString('es-VE') + '</span></td>' +
                            '<td class="text-end">$ ' + Number(r.precioventa).toLocaleString('es-VE', {minimumFractionDigits: 2}) + '</td>' +
                            '<td class="text-end pe-3 text-muted">' + Number(partPct).toFixed(2) + ' %</td>' +
                            '</tr>';
                    });
                    $('#modalDistribucionBody').html(html);

                    const t = res.totales;
                    $('#modalDistribucionFoot').html(
                        '<tr>' +
                        '<td class="ps-3">TOTALES</td>' +
                        '<td class="text-center">' + Number(t.cant).toLocaleString('es-VE') + '</td>' +
                        '<td class="text-end">$ ' + Number(t.venta).toLocaleString('es-VE', {minimumFractionDigits: 2}) + '</td>' +
                        '<td class="text-end pe-3">100 %</td>' +
                        '</tr>'
                    );
                },
                error: function () {
                    $('#modalDistribucionBody').html('<tr><td colspan="4" class="text-center text-danger py-4">Error al cargar datos</td></tr>');
                }
            });
        });

        function getMesNombre(mes) {
            const meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
            return meses[mes - 1];
        }
    </script>
@endsection
