<?php

namespace App\Exports;

use App\Models\Saprod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class SaprodExport implements FromCollection, WithHeadings
{

    public function headings(): array
    {
        return [
            'codprod',
            'descrip',
            'descrip2',
            'descrip3',
            'descrip4',
            'precio1',
            'precio2',
            'precio3',
            'costo',
            'referencia',
            'marca',
            'existencia'
        ];
    }

    public function collection()
    {
        $comercial  = session('comercialid') ;
        return Saprod::selectRaw("codprod,descrip,descrip2,descrip3,descrip4,costod as precio1, costod2 as precio2, costod3 as precio3, preciod as costo,
        refere as referencia, marca, newexisten as existencia   ")->where("comercial",$comercial)->orderBy('marca')->get();

    }
}
