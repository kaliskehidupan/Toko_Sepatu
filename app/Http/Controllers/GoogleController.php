<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        // Gunakan stateless() untuk menghindari masalah session saat redirect ke Google
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            // Gunakan stateless() juga di sini agar lebih stabil
            $user = Socialite::driver('google')->stateless()->user();

            $finduser = User::where('google_id', $user->id)
                            ->orWhere('email', $user->email)
                            ->first();

            if($finduser){
                $finduser->update([
                    'google_id' => $user->id,
                    'name' => $user->name, // Update nama barangkali berubah di Google
                ]);
                Auth::login($finduser, true);
            } else {
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id'=> $user->id,
                    'password' => bcrypt('password123'),
                ]);
                Auth::login($newUser, true);
            }

            // Gunakan redirect()->intended('/') agar balik ke halaman yang diinginkan
            return redirect()->intended('/');

        } catch (Exception $e) {
            // JIKA ERROR, TAMPILKAN DI LAYAR BIAR KITA TAU KENAPA
            dd("Error Login Google: " . $e->getMessage());
        }
    }
}
