
<style>
    .card-header {
        border-bottom: 1px solid #132659;
    }

    .tdline{
        border:1px solid #0072c5 !important;
    }
    .tdlineff{
        border-left:1px solid #fff !important;
        color: white !important;
        background-color: #0072c5 !important;
        box-shadow: unset !important;
    }
    .tddd{
        font-size: 11px;
        padding: 2px !important;
    }
</style>
    <div class="card-body  ">
            <div class="row ">
                <div class="col-lg-12">
                    <table width="100%" border="0">
                        <tr>
                            <td>
                                <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">    {{$documento->descrip}}</p>
                                <h5 class="fs-15 mb-0"> <a id="invoice-no" href="/compra/{{$documento->id}}"> {{$numerod}}</a></h5>
                            </td>
                            <td>
                                <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">FECHA</p>
                                <h5 class="fs-15 mb-0">
                                    <span id="invoice-date">{{$documento->fechaformat}}</span>
                                </h5>
                            </td>
                            <td>
                                <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Estatus</p>
                                @php
                                    if($documento->status == 0) echo '<span class="badge badge-soft-success" id="payment-status">Cerrada</span>';
                                    if($documento->status == 2) echo '<span class="badge badge-soft-danger"  id="payment-status">Pendiente</span>';
                                    if($documento->status == 1) echo '<span class="badge badge-soft-primary" id="payment-status">Abierta</span>';
                                @endphp
                            </td>
                            <td>
                                <p class="  mb-1" style="color: #132659 !important;"><b>NOTAS1:</b> {{$documento->notas1}} </p>
                                <p class="  mb-1" style="color: #132659 !important;"><b>NOTAS2:</b> {{$documento->notas2}} </p>
                                @if(isset($documento->notas8) and $documento->notas8 != '')
                                    <p class="  mb-1" style="color: #132659 !important;">  {{$documento->notas8}} </p>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-lg-12">
                    <div class="card-body  ">
                        <div class="table-responsive">
                            <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                                <thead>
                                <tr class="table-active">
                                    <th width="1%" class="tdline" >#</th>
                                    <th width="2%" class="text-center tdline">Cod   </th>
                                    <th width="30%" class="text-start tdline">Producto </th>
                                    <th width="10%" class="text-center tdline">Cant </th>
                                    <th width="10%" class="text-center tdline">Costo </th>
                                    <th width="10%" class="text-center tdline">Precio1 </th>
                                    <th width="10%" class="text-center tdline">Precio2 </th>
                                    <th width="10%" class="text-center tdline">Precio3 </th>
                                </tr>
                                </thead>
                                <tbody id="products-list">
                                    @foreach($documento->items as $index => $item)
                                        @if($item->fk_sucursal == $documento->fk_sucursal)
                                            <tr @if(($index%2)!=0) bgcolor="#f5f8fb" @endif>
                                                <td scope="row" class="tdline tddd" valign="top">{{$index+1}}</td>
                                                <td scope="row" class="tdline tddd text-start" valign="top">{{ $item->producto->codprod}}</td>
                                                <td scope="row" class="tdline tddd text-start" valign="top">{{ $item->producto->descrip}}</td>
                                                <td scope="row" class="tdline tddd" valign="top">{{ $item->cantidad}}</td>
                                                <td scope="row" class="tdline tddd text-end" valign="top">{{ $item->preciod}}</td>
                                                <td scope="row" class="tdline tddd text-end" valign="top">{{ $item->costod}}</td>
                                                <td scope="row" class="tdline tddd text-end" valign="top">{{ $item->costod2}}</td>
                                                <td scope="row" class="tdline tddd text-end" valign="top">{{ $item->costod3}}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="hstack gap-2 justify-content-end d-print-none mt-4">
                            @if($documento->status == 1 || $documento->status == 2)
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#cambiarStatusModal">
                                    <i class="ri-refresh-line align-bottom me-1"></i> Cambiar Estatus
                                </button>
                            @endif
                            <a href="javascript:window.print()" class="btn btn-success">
                                <i class="ri-printer-line align-bottom me-1"></i> Print
                            </a>
                        </div>

                    </div>
                </div>
            </div>
    </div>

</div>
