<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (
            !Gate::check('owner')
            && !Gate::check('admin')
            && !Gate::check('super-admin')
        ) return abort('403');
        if (Gate::check('owner')) {
            $incomes = Income::where('owner_id', Auth::user()->id)->latest()->paginate(10)->onEachSide(2);
        }
        if (Gate::check('admin')) {
            $incomes = Income::where('admin_id', Auth::user()->id)->orWhere('owner_id', Auth::user()->owner_id)->latest()->paginate(10)->onEachSide(2);
        }
        if (Gate::check('super-admin')) {
            $incomes = Income::latest()->paginate(10)->onEachSide(2);
        }

        return view('admin.income.index', compact('incomes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (
            !Gate::check('owner')
            && !Gate::check('admin')
            && !Gate::check('super-admin')
        ) return abort('403');

        return view('admin.income.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (
            !Gate::check('owner')
            && !Gate::check('admin')
            && !Gate::check('super-admin')
        ) return abort('403');
        $this->validate($request, [
            'name'  => 'required|string',
        ]);

        if (Gate::check('owner')) {
            $request->merge([
                'owner_id' =>  Auth::user()->id,
            ]);
        }
        if (Gate::check('admin')) {
            $request->merge([
                'admin_id' =>  Auth::user()->id,
                'owner_id' =>  Auth::user()->owner_id,
            ]);
        }

        Income::create($request->all());

        return redirect()->back()->with('success', 'Berhasil membuat opsi pendapatan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function show(Income $income)
    {
        // if (
        //     !Gate::check('owner')
        //     && !Gate::check('admin')
        // ) return abort('403');
        // return view('admin.income.show', compact('income'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function edit(Income $income)
    {
        if (
            !Gate::check('owner')
            && !Gate::check('admin')
            && !Gate::check('super-admin')
        ) return abort('403');
        return view('admin.income.edit', compact(['income']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Income $income)
    {
        if (
            !Gate::check('owner')
            && !Gate::check('admin')
            && !Gate::check('super-admin')
        ) return abort('403');
        $this->validate($request, [
            'name'  => 'sometimes|string',
        ]);
        $income->update($request->all());

        return redirect()->route('income.index')
            ->with('message', __('Berhasil mengupdate opsi pendapatan.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function destroy(Income $income)
    {
        if (
            !Gate::check('owner')
            && !Gate::check('admin')
            && !Gate::check('super-admin')
        ) return abort('403');
        $income->delete();

        return redirect()->route('income.index')
            ->with('message', __('Berhasil menghapus opsi pendapatan.'));
    }
}
