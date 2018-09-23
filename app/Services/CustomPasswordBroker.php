<?php
/**
 * Created by PhpStorm.
 * User: Tech2
 * Date: 10/23/2017
 * Time: 4:49 PM
 */

namespace App\Services;

use Illuminate\Auth\Passwords\PasswordBroker as BasePasswordBroker;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class CustomPasswordBroker extends BasePasswordBroker
{

    public function sendResetLink(array $credentials)
    {
        $user = $this->getUser($credentials);
        if (is_null($user)) {
            return static::INVALID_USER;
        }

        if(in_array(last(request()->route()->middleware()),['apiMerchant','api'])){
            //TODO SEND SMS with password Rest Code
            $user->sendPasswordResetNotification(
                $this->tokens->createSmS($user)
            );
        } else {
            $user->sendPasswordResetNotification(
                $this->tokens->create($user)
            );
        }
        return static::RESET_LINK_SENT;
    }




}