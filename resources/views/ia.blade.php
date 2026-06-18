@extends('layouts.master')

@section('title')
    Base de Conocimiento - IA
@endsection

@section('css')
    <style>
        /* ===== ESTILOS MEJORADOS ===== */

        /* Tarjetas de estadísticas */
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #eef2f7;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        }
        .stats-card .stats-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .stats-card .stats-number {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a2332;
            line-height: 1.2;
        }
        .stats-card .stats-label {
            font-size: 0.875rem;
            color: #6b7a8f;
            font-weight: 500;
            margin-top: 2px;
        }

        /* Barra de herramientas */
        .toolbar-container {
            background: white;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            border: 1px solid #eef2f7;
            margin-bottom: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .toolbar-container .search-box {
            flex: 1;
            min-width: 200px;
            position: relative;
        }
        .toolbar-container .search-box input {
            padding-left: 2.5rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            height: 42px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        .toolbar-container .search-box input:focus {
            background: white;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        .toolbar-container .search-box .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        .toolbar-container .filter-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .toolbar-container .filter-group select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            height: 42px;
            padding: 0 1rem;
            font-size: 0.9rem;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .toolbar-container .filter-group select:focus {
            border-color: #4f46e5;
            outline: none;
        }
        .btn-primary-custom {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            height: 42px;
            white-space: nowrap;
        }
        .btn-primary-custom:hover {
            background: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            color: white;
        }
        .btn-primary-custom i {
            font-size: 1.1rem;
        }

        /* Tabla mejorada */
        .table-container {
            background: white;
            border-radius: 12px;
            border: 1px solid #eef2f7;
            overflow: hidden;
        }
        .table-container .table {
            margin-bottom: 0;
        }
        .table-container .table thead th {
            background: #f8fafc;
            border-bottom: 2px solid #eef2f7;
            padding: 0.875rem 1rem;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
            white-space: nowrap;
        }
        .table-container .table tbody td {
            padding: 0.875rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
        }
        .table-container .table tbody tr:hover {
            background: #f8fafc;
        }
        .table-container .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Badges mejorados */
        .badge-custom {
            padding: 0.3rem 0.75rem;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.75rem;
            letter-spacing: 0.3px;
        }
        .badge-active {
            background: #dcfce7;
            color: #16a34a;
        }
        .badge-inactive {
            background: #fee2e2;
            color: #dc2626;
        }
        .badge-category {
            background: #e0e7ff;
            color: #4f46e5;
            padding: 0.2rem 0.6rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 500;
        }

        /* Botones de acción */
        .action-buttons {
            display: flex;
            gap: 0.3rem;
            flex-wrap: wrap;
        }
        .action-buttons .btn-sm-custom {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .action-buttons .btn-edit {
            background: #e0e7ff;
            color: #4f46e5;
        }
        .action-buttons .btn-edit:hover {
            background: #4f46e5;
            color: white;
            transform: scale(1.05);
        }
        .action-buttons .btn-delete {
            background: #fee2e2;
            color: #dc2626;
        }
        .action-buttons .btn-delete:hover {
            background: #dc2626;
            color: white;
            transform: scale(1.05);
        }
        .action-buttons .btn-toggle {
            background: #f1f5f9;
            color: #475569;
        }
        .action-buttons .btn-toggle:hover {
            background: #475569;
            color: white;
        }

        /* Texto truncado */
        .text-truncate-custom {
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }


        /* ===== PAGINACIÓN MEJORADA ===== */
        .pagination-custom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 1rem 1.25rem;
            background: #f8fafc;
            border-top: 1px solid #eef2f7;
        }
        .pagination-custom .pagination-info {
            font-size: 0.875rem;
            color: #64748b;
        }
        .pagination-custom .pagination-info strong {
            color: #1a2332;
        }
        .pagination-custom .pagination {
            margin: 0;
            gap: 0.25rem;
        }
        .pagination-custom .pagination .page-item .page-link {
            border: none;
            padding: 0.5rem 0.875rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #475569;
            background: transparent;
            transition: all 0.2s ease;
            min-width: 38px;
            text-align: center;
        }
        .pagination-custom .pagination .page-item .page-link:hover {
            background: #e0e7ff;
            color: #4f46e5;
        }
        .pagination-custom .pagination .page-item.active .page-link {
            background: #4f46e5;
            color: white;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3);
        }
        .pagination-custom .pagination .page-item.disabled .page-link {
            color: #cbd5e1;
            cursor: not-allowed;
        }
        .pagination-custom .pagination .page-item .page-link i {
            font-size: 0.8rem;
        }

        /* Modal mejorado */
        .modal-content-custom {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .modal-content-custom .modal-header {
            border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem 1.5rem;
            background: #f8fafc;
            border-radius: 16px 16px 0 0;
        }
        .modal-content-custom .modal-header .modal-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #1a2332;
        }
        .modal-content-custom .modal-body {
            padding: 1.5rem;
        }
        .modal-content-custom .modal-footer {
            border-top: 1px solid #f1f5f9;
            padding: 1rem 1.5rem;
            background: #f8fafc;
            border-radius: 0 0 16px 16px;
        }
        .modal-content-custom .form-control,
        .modal-content-custom .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 0.625rem 0.875rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .modal-content-custom .form-control:focus,
        .modal-content-custom .form-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        .modal-content-custom .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #334155;
            margin-bottom: 0.3rem;
        }
        .modal-content-custom .form-text {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        /* Toggle Switch */
        .toggle-switch {
            position: relative;
            width: 40px;
            height: 22px;
            display: inline-block;
            flex-shrink: 0;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #cbd5e1;
            transition: 0.3s;
            border-radius: 34px;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 3px;
            bottom: 3px;
            background: white;
            transition: 0.3s;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.15);
        }
        .toggle-switch input:checked + .toggle-slider {
            background: #4f46e5;
        }
        .toggle-switch input:checked + .toggle-slider:before {
            transform: translateX(18px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .toolbar-container {
                flex-direction: column;
                align-items: stretch;
            }
            .toolbar-container .search-box {
                min-width: unset;
            }
            .toolbar-container .filter-group {
                flex-wrap: wrap;
            }
            .toolbar-container .filter-group select {
                flex: 1;
                min-width: 120px;
            }
            .pagination-custom {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .table-container .table {
                font-size: 0.8rem;
            }
            .table-container .table thead th,
            .table-container .table tbody td {
                padding: 0.5rem 0.6rem;
            }
            .text-truncate-custom {
                max-width: 120px;
            }
            .stats-card .stats-number {
                font-size: 1.25rem;
            }
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .stats-card {
            animation: fadeInUp 0.5s ease forwards;
        }
        .stats-card:nth-child(1) { animation-delay: 0.05s; }
        .stats-card:nth-child(2) { animation-delay: 0.1s; }
        .stats-card:nth-child(3) { animation-delay: 0.15s; }
        .stats-card:nth-child(4) { animation-delay: 0.2s; }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">

        {{-- ===== TÍTULO Y ACCIONES ===== --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-1 fw-bold" style="color: #1a2332;">🤖 Base de Conocimiento</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Gestiona la información que utiliza el asistente virtual para atender clientes</p>
            </div>
            <button type="button" class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#knowledgeModal">
                <i class="bi bi-plus-lg"></i> Nueva Información
            </button>
        </div>

        {{-- ===== ESTADÍSTICAS ===== --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stats-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon" style="background: #e0e7ff; color: #4f46e5;">
                            <i class="bi bi-database"></i>
                        </div>
                        <div>
                            <div class="stats-number">{{ $knowledgeItems->total() }}</div>
                            <div class="stats-label">Total de registros</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stats-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon" style="background: #dcfce7; color: #16a34a;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <div class="stats-number">{{ $knowledgeItems->where('active', true)->count() }}</div>
                            <div class="stats-label">Activos</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stats-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon" style="background: #fef3c7; color: #d97706;">
                            <i class="bi bi-tags"></i>
                        </div>
                        <div>
                            <div class="stats-number">{{ $knowledgeItems->pluck('category')->filter()->unique()->count() }}</div>
                            <div class="stats-label">Categorías</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="stats-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stats-icon" style="background: #fce4ec; color: #e11d48;">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div>
                            <div class="stats-number">{{ $knowledgeItems->first()?->fechaformat ?? 'N/A' }}</div>
                            <div class="stats-label">Última actualización</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== BARRA DE HERRAMIENTAS ===== --}}
        <div class="toolbar-container">
            <div class="search-box">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar por título, contenido o categoría...">
            </div>
            <div class="filter-group">
                <select id="filterCategory" class="form-select">
                    <option value="">Todas las categorías</option>
                    @php
                        $categories = $knowledgeItems->pluck('category')->filter()->unique();
                    @endphp
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
                <select id="filterStatus" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="1">Activos</option>
                    <option value="0">Inactivos</option>
                </select>
                <button class="btn-primary-custom" id="clearFilters" style="background: #e2e8f0; color: #475569;">
                    <i class="bi bi-x-lg"></i> Limpiar
                </button>
            </div>
        </div>

        {{-- ===== TABLA DE REGISTROS ===== --}}
        <div class="table-container">
            <div class="table-responsive">
                <table class="table" id="knowledgeTable">
                    <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Título</th>
                        <th style="width: 130px;">Categoría</th>
                        <th>Contenido</th>
                        <th style="width: 90px;">Estado</th>
                        <th style="width: 160px; text-align: center;">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($knowledgeItems as $item)
                        <tr data-id="{{ $item->id }}" data-category="{{ $item->category }}" data-active="{{ $item->active }}">
                            <td class="text-muted">{{ $item->id }}</td>
                            <td>
                                <span class="fw-semibold" style="color: #1a2332;">{{ $item->title }}</span>
                            </td>
                            <td>
                                @if($item->category)
                                    <span class="badge-category">{{ $item->category }}</span>
                                @else
                                    <span class="text-muted" style="font-size: 0.75rem;">Sin categoría</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-truncate-custom" title="{{ $item->text }}">
                                    {{ Str::limit($item->text, 80) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-custom {{ $item->active ? 'badge-active' : 'badge-inactive' }}">
                                    {{ $item->active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons justify-content-center">
                                    <button class="btn-sm-custom btn-toggle" onclick="toggleStatus({{ $item->id }})" title="Cambiar estado">
                                        <i class="bi bi-{{ $item->active ? 'toggle-on' : 'toggle-off' }}"></i>
                                    </button>
                                    <button class="btn-sm-custom btn-edit" onclick="editKnowledge({{ $item }})" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn-sm-custom btn-delete" onclick="deleteKnowledge({{ $item->id }})" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 2.5rem; color: #cbd5e1;"></i>
                                <p class="mt-3 text-muted">No hay registros en la base de conocimiento</p>
                                <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#knowledgeModal" style="margin-top: 0.5rem;">
                                    <i class="bi bi-plus-lg"></i> Agregar el primero
                                </button>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ===== PAGINACIÓN MEJORADA ===== --}}
            <div class="pagination-custom">
                <div class="pagination-info">
                    Mostrando <strong>{{ $knowledgeItems->firstItem() ?? 0 }}</strong> -
                    <strong>{{ $knowledgeItems->lastItem() ?? 0 }}</strong> de
                    <strong>{{ $knowledgeItems->total() }}</strong> registros
                </div>
                <div>
                    {{ $knowledgeItems->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MODAL PARA CREAR/EDITAR ===== --}}
    <div class="modal fade" id="knowledgeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-content-custom">
                <form id="knowledgeForm" method="POST">
                    @csrf
                    <input type="hidden" id="method" name="_method" value="POST">
                    <input type="hidden" id="editId" name="edit_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">
                            <i class="bi bi-plus-circle me-2" style="color: #4f46e5;"></i> Agregar Información
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="title">Título <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Ej: Horario de atención" required>
                                <div class="form-text">Un título descriptivo que identifique fácilmente esta información</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="category">Categoría</label>
                                <input type="text" class="form-control" id="category" name="category" placeholder="Ej: Horarios, Productos, Garantías">
                                <div class="form-text">Agrupa información relacionada</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="tags">Tags</label>
                                <input type="text" class="form-control" id="tags" name="tags" placeholder="palabra1, palabra2, palabra3">
                                <div class="form-text">Separa con comas, ayuda a encontrar esta información</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="text">Contenido <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="text" name="text" rows="6" placeholder="Escribe aquí la información que el chatbot usará para responder..." required></textarea>
                                <div class="form-text">Esta información será utilizada por el asistente virtual para responder preguntas</div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex align-items-center gap-3">
                                    <label class="form-label mb-0" for="active">Estado:</label>
                                    <div class="toggle-switch">
                                        <input type="checkbox" id="active" name="active" checked>
                                        <span class="toggle-slider"></span>
                                    </div>
                                    <span id="statusLabel" class="text-muted" style="font-size: 0.875rem;">Activo</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.5rem 1.5rem;">Cancelar</button>
                        <button type="submit" class="btn-primary-custom" style="border: none;">
                            <i class="bi bi-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/app.js?'.rand(0,5555555)) }}"></script>
    <script>
        $(document).ready(function() {
            // ===== BUSCAR EN TIEMPO REAL =====
            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    applyFilters();
                }, 300);
            });

            // ===== FILTROS =====
            $('#filterCategory, #filterStatus').on('change', function() {
                applyFilters();
            });

            $('#clearFilters').on('click', function() {
                $('#searchInput').val('');
                $('#filterCategory').val('');
                $('#filterStatus').val('');
                applyFilters();
            });

            function applyFilters() {
                const search = $('#searchInput').val().toLowerCase();
                const category = $('#filterCategory').val();
                const status = $('#filterStatus').val();

                let visibleCount = 0;
                $('#knowledgeTable tbody tr').each(function() {
                    const $row = $(this);
                    const title = $row.find('td:nth-child(2)').text().toLowerCase();
                    const content = $row.find('td:nth-child(4)').text().toLowerCase();
                    const rowCategory = $row.data('category') || '';
                    const rowActive = $row.data('active') == 1;

                    let show = true;

                    if (search) {
                        show = title.includes(search) || content.includes(search) || rowCategory.toLowerCase().includes(search);
                    }

                    if (show && category) {
                        show = rowCategory === category;
                    }

                    if (show && status !== '') {
                        show = rowActive === (status === '1');
                    }

                    if (show) {
                        $row.show();
                        visibleCount++;
                    } else {
                        $row.hide();
                    }
                });

                // Mostrar mensaje si no hay resultados
                const $emptyMsg = $('#knowledgeTable tbody tr.empty-message');
                if (visibleCount === 0) {
                    if ($emptyMsg.length === 0) {
                        $('#knowledgeTable tbody').append(`
                    <tr class="empty-message">
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-search" style="font-size: 2rem; color: #cbd5e1;"></i>
                            <p class="mt-2 text-muted">No se encontraron resultados con los filtros aplicados</p>
                        </td>
                    </tr>
                `);
                    }
                } else {
                    $emptyMsg.remove();
                }
            }

            // ===== TOGGLE ESTADO =====
            window.toggleStatus = function(id) {
                const $row = $(`tr[data-id="${id}"]`);
                const currentStatus = $row.data('active') == 1;
                const newStatus = !currentStatus;

                $.ajax({
                    url: `{{ url("iaknowledge") }}/${id}`,
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        title: $row.find('td:nth-child(2)').text().trim(),
                        text: $row.find('td:nth-child(4)').text().trim(),
                        category: $row.data('category') || '',
                        tags: [],
                        active: newStatus
                    },
                    success: function() {
                        $row.data('active', newStatus ? 1 : 0);
                        const $badge = $row.find('.badge-custom');
                        const $btn = $row.find('.btn-toggle i');

                        if (newStatus) {
                            $badge.removeClass('badge-inactive').addClass('badge-active').text('Activo');
                            $btn.removeClass('bi-toggle-off').addClass('bi-toggle-on');
                        } else {
                            $badge.removeClass('badge-active').addClass('badge-inactive').text('Inactivo');
                            $btn.removeClass('bi-toggle-on').addClass('bi-toggle-off');
                        }

                        // Recontar estadísticas
                        updateStats();
                    },
                    error: function() {
                        alert('Error al cambiar el estado');
                    }
                });
            };

            function updateStats() {
                const total = $('#knowledgeTable tbody tr:visible:not(.empty-message)').length;
                const active = $('#knowledgeTable tbody tr:visible:not(.empty-message)').filter(function() {
                    return $(this).data('active') == 1;
                }).length;

                // Actualizar números en las tarjetas
                $('.stats-card').eq(0).find('.stats-number').text(total);
                $('.stats-card').eq(1).find('.stats-number').text(active);
            }

            // ===== GUARDAR FORMULARIO =====
            $('#knowledgeForm').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const method = $('#method').val();
                const id = $('#editId').val();
                let url = '{{ route("iaknowledge.store") }}';

                if (method === 'PUT' && id) {
                    url = `{{ url("iaknowledge") }}/${id}`;
                }

                const formData = {
                    title: $('#title').val(),
                    category: $('#category').val(),
                    text: $('#text').val(),
                    tags: $('#tags').val() ? $('#tags').val().split(',').map(tag => tag.trim()) : [],
                    active: $('#active').is(':checked')
                };

                $.ajax({
                    url: url,
                    method: method === 'PUT' ? 'PUT' : 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        $form.find('button[type="submit"]').html('<i class="bi bi-arrow-repeat bi-spin"></i> Guardando...').prop('disabled', true);
                    },
                    success: function() {
                        location.reload();
                    },
                    error: function() {
                        alert('Error al guardar la información');
                        $form.find('button[type="submit"]').html('<i class="bi bi-save"></i> Guardar').prop('disabled', false);
                    }
                });
            });

            // ===== EDITAR =====
            window.editKnowledge = function(item) {
                $('#modalTitle').html('<i class="bi bi-pencil me-2" style="color: #4f46e5;"></i> Editar Información');
                $('#method').val('PUT');
                $('#editId').val(item.id);

                $('#title').val(item.title);
                $('#category').val(item.category || '');
                $('#text').val(item.text);
                $('#tags').val(item.tags ? item.tags.join(', ') : '');
                $('#active').prop('checked', item.active == 1);
                $('#statusLabel').text(item.active ? 'Activo' : 'Inactivo');

                $('#knowledgeModal').modal('show');
            };

            // ===== ELIMINAR =====
            window.deleteKnowledge = function(id) {
                if (confirm('¿Estás seguro de eliminar esta información? Esta acción no se puede deshacer.')) {
                    $.ajax({
                        url: `{{ url("iaknowledge") }}/${id}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function() {
                            location.reload();
                        },
                        error: function() {
                            alert('Error al eliminar la información');
                        }
                    });
                }
            };

            // ===== TOGGLE STATUS LABEL EN MODAL =====
            $('#active').on('change', function() {
                $('#statusLabel').text($(this).is(':checked') ? 'Activo' : 'Inactivo');
            });

            // ===== RESETEAR MODAL AL CERRAR =====
            $('#knowledgeModal').on('hidden.bs.modal', function() {
                $('#modalTitle').html('<i class="bi bi-plus-circle me-2" style="color: #4f46e5;"></i> Agregar Información');
                $('#method').val('POST');
                $('#editId').val('');
                $('#knowledgeForm')[0].reset();
                $('#active').prop('checked', true);
                $('#statusLabel').text('Activo');
                $('.is-invalid').removeClass('is-invalid');
            });

            // ===== AUTOCOMPLETE OFF =====
            $('input[type=text]').attr('autocomplete', 'off');
        });
    </script>
@endsection
