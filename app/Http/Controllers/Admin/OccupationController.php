<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Occupation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class OccupationController extends Controller
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
            $occupations = Occupation::where('owner_id', Auth::user()->id)->latest()->paginate(10)->onEachSide(2);
        }
        if (Gate::check('admin')) {
            $occupations = Occupation::where('admin_id', Auth::user()->id)->orWhere('owner_id', Auth::user()->owner_id)->latest()->paginate(10)->onEachSide(2);
        }
        if (Gate::check('super-admin')) {
            $occupations = Occupation::latest()->paginate(10)->onEachSide(2);
        }

        return view('admin.occupation.index', compact('occupations'));
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
        ) return abort('403');

        return view('admin.occupation.create');
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

        Occupation::create($request->all());

        return redirect()->back()->with('success', 'Berhasil membuat opsi pekerjaan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Occupation  $occupation
     * @return \Illuminate\Http\Response
     */
    public function show(Occupation $occupation)
    {
        // if (
        //     !Gate::check('owner')
        //     && !Gate::check('admin')
        // ) return abort('403');
        // return view('admin.occupation.show', compact('occupation'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Occupation  $occupation
     * @return \Illuminate\Http\Response
     */
    public function edit(Occupation $occupation)
    {
        if (
            !Gate::check('owner')
            && !Gate::check('admin')
            && !Gate::check('super-admin')
        ) return abort('403');
        return view('admin.occupation.edit', compact(['occupation']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Occupation  $occupation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Occupation $occupation)
    {
        if (
            !Gate::check('owner')
            && !Gate::check('admin')
            && !Gate::check('super-admin')
        ) return abort('403');
        $this->validate($request, [
            'name'  => 'sometimes|string',
        ]);
        $occupation->update($request->all());

        return redirect()->route('occupation.index')
            ->with('message', __('Berhasil mengupdate opsi pekerjaan.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Occupation  $occupation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Occupation $occupation)
    {
        if (
            !Gate::check('owner')
            && !Gate::check('admin')
            && !Gate::check('super-admin')
        ) return abort('403');
        $occupation->delete();

        return redirect()->route('occupation.index')
            ->with('message', __('Berhasil menghapus opsi pekerjaan.'));
    }
}
