<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\TestingOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TestingOtpController extends Controller
{
    public function index()
    {
        if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('super-admin')) {
            return abort('403');
        }

        $otps = TestingOtp::latest()
            ->paginate(10)
            ->onEachSide(2);

        return view('admin.otp.index', compact('otps'));
    }
    public function create()
    {
        if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('super-admin')) {
            return abort('403');
        }

        return view('admin.otp.create');
    }

    public function store(Request $request)
    {
        if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('super-admin')) {
            return abort('403');
        }
        $this->validate($request, [
            'nama' => ['required', 'string', 'max:255'],
            'number_phone' => ['nullable', 'numeric', 'digits_between:8,14'],
        ]);

        $request->merge(['user_id' => Auth::user()->id]);

        TestingOtp::create($request->all());

        return redirect()
            ->back()
            ->with('success', 'Berhasil membuat testing otp');
    }
    public function edit(TestingOtp $otp)
    {
        return view('admin.otp.edit', compact('otp'));
    }
    public function update(Request $request, $id)
    {
        $otp = TestingOtp::findOrFail($id);

        $this->validate($request, [
            'nama' => ['required', 'string', 'max:255'],
            'number_phone' => ['nullable', 'numeric', 'digits_between:8,14'],
        ]);
        $otp->update($request->only(['nama', 'number_phone']));

        return redirect()
            ->back()
            ->with('success', 'Berhasil mengupdate testing otp');
    }
    public function destroy($id)
    {
        if ($roles = Auth::user()->roles) {
            foreach ($roles as $role) {
                if ($role->name !== 'admin' && $role->name !== 'super-admin' && $role->name !== 'owner') {
                    return abort(403);
                }
            }
        }
        $otp = TestingOtp::find($id);
        $otp->delete();
        return redirect()
            ->back()
            ->with('success', 'Berhasil menghapus testing otp');
    }
}
