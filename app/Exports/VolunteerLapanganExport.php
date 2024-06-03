<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VolunteerLapanganExport implements FromView
{
   
    protected $users;

    public function __construct($users)
    {
        $this->users = $users;
        //   dd($this->$interview);
    }
  
    public function view(): View
{
    return view('admin.volunteer.export', ['users' => $this->users]);
}
}