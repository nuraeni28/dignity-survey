<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PerformaVolunteerExport implements FromView
{
   
    protected $interviews;

    public function __construct($interviews)
    {
        $this->interviews = $interviews;
        //   dd($this->$interview);
    }
  
    public function view(): View
{
    return view('admin.performa.export', ['interviews' => $this->interviews]);
}
}