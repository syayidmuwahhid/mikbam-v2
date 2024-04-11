<?php

namespace App\Http\Controllers\API;

use App\Helpers\AnyHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RouterOS\Query;

class Interfaces extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $code = 500;
        try {
            $code = 200;
            AnyHelper::loginRequest($request);
            $resp = array(
                'status' => 'success',
                'data' => AnyHelper::login('/interface/print')->read()
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
    
    public function getIP(Request $request)
    {
        $code = 500;
        try {
            $code = 200;
            AnyHelper::loginRequest($request);

            $query = new Query('/ip/address/print');
            $query->where('interface', $request->interface);
            $query->where('disabled', 'no');

            $resp = array(
                'status' => 'success',
                'data' => AnyHelper::login($query)->read()
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
        //
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
    public function destroy(string $id)
    {
        //
    }
}
