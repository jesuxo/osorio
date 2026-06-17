@extends('layouts.master')
@section('title')
    Productos
@endsection
@section('css')
    <style>
        .botoncal{
            background: transparent;
            border: none;
            color: white;
        }
        .botoncal:hover{
            font-size: 13px;
        }

        #clearall{
            text-decoration: none !important;
        }
        .error {
            border: 2px solid red !important;
            background-color: #ffe6e6 !important;
        }

        .error:focus {
            outline: none;
            border-color: #ff0000;
            box-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
        }
    </style>
@endsection
@section('content')

    <div class="row">

        <!-- end col -->
        @if(isset($serial) and isset($codprod) and $serial !='')
            <div class="col-xxl-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">Historial de transacciones del serial {{$serial}}</h5>
                        </div>
                    </div>
                    <div class="card-body">


                        <div class="tab-content">
                            <div class="tab-pane active"   role="tabpanel">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive table-card mb-1">
                                            <table class="table table-borderless table-striped align-middle table-sm fs-14 mb-0">
                                                <thead class="text-muted table-light">
                                                <tr>
                                                    <th width="5%"  scope="col">  Operaci&oacute;n  </th>
                                                    <th  width="5%" scope="col" style="text-align: center !important" align="center">Fecha</th>
                                                    <th  width="10%"scope="col" style="text-align: center !important" align="center">Documento</th>
                                                    <th  width="20%"scope="col" style="text-align: center !important" align="center">Sucursal</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php
                                                    $arraysucu = [];
                                                    foreach ($sucursales as $suc){
                                                        $arraysucu[$suc->id] = $suc->descrip;
                                                    }
                                                @endphp
                                                   @foreach($operacionesrep as $index => $row)
                                                       <tr>
                                                           <td align="left">
                                                               @php
                                                                   if($row->tipo == 'A') echo "Factura";
                                                                   if($row->tipo == 'Z') echo "Factura";
                                                                   if($row->tipo == 'B') echo "DevFac";
                                                                   if($row->tipo == 'W') echo "DevFac";
                                                                   if($row->tipo == 'H') echo "Compra";
                                                                   if($row->tipo == 'U') echo "Compra";
                                                                   if($row->tipo == 'I') echo "DevComp";
                                                                   if($row->tipo == 'Y') echo "DevComp";
                                                                   if($row->tipo == 'P') echo "Descargo";
                                                                   if($row->tipo == 'K') echo "Descargo";
                                                                   if($row->tipo == 'O') echo "Cargo";
                                                                   if($row->tipo == 'T') echo "Cargo";
                                                                   if($row->tipo == 'N') echo "Traslado";
                                                                   if($row->tipo == 'S') echo "Traslado";
                                                               @endphp
                                                           </td>
                                                           <td align="center">{{(isset($row->fecha)? $row->fecha: '')}}</td>
                                                           <td align="center">
                                                               @if(  $row->tipo == 'A'  or $row->tipo == 'B'  or $row->tipo == 'Z'  or $row->tipo == 'W'  )
                                                                  <a href="/doc/{{$row->tipo}}/{{$row->numerod}}/{{$row->fk_sucursal}}" target="_blank"> {{$row->numerod}} </a>
                                                               @else
                                                                   {{$row->numerod}}
                                                               @endif
                                                           </td>
                                                           <td align="right">{{$arraysucu[$row->fk_sucursal]}}</td>
                                                       </tr>
                                                   @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endif
    </div>

@endsection
@section('scripts')
    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
