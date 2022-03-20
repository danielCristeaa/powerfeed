<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function deleteUserFromACompany($userId) {
        $user = User::getLoggedInUser();
        if(!$user) {
            return;
        }

        if($user->is_admin) {
            User::where('_id', $userId)->delete();
            return true;
        }

        return false;
    }
}
