<?php

namespace App\Http\Controllers\API;

use App\Helpers\AnyHelper;
use App\Http\Controllers\Controller;
use Error;
use Illuminate\Http\Request;
use RouterOS\Query;

class DHCPServer extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $code = 200;
        try {
            AnyHelper::loginRequest($request);
            $response = AnyHelper::login('/ip/dhcp-server/print')->read();
            $dhcpData = [];

            if ($response) {
                foreach ($response as $dhcp ) {
                    //get IP
                    $query = new Query('/ip/address/print');
                    $query->where("interface", $dhcp["interface"]);
                    // $query->where("disabled", "no");
                    $getip = AnyHelper::login($query)->read();

                    $dhcp["network"] = $getip[0]["network"];
                    $dhcp["gateway"] = explode("/", $getip[0]["address"])[0];

                    //getAddressPool
                    $query = new Query('/ip/pool/print');
                    $query->where("name", $dhcp["address-pool"]);
                    $dhcp['pool'] = "";
                    foreach (explode(',', AnyHelper::login($query)->read()[0]['ranges']) as $p) {
                        $dhcp['pool'] .= $p . "\n";
                    }

                    array_push($dhcpData, $dhcp);
                }
            }

            $resp = array(
                'status' => 'success',
                'data' => $dhcpData
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
                'ip' => 'required|string',
                'network' => 'required|string',
                'pool' => 'required|string',
            ]);

            AnyHelper::loginRequest($request);

            $interface = $request->interface;
            $rawip = explode("/", $request->ip);
            $network = $request->network . "/" . $rawip[1];
            $gateway = $rawip[0];

            //cekpool
            $query = new Query('/ip/pool/print');
            $query->where('ranges', $request->pool);
            $cek_pool = AnyHelper::login($query)->read();

            if (!empty($cek_pool)) {
                $poolName = $cek_pool[0]["name"];
            } else {
                //addpool
                $query = new Query('/ip/pool/add');
                $query->equal('ranges', $request->pool);
                $pool = AnyHelper::login($query)->read();

                if (isset($pool["after"]["message"])) {
                    $code = 400;
                    throw new Error($pool["after"]["message"]);
                } 
                
                //getPoolname
                $query = new Query('/ip/pool/print');
                $query->where('ranges', $request->pool);
                $poolName = AnyHelper::login($query)->read()[0]['name'];
                
            }

            //addDHCPNetwork
            $query = new Query('/ip/dhcp-server/network/add');
            $query->equal('address', $network);
            $query->equal('gateway', $gateway);
            $query->equal('dns-server', ($request->dns != null) ? $request->dns : "");
            $dhcpn = AnyHelper::login($query)->read();

            if (isset($dhcpn["after"]["message"])) {
                $code = 400;
                throw new Error($dhcpn["after"]["message"]);
            }

            //addDHCPServer
            $query = new Query('/ip/dhcp-server/add');
            $query->equal('interface', $interface);
            $query->equal('lease-time', "1d");
            $query->equal('disabled', "no");
            $query->equal('address-pool', $poolName);
            $dhcp = AnyHelper::login($query)->read();

            if (isset($dhcp["after"]["message"])) {
                $code = 400;
                throw new Error($dhcp["after"]["message"]);
            }

            $resp['status'] = 'success';
            $resp['message'] = 'DHCP Server berhasil ditambahkan';
            $code = 201;
            AnyHelper::saveLog('Berhasil Menambahkan DHCP Server', 'info');
        } catch (\Throwable $th) {
            $resp['message'] = $th->getMessage();
            AnyHelper::saveLog('Gagal Menambahkan DHCP Server: ' . $th->getMessage(), 'error');
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

            //get gateway
            $query = new Query('/ip/dhcp-server/print');
            $query->where('.id', $request->id);
            $getData = AnyHelper::login($query)->read()[0];
            $query = new Query('/ip/address/print');
            $query->where('interface', $getData['interface']);
            $gateway = explode('/', AnyHelper::login($query)->read()[0]['address'])[0];
            $addressPool = $getData['address-pool'];

            //hapus dhcp server
            $query = new Query('/ip/dhcp-server/remove');
            $query->equal('.id', $request->id);
            $response = AnyHelper::login($query)->read();

            if (isset($response["after"]["message"])) {
                $code = 400;
                throw new Error($response['after']['message']);
            }

            //hapus network dhcp
            $query = new Query('/ip/dhcp-server/network/print');
            $query->where('gateway', $gateway);
            $dhcpn_id = AnyHelper::login($query)->read()[0]['.id'];

            $query = new Query('/ip/dhcp-server/network/remove');
            $query->equal('.id', $dhcpn_id);
            $response2 = AnyHelper::login($query)->read();

            if (isset($response2["after"]["message"])) {
                $code = 400;
                throw new Error($response2['after']['message']);
            }

            //hapus dhcp pool
            $query = new Query('/ip/pool/print');
            $query->where('name', $addressPool);
            $addressPoolId = AnyHelper::login($query)->read()[0]['.id'];

            $query = new Query('/ip/pool/remove');
            $query->equal('.id', $addressPoolId);
            $response3 = AnyHelper::login($query)->read();

            if (isset($response3["after"]["message"])) {
                $code = 400;
                throw new Error($response3['after']['message']);
            }
            
            $code = 200;
            $resp = array(
                'status' => 'success',
                'message' => 'DHCP Server berhasil dihapus'
            );
            AnyHelper::saveLog('Berhasil Menghapus DHCP Server', 'info');
        } catch (\Throwable $th) {
            $resp = array(
                'status' => 'fail',
                'message' => $th->getMessage()
            );
            AnyHelper::saveLog('Gagal Menghapus DHCP Server: ' . $th->getMessage(), 'error');
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

            //query
            $query = new Query('/ip/dhcp-server/' . strtolower($request->stat));
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
            AnyHelper::saveLog('Berhasil Merubah Status DHCP Server', 'info');
        } catch (\Throwable $th) {
            $resp = array(
                'status' => 'fail',
                'message' => $th->getMessage()
            );
            AnyHelper::saveLog('Gagal Merubah Status DHCP Server: ' . $th->getMessage(), 'error');
        }
        return response()->json($resp, $code);
    }

    public function generatePool(Request $request)
    {
        $code = 500;
        try {
            $request->validate([
                'ip' => 'required|string',
                'network' => 'required|string'
            ]);
            
            $code = 200;
            $resp = array(
                'status' => 'success',
                'data' => AnyHelper::prefix($request->ip, $request->network)
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
