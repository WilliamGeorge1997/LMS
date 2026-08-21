<?php

namespace App\Exports;

use App\Models\ser;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class sersxport implements FromCollection
{
    public function collection(): Collection
    {
        return ser::all();
    }
}
