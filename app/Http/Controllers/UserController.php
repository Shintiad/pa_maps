<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Notifications\VerifyEmailNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function show()
    {
        if (auth()->check() && (auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin')) {
            $user = User::where('role', 'user')->paginate(5);
            $about = About::pluck('value', 'part_name')->toArray();
            return view("pages.user", compact("user", "about"));
        } else {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melihat halaman user.');
        }
    }
    public function create()
    {
        if (auth()->check() && (auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin')) {
            return view("add.add-user");
        } else {
            return redirect()->route('user')->with('error', 'Anda tidak memiliki akses untuk melihat halaman user.');
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'required|max:255|unique:users',
            'password' => 'required|confirmed|min:8',
            'role' => 'required|in:user',
        ]);

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'email_verified_at' => now(),
            ]);

            return redirect()->route('user')->with('success', 'Data user berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->route('user')->with('error', 'Gagal menambahkan data user: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        if (auth()->check() && (auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin')) {
            $user = User::find($id);
            return view("edit.edit-user", compact("user"));
        } else {
            return redirect()->route('user')->with('error', 'Anda tidak memiliki akses untuk melihat halaman user.');
        }
    }
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        try {
            $user->update($request->all());
            return redirect()->route('user')->with('success', 'Data user berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->route('user')->with('error', 'Gagal memperbarui data user: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        $user = User::find($id);

        try {
            $user->delete();

            return redirect()->route('user')->with('success', 'Data user berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('user')->with('error', 'Gagal menghapus data user: ' . $e->getMessage());
        }
    }
    public function verifyEmail($id)
    {
        Log::info('Verifying email for user: ' . $id);

        $user = User::find($id);

        // Check if user exists
        if (!$user) {
            Log::error('User not found: ' . $id);
            return redirect()->route('user')->with('error', 'User not found.');
        }

        // Check if the email is not verified
        if (!$user->hasVerifiedEmail()) {
            try {
                Log::info('Sending verification email to: ' . $user->email);
                $user->sendEmailVerificationNotification();
                Log::info('Email sent successfully');
                return redirect()->route('user')->with('status', 'Verification email sent to ' . $user->email);
            } catch (\Exception $e) {
                Log::error('Error sending email: ' . $e->getMessage());
                Log::error('Exception trace: ' . $e->getTraceAsString());
                return redirect()->route('user')->with('error', 'Gagal mengirim email verifikasi: ' . $e->getMessage());
            }
        }

        return redirect()->route('user')->with('status', 'Email telah diverifikasi.');
    }
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $about = About::pluck('value', 'part_name')->toArray();

        $user = User::where('role', 'user')
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%$keyword%")
                    ->orWhere('email', 'like', "%$keyword%")
                    ->orWhere('phone', 'like', "%$keyword%");
            })
            ->paginate(5);

        return view('pages.user', ['user' => $user], compact('about'))
            ->with('keyword', $keyword);
    }
}
