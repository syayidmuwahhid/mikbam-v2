<?php

namespace App\Http\Controllers\API;

use App\Helpers\AnyHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DNS extends Controller
{
    public function index(Request $request)
    {
        $code = 500;
        try {
            $code = 200;
            AnyHelper::loginRequest($request);

            $dataDNS = array();
            $dns = AnyHelper::login('/ip/dns/print')->read();
            $dnsStatic = explode(',', $dns[0]['servers']);
            $dnsDynamic = explode(',', $dns[0]['dynamic-servers']);

            foreach($dnsStatic as $ds) {
                if ($ds != "") {
                    array_push($dataDNS, $ds);
                }
            }
            
            foreach($dnsDynamic as $dd) {
                if ($dd != "") {
                    array_push($dataDNS, $dd);
                }
            }
            $resp = array(
                'status' => 'success',
                'data' => $dataDNS
            );
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
