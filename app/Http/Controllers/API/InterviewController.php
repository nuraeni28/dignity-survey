<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\InterviewResource;
use App\Http\Resources\PeriodResource;
use App\Models\Interview;
use App\Models\InterviewData;
use App\Models\InterviewSchedule;
use App\Models\Period;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterviewController extends BaseController
{
    /**
     * Store interview api
     *
     * @return \Illuminate\Http\Response
     */
    public function period(Request $request)
    {
        $period = Period::where('is_active', 1)
            ->whereDate('start_date', '<=', Carbon::today()->toDateString())
            ->whereDate('end_date', '>=', Carbon::today()->toDateString())
            ->first();
        if ($period) {
            return $this->sendResponse(new PeriodResource($period), 'Berhasil mengambil periode aktif');
        } else {
            return $this->sendResponse(null, 'Tidak ditemukan periode aktif');
        }
    }

    /**
     * Store interview api
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $photo = $request->file('photo');
        $photoname = date('YmdHi') . $photo->getClientOriginalName();
        $photo->move(public_path('public/image'), $photoname);

        if ($request->hasFile('record_file')) {
            $record_file = $request->file('record_file');
            $recordname = date('YmdHi') . $record_file->getClientOriginalName();
            $record_file->move(public_path('public/record'), $recordname);
            $input['record_file'] = $recordname;
        }

        $date = Carbon::now();
        $input['admin_id']  = Auth::user()->admin_id;
        $input['owner_id']  = Auth::user()->owner_id;
        $input['photo'] = $photoname;
        $input['interview_date'] = $date;
        $interview = Interview::create($input);


        $schedule = InterviewSchedule::find($request->interview_schedule_id);
           if ($schedule) {
        $schedule->update(['interview_date' => $date]);
    }

        foreach ($input['question'] as $question) {
            $question['interview_id'] = $interview->id;
            InterviewData::create($question);
        }

        return $this->sendResponse(new InterviewResource($interview), 'Berhasil membuat interview');
    }

    public function getAllInterviewData(Request $request)
    {
        $interview = Interview::where('interview_schedule_id', $request->interview_schedule_id)->get();
        return $this->sendResponse(InterviewResource::collection($interview), 'Berhasil mengambil data interview');
    }

    // get all interview data where id between 1 and 100
    public function getInterviewData(Request $request)
    {
        $interview = Interview::where('interview_schedule_id', $request->interview_schedule_id)->whereBetween('id', [1, 100])->get();
        return $this->sendResponse(InterviewResource::collection($interview), 'Berhasil mengambil data interview');
    }
     public function getInterviewDataUser(Request $request)
    {
        $userId = $request->input('user_id');

        $interviews = InterviewSchedule::whereHas('user', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->whereHas('interview')
            ->get();
        // $query = InterviewSchedule::whereHas('user', function ($query) use ($userId) {
        //     $query->where('id', $userId);
        // })->whereHas('interview');

        // dd($query->toSql());

        if ($interviews->isNotEmpty()) {
            $interviewCount = $interviews->count();
            return $this->sendResponse(['Jumlah interview' => $interviewCount], 'Berhasil mengambil data interview');
        } else {
            return $this->sendError('Data tidak ditemukan', 404);
        }
    }
        public function storeSecond(Request $request)
    {
          $input = $request->all();
        $photo = $request->file('photo');
        $photoname = date('YmdHi') . $photo->getClientOriginalName();
        $photo->move(public_path('public/image'), $photoname);

        if ($request->hasFile('record_file')) {
            $record_file = $request->file('record_file');
            $recordname = date('YmdHi') . $record_file->getClientOriginalName();
            $record_file->move(public_path('public/record'), $recordname);
            $input['record_file'] = $recordname;
        }

        $date = Carbon::now();
        $input['admin_id']  = Auth::user()->admin_id;
        $input['owner_id']  = Auth::user()->owner_id;
        $input['photo'] = $photoname;
        $input['interview_date'] = $date;
        $interview = Interview::create($input);


        $schedule = InterviewSchedule::find($request->interview_schedule_id);
           if ($schedule) {
        $schedule->update(['interview_date' => $date]);
    }

        foreach ($input['question'] as $question) {
            $question['interview_id'] = $interview->id;
            InterviewData::create($question);
        }

        return $this->sendResponse(new InterviewResource($interview), 'Berhasil membuat interview');
    }
      public function getInterviewDataResponden(Request $request)
    {
        $customerId = $request->input('customer_id');

        $interviews = InterviewSchedule::whereHas('customer', function ($query) use ($customerId) {
            $query->where('customer_id', $customerId);
        })
            ->whereHas('interview')
            ->get();
        if ($interviews->isNotEmpty()) {

            return $this->sendResponse('Sudah di interview', $interviews);
        } else {
           return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
    }

}
