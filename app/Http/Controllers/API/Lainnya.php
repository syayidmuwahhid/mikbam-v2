<?php

namespace App\Http\Controllers\API;

use App\Helpers\AnyHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RouterOS\Query;

class Lainnya extends Controller
{
    public function getClock(Request $request)
    {
        $code = 500;
        $resp = array('status' => 'fail');
        try {
            AnyHelper::loginRequest($request);
            $resp = array(
                'status' => 'success',
                'data' => AnyHelper::login('/system/clock/print')->read()[0]
            );
            $code = 200;
        } catch (\Throwable $th) {
            $resp = array(
                'status' => 'fail',
                'message' => $th->getMessage()
            );
            $code = 400;
        }
        return response()->json($resp, $code);
    }
    
    public function getResource(Request $request)
    {
        $code = 500;
        $resp = array('status' => 'fail');
        try {
            AnyHelper::loginRequest($request);
            $resource = AnyHelper::login('/system/resource/print')->read()[0];
            $routerboard = AnyHelper::login('/system/routerboard/print')->read()[0];
            $resp = array(
                'status' => 'success',
                'data' => array_merge($resource, $routerboard)
            );
            $code = 200;
        } catch (\Throwable $th) {
            $resp = array(
                'status' => 'fail',
                'message' => $th->getMessage()
            );
            $code = 400;
        }
        return response()->json($resp, $code);
    }
    
    public function getLog(Request $request)
    {
        $code = 500;
        $resp = array('status' => 'fail');
        try {
            $resp = array(
                'status' => 'success',
                'data' => AnyHelper::getJson('log')
            );
            $code = 200;
        } catch (\Throwable $th) {
            $resp = array(
                'status' => 'fail',
                'message' => $th->getMessage()
            );
            $code = 400;
        }
        return response()->json($resp, $code);
    }
    
    public function getTraffic(Request $request)
    {
        $code = 500;
        $resp = array('status' => 'fail');
        try {
            AnyHelper::loginRequest($request);
            $response2 = AnyHelper::login('/system/clock/print')->read();
            $q = new Query('/interface/print');
            $q->where('default-name', $request->interface);
            $response4 = AnyHelper::login($q)->read();

            $chart = array(
                'time' => $response2[0]["time"],
                'rx_byte' => number_format($response4[0]["rx-byte"] / 1000000, 2),
                'tx_byte' => number_format($response4[0]["tx-byte"] / 1000000, 2)
            );
            
            $resp = array(
                'status' => 'success',
                'data' => $chart
            );
            $code = 200;
        } catch (\Throwable $th) {
            $resp = array(
                'status' => 'fail',
                'message' => $th->getMessage()
            );
            $code = 400;
        }
        return response()->json($resp, $code);
    }
}
