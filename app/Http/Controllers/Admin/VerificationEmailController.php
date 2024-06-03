<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationEmailController extends Controller
{
 
   use VerifiesEmails;

    public function verify(Request $request)
    {
        // Cek apakah URL verifikasi email valid
        if (! hash_equals((string) $request->route('id'), (string) $request->user()->getKey())) {
            return response()->json(['message' => 'Invalid verification URL'], 400);
        }

        if (! hash_equals((string) $request->get('hash'), sha1($request->user()->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification URL'], 400);
        }

        // Cek apakah pengguna sudah diverifikasi sebelumnya
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email sudah diverifikasi'], 400);
        }

        // Verifikasi email pengguna
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return response()->json(['message' => 'Email berhasil diverifikasi'], 200);
    }
}

