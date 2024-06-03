<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePeriodRequest;
use App\Http\Requests\Admin\UpdatePeriodRequest;
use App\Models\Period;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PeriodController extends Controller
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
        $periods = (new Period)->newQuery();
        $periods->latest();
        $periods = $periods->paginate(10)->onEachSide(2);

        return view('admin.period.index', compact('periods'));
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
        return view('admin.period.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\StorePeriodRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePeriodRequest $request)
    {
        if (
            !Gate::check('super-admin')
            && !Gate::check('owner')
            && !Gate::check('admin')
        ) return abort('403');
        $data = $request->all();
        $data['is_active'] = true;
        $data['created_by'] = Auth::user()->name;
        Period::create($data);

        return redirect()->route('period.index')
            ->with('message', __('Berhasil membuat periode.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Period  $period
     * @return \Illuminate\Http\Response
     */
    public function show(Period $period)
    {
        if (
            !Gate::check('super-admin')
            && !Gate::check('owner')
            && !Gate::check('admin')
        ) return abort('403');
        return view('admin.period.show', compact('period'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Period  $period
     * @return \Illuminate\Http\Response
     */
    public function edit(Period $period)
    {
        if (
            !Gate::check('super-admin')
            && !Gate::check('owner')
            && !Gate::check('admin')
        ) return abort('403');
        return view('admin.period.edit', compact(['period']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\UpdatePeriodRequest  $request
     * @param  \App\Models\Period  $period
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePeriodRequest $request, Period $period)
    {
        if (
            !Gate::check('super-admin')
            && !Gate::check('owner')
            && !Gate::check('admin')
        ) return abort('403');
        $period->update($request->all());

        return redirect()->route('period.index')
            ->with('message', __('Berhasil mengupdate periode.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Period  $period
     * @return \Illuminate\Http\Response
     */
    public function destroy(Period $period)
    {
        if (
            !Gate::check('super-admin')
            && !Gate::check('owner')
            && !Gate::check('admin')
        ) return abort('403');
        $period->delete();

        return redirect()->route('period.index')
            ->with('message', __('Berhasil menghapus periode.'));
    }
}
