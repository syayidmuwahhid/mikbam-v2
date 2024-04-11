<?php

namespace App\Http\Controllers\API;

use App\Helpers\AnyHelper;
use App\Http\Controllers\Controller;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use RouterOS\Query;

class InternetGateway extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $code = 500;

        $resp = array (
            'status' => 'fail',
        );

        try {
            $request->validate([
                'interface' => 'string|required'
            ]);

            AnyHelper::loginRequest($request);

            //ip address ether1
            $query = new Query('/ip/address/print');
            $query->where('interface', $request->interface);
            $ip = AnyHelper::login($query)->read();

            //gateway
            $query = new Query('/ip/route/print');
            $query->where('dst-address', "0.0.0.0/0");
            $query->where('active', 'true');
            $gateway = AnyHelper::login($query)->read();

            //dns
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

            //NAT
            $query = new Query('/ip/firewall/nat/print');
            $query->where('chain', 'srcnat');
            $query->where('action', 'masquerade');
            $nat = AnyHelper::login($query)->read();

            //cekInternet
            $query = new Query('/ping');
            $query->equal('address', "google.com");
            $query->equal('interface', $request->interface);
            $query->equal('count', 4);
            $cekinternet = AnyHelper::login($query)->read();
            
            if (isset($cekinternet["after"]["message"]) || $cekinternet == null) {
                $internet = false;
            } else {
                $received = 0;
                $internet = true;
                foreach ($cekinternet as $r) {
                    $received += (int) $r["received"];
                }
                if ($received == 0) {
                    $internet = false;
                }
            }

            $code = 200;
            $resp['status'] = 'success';
            $resp['data'] = array(
                'dhcp-client' => self::cekDHCPCLient($request->interface),
                'ip-address' => $ip ? $ip[0]['address'] : false,
                'gateway' => $gateway ? $gateway[0]['gateway'] : false,
                'dns' => $dataDNS,
                'nat' => $nat ? true : false,
                'internet' => true,
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

    private function cekDHCPCLient($interface)
    {
        $query = new Query('/ip/dhcp-client/print');
        $query->where('interface', $interface);
        $query->where('status', "bound");
        $cekEth1 = AnyHelper::login($query)->read();
        return  $cekEth1 == null ? false : true;
    }

    public function auto(Request $request)
    {
        $code = 500;
        
        $resp = array(
            'status' => 'fail'
        );

        
        try {
            $request->validate([
                'interface' => 'string|required'
            ]);
            
            AnyHelper::loginRequest($request);

            $cekDHCPClient = self::cekDHCPCLient($request->interface);

            if (!$cekDHCPClient) { //add dhcp client
                $idEth1Lama = AnyHelper::getInterfaceId($request->interface);

                $query = new Query('/ip/dhcp-client/add');
                $query->equal('interface', $request->interface);
                $query->equal('disabled', "no");
                $response = AnyHelper::login($query)->read();

                if (isset($response["after"]["message"])) {
                    throw new Error($response['after']['message']);
                }

                if ($idEth1Lama) {
                    //disable ether 1 static
                    $query = new Query('/ip/address/disable');
                    $query->equal('.id', $idEth1Lama);
                    $response = AnyHelper::login($query)->read();
    
                    if (isset($response["after"]["message"])) {
                        throw new Error($response['after']['message']);
                    }
                }
            }

            //firewall NAT
            //cek NAT
            $query = new Query('/ip/firewall/nat/print');
            $query->where('chain', 'srcnat');
            $query->where('action', 'masquerade');
            
            if (!AnyHelper::login($query)->read()){
                //add NAT
                $query = new Query('/ip/firewall/nat/add');
                $query->equal('action', "masquerade");
                $query->equal('out-interface', $request->interface);
                $query->equal("chain", "srcnat");
                $response = AnyHelper::login($query)->read();

                if (isset($response["after"]["message"])) {
                    throw new Error($response['after']['message']);
                }
            }
            
            $resp['status'] = 'success';
            $resp['message'] = 'Internet Gateway Berhasil di Konfigurasi';
            $code = 201;
            AnyHelper::saveLog('Berhasil Mengonfigurasi Internet Gateway Otomatis', 'info');

        } catch (\Throwable $th) {
            $resp = array(
                'status' => 'fail',
                'message' => $th->getMessage()
            );
            $code = 400;
            
            AnyHelper::saveLog('Gagal Mengonfigurasi Internet Gateway Otomatis: ' . $th->getMessage(), 'error');
        }

        return response()->json($resp, $code);
    }

    public function manual(Request $request)
    {
        $code = 500;
        
        $resp = array(
            'status' => 'fail'
        );

        try {
            $request->validate([
                'ip' => 'required|string',
                'gateway' => 'required|string',
                'dns' => 'required|string'
            ]);

            AnyHelper::loginRequest($request);

            $idEth1Lama = AnyHelper::getInterfaceId($request->interface);

            //tambah ip address ether 1
            $query = new Query('/ip/address/add');
            $query->equal('address', $request->ip);
            $query->equal('interface', $request->interface);
            $response = AnyHelper::login($query)->read();
            
            if (isset($response["after"]["message"])) {
                throw new Error($response['after']['message']);
            }

            if ($idEth1Lama) {
                //disable ether 1 awal
                $query = new Query('/ip/address/disable');
                $query->equal('.id', $idEth1Lama);
                $response = AnyHelper::login($query)->read();

                if (isset($response["after"]["message"])) {
                    throw new Error($response['after']['message']);
                }
            }

            //add gateway
            $query = new Query('/ip/route/print');
            $query->where('dst-address', "0.0.0.0/0");
            $query->where('gateway', $request->gateway);
            $query->where('static', "yes");
            $query->where('active', "yes");
            $cekgw = AnyHelper::login($query)->read();
            
            if (!$cekgw) {
                $query = new Query('/ip/route/add');
                $query->equal('gateway', $request->gateway);
                $query->equal('dst-address', "0.0.0.0/0");
                $response = AnyHelper::login($query)->read();

                if (isset($response["after"]["message"])) {
                    throw new Error($response['after']['message']);
                }
            }

            //add DNS
            $query = new Query('/ip/dns/set');
            $query->equal('servers', $request->dns);
            $dns = AnyHelper::login($query)->read();

            if (isset($dns["after"]["message"])) {
                throw new Error($response['after']['message']);
            }

            //firewall NAT
            //cek NAT
            $query = new Query('/ip/firewall/nat/print');
            $query->where('chain', 'srcnat');
            $query->where('action', 'masquerade');
            
            if (!AnyHelper::login($query)->read()){
                //add NAT
                $query = new Query('/ip/firewall/nat/add');
                $query->equal('action', "masquerade");
                $query->equal('out-interface', $request->interface);
                $query->equal("chain", "srcnat");
                $response = AnyHelper::login($query)->read();

                if (isset($response["after"]["message"])) {
                    throw new Error($response['after']['message']);
                }
            }

            $resp['status'] = 'success';
            $resp['message'] = 'Internet Gateway Berhasil di Konfigurasi';
            $code = 201;
            AnyHelper::saveLog('Berhasil Mengonfigurasi Internet Gateway Manual', 'info');

        } catch (\Throwable $th) {
            $resp = array(
                'status' => 'fail',
                'message' => $th->getMessage()
            );
            $code = 400;
            
            AnyHelper::saveLog('Gagal Mengonfigurasi Internet Gateway Manual: ' . $th->getMessage(), 'error');
        }

        return response()->json($resp, $code);
    }
}
