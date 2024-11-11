<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vote;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    private $vote;
    private $data;
    private $user;
    private $setting;
    public function __construct() {
        $this->vote = new Vote();
        $this->data = new Data();
        $this->user = new User();
        $this->setting = new Setting();
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function view()
    {
        return Inertia::render('Profile', [
            'apps'   => $this->setting->get_app(),
            'status' => session('status'),
            'detail' => $this->user->detail_user(Auth::user()->uuid),
        ]);
    }

    public function is_update(Request $request) 
    {
        $validated = $request->validate([
            'name'  => 'required|string',
            'uuid'  => 'required|string',
        ]);

        if ($validated) {
            $data = [
                'name'  => $request->name,
            ];

            // $save = $this->user->update_user($data, $request->uuid);
            $save = User::where('uuid', $request->uuid)->update($data);
            if ($save) {
                $status = 'success';
                $msg = 'Data berhasil di update';
            } else {
                $status = 'error';
                $msg = 'Data gagal di update';
            }
        } else {
            $status = 'error';
            $msg = 'Data tidak sesuai';
        }

        $res  = ['status' => $status, 'msg' => $msg];
        return Redirect::route('profile')->with('message', $res);
    }
}
