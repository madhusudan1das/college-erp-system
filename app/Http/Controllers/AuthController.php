<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = trim($request->input('username'));
        $password = trim($request->input('password'));

        $user = User::with('role')
            ->where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $request->has('remember'));
            $request->session()->regenerate();
            
            // Set session variables matching legacy code if needed
            session([
                'user_id' => $user->id,
                'username' => $user->username,
                'role' => $user->role->name,
                'role_id' => $user->role_id,
            ]);

            return $this->redirectUser($user);
        }

        return back()->with('error', 'Invalid credentials.')->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectUser($user)
    {
        $roleName = $user->role->name;

        if ($roleName === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($roleName === 'faculty') {
            return redirect()->route('faculty.dashboard');
        } elseif ($roleName === 'student') {
            return redirect()->route('student.dashboard');
        }

        return redirect()->route('login');
    }

    // Change Password Actions
    public function changePasswordForm()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->with('error', 'Your current password does not match our records.');
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return back()->with('success', 'Password updated successfully!');
    }

    // Forgot Password Actions
    public function forgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function forgotPasswordVerify(Request $request)
    {
        $request->validate([
            'role' => 'required|in:faculty,student',
            'username_or_email' => 'required|string',
            'verification_info' => 'required|string',
        ]);

        $role = $request->input('role');
        $usernameOrEmail = trim($request->input('username_or_email'));
        $verificationInfo = trim($request->input('verification_info'));

        // Find user
        $user = User::whereHas('role', function ($q) use ($role) {
            $q->where('name', $role);
        })
        ->where(function ($q) use ($usernameOrEmail) {
            $q->where('username', $usernameOrEmail)->orWhere('email', $usernameOrEmail);
        })
        ->first();

        if (!$user) {
            return back()->with('error', 'No matching user account found with those credentials.')->withInput();
        }

        // Verify based on role details
        $verified = false;
        if ($role === 'faculty') {
            $faculty = \App\Models\Faculty::where('user_id', $user->id)->first();
            if ($faculty && trim($faculty->phone) === $verificationInfo) {
                $verified = true;
            }
        } elseif ($role === 'student') {
            $student = \App\Models\Student::where('user_id', $user->id)->first();
            if ($student && trim($student->enrollment_no) === $verificationInfo) {
                $verified = true;
            }
        }

        if (!$verified) {
            $msg = $role === 'faculty' ? 'Phone number verification failed.' : 'Enrollment number verification failed.';
            return back()->with('error', $msg)->withInput();
        }

        // Save verification in session
        session(['reset_user_id' => $user->id]);

        return redirect()->route('password.reset');
    }

    public function resetPasswordForm()
    {
        if (!session()->has('reset_user_id')) {
            return redirect()->route('password.forgot')->with('error', 'Please verify your identity first.');
        }
        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        if (!session()->has('reset_user_id')) {
            return redirect()->route('password.forgot')->with('error', 'Session expired. Please verify again.');
        }

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $userId = session('reset_user_id');
        $user = User::findOrFail($userId);
        
        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Clear session
        session()->forget('reset_user_id');

        return redirect()->route('login')->with('success', 'Your password has been reset successfully! Please login with your new password.');
    }

    public function markNotificationRead($id)
    {
        $notification = \App\Models\AppNotification::where('user_id', \Illuminate\Support\Facades\Auth::id())->findOrFail($id);
        $notification->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }
}
