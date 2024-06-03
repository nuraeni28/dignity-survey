<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreScheduleRequest;
use App\Models\Customer;
use App\Models\InterviewSchedule;
use App\Models\Period;
use App\Models\User;
use Carbon\Carbon;
use App\Models\UserTargetInterviews;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (
            !Gate::check('super-admin')
            && !Gate::check('owner')
            && !Gate::check('admin')
        ) return abort('403');
        $id = Auth::user()->id;
        $schedules = (new InterviewSchedule)->newQuery()->whereHas('user', function ($query) use ($id) {
            $query->where('owner_id', $id)->orWhere('admin_id', $id);
        });;

        if (request()->has('period')) {
            $schedules->where('period_id', '=', request()->input('period'));
        }

        if (request()->query('sort')) {
            $attribute = request()->query('sort');
            $sort_order = 'ASC';
            if (strncmp($attribute, '-', 1) === 0) {
                $sort_order = 'DESC';
                $attribute = substr($attribute, 1);
            }
            $schedules->orderBy($attribute, $sort_order);
        } else {
            $schedules->latest();
        }

        $schedules = $schedules
            ->has('period')
            ->has('user')
            ->has('customer')
            ->with('period')
            ->with('user')
            ->with('customer')
            ->paginate(10)
            ->onEachSide(2);
        $periods = Period::all();
        return view('admin.schedule.index', compact(['schedules', 'periods']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (
            !Gate::check('super-admin')
            && !Gate::check('owner')
            && !Gate::check('admin')
        ) return abort('403');
        $periods = Period::where('end_date', '>=', Carbon::now())->get();
        $users = User::where('admin_id', Auth::user()->id)->orWhere('owner_id', Auth::user()->id)->whereHas(
            'roles',
            function ($q) {
                $q->where('name', 'user');
            }
        )->get();
        $customers = Customer::where('admin_id', Auth::user()->id)->orWhere('owner_id', Auth::user()->id)->get();
        return view('admin.schedule.create', compact(['periods', 'users', 'customers']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreInterviewScheduleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreScheduleRequest $request)
    {
        if (
            !Gate::check('super-admin')
            && !Gate::check('owner')
            && !Gate::check('admin')
        ) return abort('403');
        // dd($request->all());

        if (count($request->input('customer_id')) < $request->input('target_interviews')) {
            return redirect()->route('schedule.create')
                ->with('message', __('Jumlah customer tidak boleh kurang dari target interview.'));
        }

        UserTargetInterviews::create([
            'user_id' => $request->input('user_id'),
            'period_id' => $request->input('period_id'),
            'target_interviews' => $request->input('target_interviews'),
        ]);

        foreach ($request->input('customer_id') as $key => $customer_id) {
            $schedule = new InterviewSchedule;
            $schedule->period_id = $request->input('period_id');
            $schedule->user_id = $request->input('user_id');
            $schedule->customer_id = $customer_id;
            $schedule->save();
        }

        // $data = $request->all();
        // InterviewSchedule::create($data);
        return redirect()->route('schedule.index')
            ->with('message', __('Berhasil membuat jadwal.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InterviewSchedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function show(InterviewSchedule $schedule)
    {
        if (
            !Gate::check('super-admin')
            && !Gate::check('owner')
            && !Gate::check('admin')
        ) return abort('403');
        return view('admin.schedule.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InterviewSchedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function edit(InterviewSchedule $schedule)
    {
        if (
            !Gate::check('super-admin')
            && !Gate::check('owner')
            && !Gate::check('admin')
        ) return abort('403');
        $periods = Period::all();
        return view('admin.schedule.edit', compact(['schedule', 'periods']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\UpdateInterviewScheduleRequest  $request
     * @param  \App\Models\InterviewSchedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InterviewSchedule $schedule)
    {
        if (
            !Gate::check('super-admin')
            && !Gate::check('owner')
            && !Gate::check('admin')
        ) return abort('403');
        $data = $request->all();
        $schedule->update($data);

        return redirect()->route('schedule.index')
            ->with('message', __('Berhasil mengupdate jadwal.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InterviewSchedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(InterviewSchedule $schedule)
    {
        if (
            !Gate::check('super-admin')
            && !Gate::check('owner')
            && !Gate::check('admin')
        ) return abort('403');
        $schedule->delete();

        return redirect()->route('schedule.index')
            ->with('message', __('Berhasil menghapus jadwal.'));
    }
}
