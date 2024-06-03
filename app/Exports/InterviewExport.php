<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class InterviewExport implements FromView
{
   
    protected $interview;

    public function __construct($interview)
    {
        $this->interview = $interview;
        //   dd($this->$interview);
    }
  
    public function view(): View
{
    return view('admin.interview.export', ['interview' => $this->interview]);
}
}