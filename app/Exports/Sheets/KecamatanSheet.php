<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class KecamatanSheet implements FromView, WithTitle
{
    private $kec;
    private $name;
    public function __construct($kec, $name)
    {
        $this->kec = $kec;
        $this->name = $name;
    }

    public function view(): View
    {
        return view('');
    }

    public function title(): string
    {
        return $this->name;
    }
}
