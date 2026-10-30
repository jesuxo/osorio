@extends('layouts.master')
@section('title') Reporte Compra Anual @endsection

@section('css')
    <style>
        .table-anual th, .table-anual td { white-space: nowrap; }
        .mes-cell { cursor: pointer; transition: background-color 0.2s; }
        .mes-cell:hover { background-color: #f0f7ff !important; }
        .table-anual.view-cant .col-monto { display: none !important; }
        .table-anual.view-monto .col-cant { display: none !important; }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0" style="color: #fff !important;">Reporte de Compras Anual</h4>
                        <p class="text-white-50 mb-0 small">Año {{ $anio }} — Matriz Mensual de Instancias</p>
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="viewtype" id="viewCant" value="cant" checked onchange="cambiarVista('cant')">
                            <label class="btn btn-outline-light" for="viewCant">Cantidades</label>
                            <input type="radio" class="btn-check" name="viewtype" id="viewMonto" value="monto" onchange="cambiarVista('monto')">
                            <label class="btn btn-outline-light" for="viewMonto">Montos ($)</label>
                        </div>
                        <button type="button" class="btn btn-sm btn-light fw-semibold" onclick="descargarExcel()">
                            <i class="bi bi-file-earmark-excel me-1"></i> Excel
                        </button>
                        <form method="get" action="{{ route('reportecompra.anual') }}" class="d-flex gap-2 mb-0">
                            <select name="anio" class="form-select form-select-sm border-0" onchange="this.form.submit()">
                                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ $y == $anio ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </form>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                        <table class="table table-hover table-anual align-middle mb-0 text-center view-cant" id="tablaAnual" style="font-size: 13px;">
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
                                @if($row['total_cant'] != 0)
                                    <tr>
                                        <td class="ps-3 text-start fw-medium">{{ $row['descrip'] }}</td>
                                        @for($i = 1; $i <= 12; $i++)
                                            @php
                                                $cant  = $row['meses'][$i]['cant'];
                                                $monto = $row['meses'][$i]['monto'];
                                            @endphp
                                            <td class="mes-cell" data-codinst="{{ $key }}" data-descrip="{{ $row['descrip'] }}" data-mes="{{ $i }}">
                                                <span class="col-cant">{{ $cant > 0 ? number_format($cant, 0, ',', '.') : '-' }}</span>
                                                <span class="col-monto">{{ $monto > 0 ? '$' . number_format($monto, 2, ',', '.') : '-' }}</span>
                                            </td>
                                        @endfor
                                        <td class="text-end fw-bold text-primary bg-light">
                                            <span class="col-cant">{{ number_format($row['total_cant'], 0, ',', '.') }}</span>
                                            <span class="col-monto">$ {{ number_format($row['total_monto'], 2, ',', '.') }}</span>
                                        </td>
                                        <td class="text-center pe-3">
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-ver-detalle" data-codinst="{{ $key }}" data-descrip="{{ $row['descrip'] }}" data-mes="0">
                                                <i class="bi bi-diagram-3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr><td colspan="15" class="text-center text-muted py-4">Sin compras en el año {{ $anio }}</td></tr>
                            @endforelse
                            </tbody>
                            <tfoot class="table-light fw-bold sticky-bottom">
                            <tr>
                                <td class="ps-3 text-start">TOTALES</td>
                                @for($i = 1; $i <= 12; $i++)
                                    <td>
                                        <span class="col-cant">{{ $totalesMes[$i]['cant'] > 0 ? number_format($totalesMes[$i]['cant'], 0, ',', '.') : '-' }}</span>
                                        <span class="col-monto">{{ $totalesMes[$i]['monto'] > 0 ? '$' . number_format($totalesMes[$i]['monto'], 2, ',', '.') : '-' }}</span>
                                    </td>
                                @endfor
                                <td class="text-end text-primary fs-6">
                                    <span class="col-cant">{{ number_format($granTotalCant, 0, ',', '.') }}</span>
                                    <span class="col-monto">$ {{ number_format($granTotalMonto, 2, ',', '.') }}</span>
                                </td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Distribución -->
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
                                <th class="text-end pe-3">Monto Compra</th>
                            </tr>
                            </thead>
                            <tbody id="modalDistribucionBody">
                            <tr>
                                <td colspan="3" class="text-center py-4">
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
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        function cambiarVista(tipo) {
            const tabla = document.getElementById('tablaAnual');
            tabla.classList.toggle('view-cant', tipo === 'cant');
            tabla.classList.toggle('view-monto', tipo === 'monto');
        }

        $(document).on('click', '.mes-cell, .btn-ver-detalle', function () {
            const codinst = $(this).data('codinst');
            const descrip = $(this).data('descrip');
            const mes     = $(this).data('mes') || 0;

            let tituloMes = mes > 0 ? ` - ${getMesNombre(mes)}` : ' - Todo el Año';
            $('#modalDistribucionTitle').text('Distribución: ' + descrip + tituloMes);
            $('#modalDistribucionSubtitle').text('Año {{ $anio }}');

            $('#modalDistribucionBody').html(
                '<tr><td colspan="3" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><div class="small text-muted mt-2">Cargando...</div></td></tr>'
            );
            $('#modalDistribucionFoot').empty();

            const scrollActual = window.scrollY;
            const modalEl = document.getElementById('modalDistribucion');
            $('body').css({ position: 'fixed', top: -scrollActual + 'px', width: '100%', overflow: 'hidden' });

            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();

            modalEl.addEventListener('hidden.bs.modal', function handler() {
                $('body').css({ position: '', top: '', width: '', overflow: '' });
                window.scrollTo(0, scrollActual);
                modalEl.removeEventListener('hidden.bs.modal', handler);
            }, { once: true });

            $.ajax({
                url: '{{ route("reportecompra.instancia-detalle") }}',
                method: 'GET',
                data: { codinst: codinst, anio: '{{ $anio }}', mes: mes },
                success: function (res) {
                    if (!res.detalle || res.detalle.length === 0) {
                        $('#modalDistribucionBody').html('<tr><td colspan="3" class="text-center text-muted py-4">Sin compras registradas</td></tr>');
                        return;
                    }

                    let html = '';
                    res.detalle.forEach(function (r) {
                        html += '<tr>' +
                            '<td class="ps-3 fw-medium">' + r.sucursal + '</td>' +
                            '<td class="text-center"><span class="badge bg-primary-subtle text-primary">' +
                            Number(r.cant).toLocaleString('es-VE') + '</span></td>' +
                            '<td class="text-end pe-3">$ ' + Number(r.monto_compra).toLocaleString('es-VE', {minimumFractionDigits: 2}) + '</td>' +
                            '</tr>';
                    });
                    $('#modalDistribucionBody').html(html);

                    $('#modalDistribucionFoot').html(
                        '<tr>' +
                        '<td class="ps-3">TOTALES</td>' +
                        '<td class="text-center">' + Number(res.totales.cant).toLocaleString('es-VE') + '</td>' +
                        '<td class="text-end pe-3">$ ' + Number(res.totales.monto).toLocaleString('es-VE', {minimumFractionDigits: 2}) + '</td>' +
                        '</tr>'
                    );
                }
            });
        });

        function getMesNombre(mes) {
            const meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
            return meses[mes - 1];
        }

        function descargarExcel() {
            const tabla = document.getElementById('tablaAnual');
            const modoCant = tabla.classList.contains('view-cant');
            const modoTexto = modoCant ? 'Cantidades' : 'Montos USD';
            const anio = '{{ $anio }}';
            const tablaClon = tabla.cloneNode(true);

            const claseAEliminar = modoCant ? '.col-monto' : '.col-cant';
            tablaClon.querySelectorAll(claseAEliminar).forEach(el => el.remove());

            tablaClon.querySelectorAll('tr').forEach(tr => {
                const celdas = tr.querySelectorAll('th, td');
                if (celdas.length > 0) {
                    const ultima = celdas[celdas.length - 1];
                    if (ultima.querySelector('button') || (tr.closest('thead') && ultima.textContent.trim() === 'Acción')) {
                        ultima.remove();
                    }
                }
            });

            const wb = XLSX.utils.table_to_book(tablaClon, { sheet: "Compras" });
            const ws = wb.Sheets["Compras"];
            XLSX.utils.sheet_add_aoa(ws, [[`Reporte de Compras Anual ${anio} - ${modoTexto}`]], { origin: "A1" });

            ws['!cols'] = [{ wch: 35 }, ...Array(12).fill({ wch: 14 }), { wch: 16 }];
            XLSX.writeFile(wb, `Reporte_Compras_${anio}_${modoCant ? 'Cantidades' : 'Montos'}.xlsx`);
        }
    </script>
@endsection
