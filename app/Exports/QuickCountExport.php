<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuickCountExport implements FromView
{
   
    protected $quickCounts;

    public function __construct($quickCounts)
    {
        $this->quickCounts = $quickCounts;
        //   dd($this->$interview);
    }
  
    public function view(): View
{
    return view('admin.quick-count.export', ['quickCounts' => $this->quickCounts]);
}
}