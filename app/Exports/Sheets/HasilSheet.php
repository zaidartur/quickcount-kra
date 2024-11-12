<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;

class HasilSheet implements FromView, WithTitle
{
    public function view(): View
    {
        return view('template_hasil_suara');
    }

    public function title(): string
    {
        return 'Hasil Suara';
    }
}
