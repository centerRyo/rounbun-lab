<?php

namespace App\Http\Controllers;

use App\Job;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function mypage()
    {
        $ronbuns = Auth::user()->ronbuns()->get();
        return view('mypage', compact('ronbuns'));
    }

    public function edit()
    {
        $jobs = Job::select('id', 'name')->get()->all();
        $job_names = [];
        foreach ($jobs as $job) {
            $job_names[$job->id] = $job->name;
        }

        $roles = Role::select('id', 'name')->get()->all();
        $role_names = [];
        foreach ($roles as $role) {
            $role_names[$role->id] = $role->name;
        }
        return view('user.edit', compact('job_names', 'role_names'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'max:255',
            'pic' => 'file|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();
        $user->fill($request->all())->save();

        // 画像データは別で上書き保存する
        if (isset($request->pic)) {
            $file = $request->pic;
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $target_path = public_path('/uploads/');
            $file->move($target_path, $fileName);
            $user->fill(['pic' => $fileName])->save();
        }

        return redirect('/mypage')->with('flash_message', __('Updated!'));
    }

    public function withdraw()
    {
        return view('withdraw');
    }

    public function delete()
    {
        Auth::user()->delete();

        return redirect('/register');
    }
}
