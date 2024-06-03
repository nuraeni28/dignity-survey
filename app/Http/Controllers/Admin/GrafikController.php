<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\InterviewData;
use App\Models\InterviewSchedule;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrafikController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $address = Interview::select('location')->get();
        $address = $address->pluck('location')->toArray();
        $address = array_unique($address);

        $kota = [];
        $kelurahan = [];
        // dd($address);
        foreach ($address as $key => $value) {
            if ($value != null) {
                $kota[] = explode(',', $value)[2];
                $kelurahan[] = explode(',', $value)[1];
            }
        }

        // dd($address);

        $kota = array_unique($kota);
        $kelurahan = array_unique($kelurahan);
        $hasilInterview = [];
        $questions = Question::where('active', 1)->orderBy('position')->get();

        // $interviews = Interview::selectRaw('location, count(id) as total')->groupBy('location')->get();;

        foreach ($kota as $key => $valueKota) {
            $interview = InterviewSchedule::has('interview')->has('user')->has('customer')->with('interview')->get();
            $dataKota = [];
            foreach ($interview as $key => $value) {
                if ($value->interview->location != null) {
                    $location = explode(',', $value->interview->location);
                    if ($valueKota == $location[2]) {
                        $dataKota[] = $location[2];
                    }
                }
            }

            if (count($dataKota) > 0) {
                $hasilInterview[] = [
                    'name' => $valueKota,
                    'total' => count($dataKota)
                ];
            }
        }

        // dd($hasilInterview);



        if (request()->has('type')) {
            $type = request()->type;
            if ($type == 'kota') {
                
                $hasilInterview = [];
                foreach ($kota as $key => $valueKota) {
                    $interview = InterviewSchedule::has('interview')->has('user')->has('customer')->with('interview')->get();
                    // dd($interview);
                    $dataKota = [];
                    foreach ($interview as $key => $value) {
                        if ($value->interview->location != null) {
                            $location = explode(',', $value->interview->location);
                            // echo $location[2];
                            
                            if ($valueKota == $location[2]) {
                                $dataKota[] = $location[2];
                            }
                        }
                    }

                    // dd($dataKota);
                    // echo count($dataKota);
                    if (count($dataKota) > 0) {
                        $hasilInterview[] = [
                            'name' => $valueKota,
                            'total' => count($dataKota)
                        ];
                    }
                    // dd($hasilInterview);
                }
                // dd($hasilInterview);
            } else if ($type == 'kelurahan') {
                $hasilInterview = [];
                // dd($kelurahan);
                foreach ($kelurahan as $key => $valueKel) {
                    $interview = InterviewSchedule::has('interview')->has('user')->has('customer')->with('interview')->get();
                    $dataKel = [];
                    foreach ($interview as $key => $value) {
                        if ($value->interview->location != null) {
                            $location = explode(',', $value->interview->location);
                            if ($valueKel == $location[1] && $valueKel != " ") {
                                $dataKel[] = $location[1];
                            }
                        }
                    }

                    if (count($dataKel) > 0) {
                        $hasilInterview[] = [
                            'name' => $valueKel,
                            'total' => count($dataKel)
                        ];
                    }
                }

                // dd($hasilInterview);
            }
        }

        // dd($hasilInterview);

        if (request()->has('pertanyaan')) {
            $question = InterviewSchedule::has('interview')
                ->has('user')
                ->has('customer')->with('interview')->get();
            $totalrespondenByQuestion = [];
            $question_data = Question::find(request()->pertanyaan);
            
            if ($question_data->type == 'option') {
                $str = explode(',', str_replace('[', '', str_replace('"', '', str_replace(']', '', $question_data->answer))));
                // dd($str);
                foreach ($str as $key => $qd) {
                    $question = InterviewSchedule::has('interview')->has('user')->has('customer')->with('interview')->get();
                    //  dd($question);
                    $data = [];
                    foreach ($question as $key => $value) {
                        foreach ($value->interview->data as $key => $value) {
                            // dd($qd);
                            if ($value->question_id == request()->pertanyaan) {
                                if ($value->customer_answer == $qd) {
                                    // echo $value->customer_answer;
                                    $data[] = $value->customer_answer;
                                }
                            }
                        }
                    }
                    $totalrespondenByQuestion[] = [
                        'question' => $question_data->question,
                        'answer' => $qd,
                        'total' => count($data)
                    ];
                }
                //    dd($totalrespondenByQuestion);
            } elseif ($question_data->type == 'multiple') {
                $totalrespondenByQuestion = [];

                $question = InterviewSchedule::has('interview')->has('user')->has('customer')->with('interview')->get();
                $data = [];
                foreach ($question as $key => $value) {
                    foreach ($value->interview->data as $key => $value) {
                        if ($value->question_id == $question_data->id) {
                            if ($value->customer_answer != null) {
                                $str = explode(',', str_replace('[', '', str_replace('"', '', str_replace(']', '', $value->customer_answer))));
                                foreach ($str as $key => $value) {
                                    $data[] = $value;
                                }
                            } else {
                                $data[] = 'Tidak ada jawaban';
                            }
                        }
                    }
                }

                $uniqueData = array_unique($data);
                foreach ($uniqueData as $key => $value) {
                    $totalrespondenByQuestion[] = [
                        'question' => $question_data->question,
                        'answer' => $value,
                        'total' => count(array_keys($data, $value))
                    ];
                }
            } elseif ($question_data->type == 'rating') {
                $question = InterviewData::where('question_id', request()->pertanyaan)->selectRaw('customer_answer, count(id) as total')->groupBy('customer_answer')->get();
                // dd($question);
                foreach ($question as $key => $value) {
                    $totalrespondenByQuestion[] = [
                        'question' => $question_data->question,
                        'answer' => $value->customer_answer,
                        'total' => $value->total
                    ];
                }

                $question = InterviewSchedule::has('interview')->has('user')->has('customer')->with('interview')->get();
                $data = [];
                foreach ($question as $key => $value) {
                    foreach ($value->interview->data as $key => $value) {
                        if ($value->question_id == $question_data->id) {
                            //   dd($value->customer_answer);
                            if ($value->customer_answer != null) {
                                $str = explode(',', str_replace('[', '', str_replace('"', '', str_replace(']', '', $value->customer_answer))));
                                foreach ($str as $key => $value) {
                                    $data[] = $value;
                                }
                            } else {
                                $data[] = 'Tidak ada jawaban';
                            }
                        }
                    }
                }

                $uniqueData = array_unique($data);
                $totalrespondenByQuestion = [];
                foreach ($uniqueData as $key => $value) {
                    $totalrespondenByQuestion[] = [
                        'question' => $question_data->question,
                        'answer' => $value,
                        'total' => count(array_keys($data, $value))
                    ];
                }
            }
        } else {
            $question = InterviewSchedule::has('interview')
                ->has('user')
                ->has('customer')->with('interview')->get();
            $totalrespondenByQuestion[] = [];
            $question_data = Question::all()->first();
            if ($question_data->type == 'option') {
                $str = explode(',', str_replace('[', '', str_replace('"', '', str_replace(']', '', $question_data->answer))));
                foreach ($str as $key => $qd) {
                    $question = InterviewSchedule::has('interview')->has('user')->has('customer')->with('interview')->get();
                    //  dd($question);
                    $data = [];
                    foreach ($question as $key => $value) {
                        foreach ($value->interview->data as $key => $value) {
                            if ($value->question_id == $question_data->id) {
                                if ($value->customer_answer == $qd) {
                                    $data[] = $value->customer_answer;
                                }
                            }
                        }
                    }
                    $totalrespondenByQuestion[] = [
                        'question' => $question_data->question,
                        'answer' => $qd,
                        'total' => count($data)
                    ];
                }
            }
        }

        // dd(count($question));

        // dd($totalrespondenByQuestion);

        return view('admin.grafik.index', compact('hasilInterview', 'questions', 'totalrespondenByQuestion'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
