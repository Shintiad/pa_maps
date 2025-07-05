<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function show()
    {
        if (auth()->check() && auth()->user()->role == 'superadmin') {
            $admin = User::where('role', 'admin')->paginate(5);
            $about = About::pluck('value', 'part_name')->toArray();
            return view("pages.admin", compact("admin", "about"));
        } else {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melihat halaman admin.');
        }
    }
    public function create()
    {
        if (auth()->check() && auth()->user()->role == 'superadmin') {
            return view("add.add-admin");
        } else {
            return redirect()->route('admin')->with('error', 'Anda tidak memiliki akses untuk melihat halaman admin.');
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'required|max:255|unique:users',
            'password' => 'required|confirmed|min:8',
            'role' => 'required|in:admin',
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

            return redirect()->route('admin')->with('success', 'Data admin berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->route('admin')->with('error', 'Gagal menambahkan data admin: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        if (auth()->check() && auth()->user()->role == 'superadmin') {
            $admin = User::find($id);
            return view("edit.edit-admin", compact("admin"));
        } else {
            return redirect()->route('admin')->with('error', 'Anda tidak memiliki akses untuk melihat halaman admin.');
        }
    }
    public function update(Request $request, $id)
    {
        $admin = User::find($id);

        try {
            $admin->update($request->all());
            return redirect()->route('admin')->with('success', 'Data admin berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->route('admin')->with('error', 'Gagal memperbarui data admin: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        $admin = User::find($id);

        try {
            $admin->delete();

            return redirect()->route('admin')->with('success', 'Data admin berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('admin')->with('error', 'Gagal menghapus data admin: ' . $e->getMessage());
        }
    }
    // public function verifyEmail($id)
    // {
    //     // if (auth()->user()->role !== 1) {
    //     //     return redirect()->route('user')->with('error', 'Anda tidak memiliki izin untuk melakukan tindakan ini.');
    //     // }

    //     $admin = User::find($id);
    //     // $user->email_verified_at = Carbon::now();
    //     // $user->save();

    //     // Check if user exists
    //     if (!$admin) {
    //         return redirect()->route('admin')->with('error', 'admin not found.');
    //     }

    //     // Check if the email is not verified
    //     if (!$admin->hasVerifiedEmail()) {
    //         $admin->sendEmailVerificationNotification();

    //         return redirect()->route('admin')->with('status', 'Verification email sent to ' . $admin->email);
    //     }

    //     return redirect()->route('admin')->with('status', 'Email telah diverifikasi.');
    // }
    public function verifyEmail($id)
    {
        Log::info('Verifying email for admin: ' . $id);

        $admin = User::find($id);

        // Check if admin exists
        if (!$admin) {
            Log::error('User not found: ' . $id);
            return redirect()->route('admin')->with('error', 'Admin not found.');
        }

        // Check if the email is not verified
        if (!$admin->hasVerifiedEmail()) {
            try {
                Log::info('Sending verification email to: ' . $admin->email);
                $admin->sendEmailVerificationNotification();
                Log::info('Email sent successfully');
                return redirect()->route('admin')->with('status', 'Verification email sent to ' . $admin->email);
            } catch (\Exception $e) {
                Log::error('Error sending email: ' . $e->getMessage());
                Log::error('Exception trace: ' . $e->getTraceAsString());
                return redirect()->route('admin')->with('error', 'Gagal mengirim email verifikasi: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin')->with('status', 'Email telah diverifikasi.');
    }
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $about = About::pluck('value', 'part_name')->toArray();

        $admin = User::where('role', 'admin')
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%$keyword%")
                    ->orWhere('email', 'like', "%$keyword%")
                    ->orWhere('phone', 'like', "%$keyword%");
            })
            ->paginate(5);

        return view('pages.admin', ['admin' => $admin], compact('about'))
            ->with('keyword', $keyword);
    }
}
