<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ChangePasswordController extends Controller
{
    /**
     * Display the change password form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('pages.auth.change_password', []);
    }

    /**
     * Update the user's password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', Password::min(8)->mixedCase()->numbers()->symbols()],
            'confirm_password' => ['required', 'same:new_password'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.mixed' => 'Password baru harus mengandung huruf besar dan kecil.',
            'new_password.numbers' => 'Password baru harus mengandung angka.',
            'new_password.symbols' => 'Password baru harus mengandung simbol.',
            'confirm_password.required' => 'Konfirmasi password wajib diisi.',
            'confirm_password.same' => 'Konfirmasi password tidak sesuai dengan password baru.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Get the authenticated user
            $user = auth()->user();

            // Update the user's password
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah password.',
            ], 500);
        }
    }
}
