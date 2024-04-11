<?php

namespace App\Http\Controllers\API;

use App\Helpers\AnyHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Login extends Controller
{
    public function Login(Request $request)
    {
        $resp = array('status' => 'fail');

        try {
            AnyHelper::loginRequest($request);
            $getInfo = AnyHelper::login('/system/identity/print')->read()[0];

            $resp['status'] = 'success';
            $resp['message'] = 'Berhasil Login';
            $resp['data'] = array_merge(AnyHelper::$loginData, $getInfo);

            AnyHelper::saveLog('Berhasil Login Sistem', 'info');
        } catch (\Throwable $th) {
            $resp['message'] = $th->getMessage();
            AnyHelper::saveLog('Gagal Login: ' . $th->getMessage(), 'error');
        }

        return response()->json($resp);
    }
}
