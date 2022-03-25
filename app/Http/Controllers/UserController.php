<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;

class UserController extends Controller
{
    /**
     *
     * @return \Illuminate\View\View
     */
    public function showSettings()
    {
        $user = User::getLoggedInUser()->toJson();
        $company_users = User::getCompanyUsers()->toJson();
        return view('settings', ['user' => $user, 'company_users' => $company_users]);
    }

    public function delete(Request $request)
    {
        $userService = new UserService();
        $response = $userService->deleteUserFromACompany($request->input('userId'));

        if($response) {
            return response()->json(['success' => true, 'message' => 'User deleted successfully!']);
        }


        return response()->json(['success' => false, 'message' => 'Error when deleting the user!']);
    }
}
