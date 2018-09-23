<?php
namespace App\Observers;
use App\Mail\sendVerificationCode;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class UserObserver {

    public function created(User $user){
        $code = rand(1000,9999).'-'.rand(1000,9999);
        $user->verification()->create([
            'code'      => $code,
        ]);
    }

    public function deleted(User $user){
        $user->wallet()->delete();
    }

}
