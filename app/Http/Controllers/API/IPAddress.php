<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Helpers\AnyHelper;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Mockery\Matcher\Any;
use \RouterOS\Client;
use \RouterOS\Query;

class IPAddress extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $code = 200;
        try {
            AnyHelper::loginRequest($request);
            $resp = array(
                'status' => 'success',
                'data' => AnyHelper::login('/ip/address/print')->read()
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $code = 500;
        
        $resp = array(
            'status' => 'fail'
        );

        try {
            $request->validate([
                'interface' => 'required|string',
                'ip' => 'required|string'
            ]);

            AnyHelper::loginRequest($request);

            //cek interface
            $query = new Query('/ip/address/print');
            $query->where('interface', $request->interface);
            $query->where('disabled', 'no');
            $cek = AnyHelper::login($query)->read();

            if ($cek != null){
                $code = 400;
                throw new Error('Interface '. $request->interface .' sudah memiliki IP Address');
            }

            //query
            $query = new Query('/ip/address/add');
            $query->equal('address', $request->ip);
            $query->equal('interface', $request->interface);

            $response = AnyHelper::login($query)->read();

            if (isset($response["after"]["message"])) {
                $code = 400;
                throw new Error($response["after"]["message"]);
            }

            $resp['status'] = 'success';
            $resp['message'] = 'IP Address berhasil ditambahkan';
            $code = 201;
            AnyHelper::saveLog('Berhasil Menambahkan IP Address', 'info');
        } catch (\Throwable $th) {
            $resp['message'] = $th->getMessage();
            AnyHelper::saveLog('Gagal Menambahkan IP Address: ' . $th->getMessage(), 'error');
        }
        return response()->json($resp, $code);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $code = 500;
        try {
            $request->validate([
                'id' => 'required|string'
            ]);

            AnyHelper::loginRequest($request);

            //cek ip
            $query = new Query('/ip/address/print');
            $query->where('.id', $request->id);
            $cekIp = explode('/', AnyHelper::login($query)->read()[0]['address'])[0];

            if ($cekIp == AnyHelper::$loginData['host']) {
                $code = 400;
                throw new Error('Gagal Menghapus, IP Address sedang digunakan untuk login MIKBAM');
            }

            //query
            $query = new Query('/ip/address/remove');
            $query->equal('.id', $request->id);

            $response = AnyHelper::login($query)->read();

            if (isset($response["after"]["message"])) {
                $code = 400;
                throw new Error($response['after']['message']);
            }
            
            $code = 200;
            $resp = array(
                'status' => 'success',
                'message' => 'IP Address berhasil dihapus'
            );
            AnyHelper::saveLog('Berhasil Menghapus IP Address', 'info');
        } catch (\Throwable $th) {
            $resp = array(
                'status' => 'fail',
                'message' => $th->getMessage()
            );
            AnyHelper::saveLog('Gagal Menghapus IP Address: ' . $th->getMessage(), 'error');
        }
        return response()->json($resp, $code);
    }

    public function onoff(Request $request)
    {
        $code = 500;
        try {
            $request->validate([
                'id' => 'required|string',
                'stat' => 'required|string'
            ]);

            AnyHelper::loginRequest($request);

            //cek ip
            $query = new Query('/ip/address/print');
            $query->where('.id', $request->id);
            $cekIp = explode('/', AnyHelper::login($query)->read()[0]['address'])[0];

            if ($cekIp == AnyHelper::$loginData['host']) {
                $code = 400;
                throw new Error('Gagal Merubah Status, IP Address sedang digunakan untuk login MIKBAM');
            }

            //query
            $query = new Query('/ip/address/' . strtolower($request->stat));
            $query->equal('.id', $request->id);

            $response = AnyHelper::login($query)->read();

            if (isset($response["after"]["message"])) {
                $code = 400;
                throw new Error($response["after"]["message"]);
            }

            $code = 200;
            $resp = array(
                'status' => 'success',
                'message' => 'Status berhasil diubah'
            );
            AnyHelper::saveLog('Berhasil Merubah Status IP Address', 'info');
        } catch (\Throwable $th) {
            $resp = array(
                'status' => 'fail',
                'message' => $th->getMessage()
            );
            AnyHelper::saveLog('Gagal Merubah Status IP Address: ' . $th->getMessage(), 'error');
        }
        return response()->json($resp, $code);
    }
}
