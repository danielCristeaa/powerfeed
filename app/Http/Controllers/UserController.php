<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     *
     * @return \Illuminate\View\View
     */
    public function showSettings()
    {
        $userId = Auth::id();
        $user = User::where('_id', $userId)->first()->toJson();
        return view('settings', ['user' => $user]);
    }

    /**
     *
     * @param Request $request
     */
    public function uploadConfigFile(Request $request)
    {
        $userId = Auth::id();
        $user = User::where('_id', $userId)->first();

        if($request->file('file'))
        {
            $file = $request->file('file');
            $content = file_get_contents($file->getRealPath());
            $json_content = json_decode($content);
            $user->config = $json_content;
            $user->configFileName = $request->input('fileName');
        }
        if($request->input('merchantId'))
        {
            $user->merchantId = $request->input('merchantId');
        }
        $user->save();
        return json_encode($user);
    }
}
