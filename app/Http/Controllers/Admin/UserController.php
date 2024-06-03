<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\User\CreateUser;
use App\Actions\Admin\User\UpdateUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\InterviewSchedule;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:user list', ['only' => ['index', 'show']]);
        $this->middleware('can:user create', ['only' => ['create', 'store']]);
        $this->middleware('can:user edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:user delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = (new User)->newQuery();

        if (request()->has('search')) {
            $users->where('name', 'Like', '%' . request()->input('search') . '%');
        }

        if (request()->query('sort')) {
            $attribute = request()->query('sort');
            $sort_order = 'ASC';
            if (strncmp($attribute, '-', 1) === 0) {
                $sort_order = 'DESC';
                $attribute = substr($attribute, 1);
            }
            $users->orderBy($attribute, $sort_order);
        } else {
            $users->latest();
        }

        $users = $users->whereHas(
            'roles',
            function ($q) {
                $q->where('name', 'user');
            }
        )->with('targetInterview', 'doneInterviews')->paginate(10)->onEachSide(2);

        // dd($users);

        return view('admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();

        return view('admin.user.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreUserRequest  $request
     * @param  \App\Actions\Admin\User\CreateUser  $createUser
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request, CreateUser $createUser)
    {
        $createUser->handle($request);

        return redirect()->route('user.index')
            ->with('message', __('Berhasil membuat pengguna.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $interviews = (new InterviewSchedule())->newQuery();
        $interviews->where('user_id', '=', $user->id);
        $interviews = $interviews

            ->has('user')
            ->has('customer')
            ->paginate(10)
            ->onEachSide(2);
        $customers = InterviewSchedule::where('user_id', '=', $user->id)

            ->has('user')
            ->has('customer')
            ->latest()
            ->get()
            ->unique('customer_id');
        $user->load('targetInterview');
        $user->load('doneInterviews');
        // dd($user->doneInterviews);

        return view('admin.user.show', compact('user', 'interviews', 'customers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userHasRoles = array_column(json_decode($user->roles, true), 'id');

        return view('admin.user.edit', compact('user', 'roles', 'userHasRoles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\UpdateUserRequest  $request
     * @param  \App\Models\User  $user
     * @param  \App\Actions\Admin\User\UpdateUser  $updateUser
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user, UpdateUser $updateUser)
    {
        $updateUser->handle($request, $user);

        return redirect()->route('user.index')->with('message', __('Berhasil mengupdate pengguna.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function verify(User $user)
    {
        $user->update([
            'email_verified_at' => Carbon::now(),
        ]);
        return redirect()->route('user.index')
            ->with('message', __('Berhasil menverifikasi pengguna.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('user.index')
            ->with('message', __('Berhasil menghapus pengguna.'));
    }

    /**
     * Show the user a form to change their personal information & password.
     */
    public function accountInfo()
    {
        $user = Auth::user();

        return view('admin.user.account_info', compact('user'));
    }

    public function changePassword()
    {
        return view('admin.user.change_password');
    }

    /**
     * Save the modified personal information for a user.
     */
    public function accountInfoStore(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::user()->id],
            'address' => ['required', 'string'],
            'nik' => ['required', 'numeric', 'digits:16', 'unique:users,nik,' . Auth::user()->id],
            'phone' => ['required', 'numeric', 'digits_between:8,14'],
        ]);

        $user = Auth::user()->update($request->except(['_token']));

        if ($user) {
            $message = 'Account updated successfully.';
            return redirect()->route('admin.account.info')->with('success', __($message));
        } else {
            $message = 'Error while saving. Please try again.';
            return redirect()->route('admin.account.info')->with('error', __($message));
        }
    }

    /**
     * Save the new password for a user.
     */
    public function changePasswordStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => ['required'],
            'new_password' => ['required', Password::defaults()],
            'confirm_password' => ['required', 'same:new_password', Password::defaults()],
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!Hash::check($request->input('old_password'), Auth::user()->password)) {
                $validator->errors()->add('old_password', __('Password Lama Salah.'));
            }
        });

        if ($validator->fails()) {
            return redirect()->route('admin.account.password')->withErrors($validator);
        }

        $validator->validateWithBag('password');

        if ($validator->fails()) {
            return redirect()->route('admin.account.password')->withErrors($validator);
        }

        $user = Auth::user()->update([
            'password' => Hash::make($request->input('new_password')),
        ]);

        if ($user) {
            $message = 'Password updated successfully.';
            return redirect()->route('admin.account.password')->with('success', __($message));
        } else {
            $message = 'Error while saving. Please try again.';
            return redirect()->route('admin.account.password')->with('error', __($message));
        }
    }
}
