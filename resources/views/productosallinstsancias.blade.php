<style>
    .tdline{
        border:1px solid #0072c5 !important;

    }
    .tdlineff{
        border-left:1px solid #fff !important;

        color: white !important;
        background-color: #0072c5 !important;
    }
</style>

<div class="row">

    <div class=" col-lg-12 ">
        <div class=" row ">
            <div class="card card-height-100">
                <div class="card-header align-items-center text-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">  EXISTENCIAS POR DEPOSITO</h4>
                </div>
                <div class="card-body" data-simplebar style="max-height: 490px;">
                    <table width="1000px" border="0" class="table table-borderless table-centered align-middle table-nowrap mb-0 ">
                        <thead  style="position: sticky; top: 0;">
                        <tr>
                            <td width="4%" height="30" align="center" class="titulo tdlineff  "> COD</td>
                            <td width="20%" align="center" class="titulo tdlineff  "> PRODUCTO  </td>
                            <td width="9%"  align="center" class="titulo tdlineff  "> COSTO  </td>
                            @foreach($deposito as $indexdep => $descripdepo)
                                <td width="13%"  align="center" class="titulo tdlineff   "> {{ $descripdepo }}</td>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $tantos = 0;
                        @endphp

                        @foreach($productos as $index => $producto)
                            @php
                                $tantos++;
                                $bgcolor = "#f2f2f2";
                                if(($tantos%2)==0){ $bgcolor = "#ffffff"; }
                            @endphp

                            <tr  bgcolor="{{$bgcolor}}" style="color:#333;">
                                <td align="center" class="titulo "> {{$index}}</td>
                                <td align="left"   class="titulo  "> {{$producto['descrip'] }}</td>
                                <td align="right"   class="titulo  "> {{ number_format($producto['preciod'],2,',','.') }}</td>
                                @foreach($deposito as $indexdep => $descripdepo)
                                <td align="center" class="titulo ">  {{(isset($existencias[$index][$indexdep]))? $existencias[$index][$indexdep] :'' }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>


