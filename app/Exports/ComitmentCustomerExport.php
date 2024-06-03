<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ComitmentCustomerExport implements FromView
{
   
    protected $customers;

    public function __construct($customers)
    {
        $this->customers = $customers;
        //   dd($this->$interview);
    }
  
    public function view(): View
{
    return view('admin.customer.export', ['customers' => $this->customers]);
}
}