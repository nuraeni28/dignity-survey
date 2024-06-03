<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupporterExport implements FromView
{
   
    protected $supporters;

    public function __construct($supporters)
    {
        $this->supporters = $supporters;
        //   dd($this->$interview);
    }
  
    public function view(): View
{
    return view('admin.supporter.export', ['supporters' => $this->supporters]);
}
}