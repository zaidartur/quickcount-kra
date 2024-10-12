<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TemplateDownload implements FromView
{
    // use Exportable;

    private $type;
    public function __construct($type) {
        $this->type = $type;
    }

    public function view(): View
    {
        if ($this->type == 'dpt') {
            return view('template_dpt');
        } elseif ($this->type == 'kecamatan') {
            return view('template_dpt');
        } elseif ($this->type == 'desa') {
            return view('template_dpt');
        }
    }
}
