<?php

namespace App\Helpers;

use Error;
use \RouterOS\Client;
use \RouterOS\Query;

class AnyHelper
{
    public static $loginData;

    public static function loginRequest($request) {
        $pass = $request->pass == null ? '' : $request->pass;
        self::$loginData = array (
            'host' => $request->host,
            'user' => $request->user,
            'pass' => $pass,
            'port' => (int) $request->port
        );
    }

    public static function login($query = null){
        try {
            $client = new Client(self::$loginData);
            if ($query){
                $client->query($query);
            }
            return $client;
        } catch (\Throwable $th) {
            return throw new Error($th->getMessage());
        }
    }

    public static function ubahPort($port) {
        self::$loginData['port'] = $port;
    }

    public static function getInterfaceId($interface) {
        try {
            $query = new Query('/ip/address/print');
            $query->where('interface', $interface);
            $response = self::login($query)->read();
            if ($response) {
                return $response[0]['.id'];
            }
            return false;
        } catch(\Throwable $th) {
            return throw new Error($th->getMessage());
        }
    }

    public static function scanRouter(){
        $ipadd = $_SERVER['REMOTE_ADDR'];
        // $ip = explode('.', $ipadd);
        $network_id = substr($ipadd, 0, strrpos($ipadd, '.'));
        $iplist = [];

        for ($i = 1; $i < 255; $i++) {
            $connection = @fsockopen($network_id . $i, 8728, $errno, $errstr, 0.05);

            if (is_resource($connection)) {
                return $network_id.$i;
                $iplist[] = $network_id . $i;
                // $this->ip = $network_id . $i;
                fclose($connection);
            }
        }
        return @fsockopen('192.168.200.1', 8728, $errno, $errstr, 0.05);;
    }

    public static function sideBarMenu($url){
        $menu = array(
            [
                'menu-name' => 'Dashboard',
                'menu-link' => $url,
                'menu-icon' => '<i class="bi bi-grid"></i>',
                'sub-menu' => null
            ],
            // [
            //     'menu-name' => 'Add Router',
            //     'menu-link' => 'http://localhost:8000',
            //     'menu-icon' => '<i class="bi bi-grid"></i>',
            //     'sub-menu' => null
            // ],
            [
                'menu-name' => 'Konfigurasi Dasar',
                'menu-icon' => '<i class="bi bi-menu-button-wide"></i>',
                'sub-menu' => array(
                    [
                        'menu-name' => 'IP Address',
                        'menu-link' => $url.'/ip-address'
                    ],
                    [
                        'menu-name' => 'Internet Gateway',
                        'menu-link' => $url.'/internet-gateway'
                    ],
                    [
                        'menu-name' => 'DHCP Server',
                        'menu-link' => $url.'/dhcp-server'
                    ]
                ),
            ],
            [
                'menu-name' => 'Manajemen Bandwidth',
                'menu-icon' => '<i class="bi bi-menu-button-wide"></i>',
                'sub-menu' => array(
                    [
                        'menu-name' => 'Simple Queue',
                        'menu-link' => $url.'/queue-simple'
                    ],
                    [
                        'menu-name' => 'Queue Tree',
                        'menu-link' => $url.'/queue-tree'
                    ],
                    [
                        'menu-name' => 'Perconnection Queue',
                        'menu-link' => $url.'/queue-pcq'
                    ]
                ),
            ],
            [
                'menu-name' => 'Monitoring Traffic',
                'menu-icon' => '<i class="bi bi-menu-button-wide"></i>',
                'sub-menu' => array(
                    [
                        'menu-name' => 'Prioritas Traffic',
                        'menu-link' => $url.'/prioritas-traffic'
                    ],
                    [
                        'menu-name' => 'Drop Traffic',
                        'menu-link' => $url.'/drop-traffic'
                    ],
                    [
                        'menu-name' => 'Monitoring Traffic',
                        'menu-link' => $url.'monitoring-traffic'
                    ],
                    [
                        'menu-name' => 'Notifikasi',
                        'menu-link' => $url.'notifikasi'
                    ]
                ),
            ],
            [
                'menu-name' => 'Lainnya',
                'menu-icon' => '<i class="bi bi-menu-button-wide"></i>',
                'sub-menu' => array(
                    [
                        'menu-name' => 'Service',
                        'menu-link' => 'ok'
                    ],
                    [
                        'menu-name' => 'Identity',
                        'menu-link' => 'ok'
                    ],
                    [
                        'menu-name' => 'Akun',
                        'menu-link' => 'ok'
                    ],
                    [
                        'menu-name' => 'Clock',
                        'menu-link' => 'ok'
                    ],
                    [
                        'menu-name' => 'Backup',
                        'menu-link' => 'ok'
                    ],
                    [
                        'menu-name' => 'Restore',
                        'menu-link' => 'ok'
                    ]
                ),
            ],
        );

        return $menu;
    }

    public static function getJson ($filename, $filter = ['key' => null, 'value' => null])
   {
      $jsonString = file_get_contents(public_path('storage/json/' . $filename . '.json'));
      $datas = json_decode($jsonString, true);

      $resp = array();
      if (!empty($filter['key']) && !empty($filter['value'])) {
         foreach ($datas as $data) {
            if ($data[$filter['key']] == $filter['value']) {
               array_push($resp, $data);
            }
         }
      } else {
         $resp = $datas;
      }
      
      return $resp;
   }

   public static function insertJson($json, $datas)
   {
      $resp =  self::getJson($json);
      array_push($resp, $datas);

      self::storeJson($json, $resp);
      return true;
   }
   
   public static function updateJson($json, $datas, $filter = ['key' => null, 'value' => null])
   {
      $resps =  self::getJson($json);
      $result = array();

      foreach ($resps as $resp) {
         if ($resp[$filter['key']] == $filter['value']) {
            array_push($result, $datas);
         } else {
            array_push($result, $resp);
         }
      }

      self::storeJson($json, $result);
      return true;
   }

   public static function deleteJson($json, $filter = ['key' => null, 'value' => null])
   {
      $resps =  self::getJson($json);
      $result = array();

      foreach ($resps as $resp) {
         if ($resp[$filter['key']] == $filter['value']) {
            // array_push($result, $datas);
            continue;
         } else {
            array_push($result, $resp);
         }
      }

      self::storeJson($json, $result);
      return true;
   }
   
   public static function storeJson ($json, $data)
   {
      $newJsonString = json_encode($data, JSON_PRETTY_PRINT);
      file_put_contents(public_path('storage/json/' . $json . '.json'), stripslashes($newJsonString));
      return true;

   }

   public static function saveLog($deskripsi, $status)
   {
      self::insertJson('log',
         array(
            'deskripsi' => self::$loginData['user'] . '@' . self::$loginData['host'] . ' ' . $deskripsi,
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s')
         )
      );
      return true;
   }

   public static function prefix($address, $network)
   {
      $ip = explode('/', $address)[0];
      $prefix = explode('/', $address)[1];
      $ipnetwork = explode(".", $network);
      $ipoktet = explode(".", $ip);

      if ($prefix == 24) {
         //boroadcast
         $bc = 255;

         //first ip
         $f_ip = 1;
      } elseif ($prefix == 25) {
         switch ($ipnetwork[3]) {
            case 0:
               $bc = 127;
               $f_ip = 1;
               break;
            default:
               $bc = 255;
               $f_ip = 129;
         }
      } elseif ($prefix == 26) {
         switch ($ipnetwork[3]) {
            case 0:
               $bc = 63;
               $f_ip = 1;
               break;
            case 64:
               $bc = 127;
               $f_ip = 65;
               break;
            case 128:
               $bc = 191;
               $f_ip = 129;
               break;
            default:
               $bc = 255;
               $f_ip = 193;
         }
      } elseif ($prefix == 27) {
         switch ($ipnetwork[3]) {
            case 0:
               $bc = 31;
               $f_ip = 1;
               break;
            case 32:
               $bc = 63;
               $f_ip = 33;
               break;
            case 64:
               $bc = 95;
               $f_ip = 65;
               break;
            case 96:
               $bc = 127;
               $f_ip = 97;
               break;
            case 128:
               $bc = 159;
               $f_ip = 129;
               break;
            case 160:
               $bc = 191;
               $f_ip = 161;
               break;
            case 192:
               $bc = 223;
               $f_ip = 193;
               break;
            default:
               $bc = 255;
               $f_ip = 225;
         }
      } elseif ($prefix == 28) {
         switch ($ipnetwork[3]) {
            case 0:
               $bc = 15;
               $f_ip = 1;
               break;
            case 16:
               $bc = 31;
               $f_ip = 17;
               break;
            case 32:
               $bc = 47;
               $f_ip = 33;
               break;
            case 48:
               $bc = 63;
               $f_ip = 49;
               break;
            case 64:
               $bc = 79;
               $f_ip = 65;
               break;
            case 80:
               $bc = 95;
               $f_ip = 81;
               break;
            case 96:
               $bc = 111;
               $f_ip = 97;
               break;
            case 112:
               $bc = 127;
               $f_ip = 113;
               break;
            case 128:
               $bc = 143;
               $f_ip = 129;
               break;
            case 144:
               $bc = 159;
               $f_ip = 145;
               break;
            case 160:
               $bc = 175;
               $f_ip = 161;
               break;
            case 176:
               $bc = 191;
               $f_ip = 177;
               break;
            case 192:
               $bc = 207;
               $f_ip = 193;
               break;
            case 208:
               $bc = 223;
               $f_ip = 209;
               break;
            case 224:
               $bc = 239;
               $f_ip = 225;
               break;
            default:
               $bc = 255;
               $f_ip = 241;
         }
      } elseif ($prefix == 29) {
         switch ($ipnetwork[3]) {
            case 0:
               $bc = 7;
               $f_ip = 1;
               break;
            case 8:
               $bc = 15;
               $f_ip = 9;
               break;
            case 16:
               $bc = 23;
               $f_ip = 17;
               break;
            case 24:
               $bc = 31;
               $f_ip = 25;
               break;
            case 32:
               $bc = 39;
               $f_ip = 33;
               break;
            case 40:
               $bc = 47;
               $f_ip = 41;
               break;
            case 48:
               $bc = 55;
               $f_ip = 49;
               break;
            case 56:
               $bc = 63;
               $f_ip = 57;
               break;
            case 64:
               $bc = 71;
               $f_ip = 65;
               break;
            case 72:
               $bc = 79;
               $f_ip = 73;
               break;
            case 80:
               $bc = 87;
               $f_ip = 81;
               break;
            case 88:
               $bc = 95;
               $f_ip = 89;
               break;
            case 96:
               $bc = 103;
               $f_ip = 97;
               break;
            case 104:
               $bc = 111;
               $f_ip = 105;
               break;
            case 112:
               $bc = 119;
               $f_ip = 113;
               break;
            case 120:
               $bc = 127;
               $f_ip = 121;
               break;
            case 128:
               $bc = 135;
               $f_ip = 129;
               break;
            case 136:
               $bc = 143;
               $f_ip = 137;
               break;
            case 144:
               $bc = 151;
               $f_ip = 145;
               break;
            case 152:
               $bc = 159;
               $f_ip = 153;
               break;
            case 160:
               $bc = 167;
               $f_ip = 161;
               break;
            case 168:
               $bc = 175;
               $f_ip = 169;
               break;
            case 176:
               $bc = 183;
               $f_ip = 177;
               break;
            case 184:
               $bc = 191;
               $f_ip = 185;
               break;
            case 192:
               $bc = 199;
               $f_ip = 193;
               break;
            case 200:
               $bc = 207;
               $f_ip = 201;
               break;
            case 208:
               $bc = 215;
               $f_ip = 209;
               break;
            case 216:
               $bc = 223;
               $f_ip = 217;
               break;
            case 224:
               $bc = 231;
               $f_ip = 225;
               break;
            case 232:
               $bc = 239;
               $f_ip = 233;
               break;
            case 240:
               $bc = 247;
               $f_ip = 241;
               break;
            default:
               $bc = 255;
               $f_ip = 249;
         }
      } elseif ($prefix == 30) {
         switch ($ipnetwork[3]) {
            case 0:
               $bc = 3;
               $f_ip = 1;
               break;
            case 4:
               $bc = 7;
               $f_ip = 5;
               break;
            case 8:
               $bc = 11;
               $f_ip = 9;
               break;
            case 12:
               $bc = 15;
               $f_ip = 13;
               break;
            case 16:
               $bc = 19;
               $f_ip = 17;
               break;
            case 20:
               $bc = 23;
               $f_ip = 21;
               break;
            case 24:
               $bc = 27;
               $f_ip = 25;
               break;
            case 28:
               $bc = 31;
               $f_ip = 29;
               break;
            case 32:
               $bc = 35;
               $f_ip = 33;
               break;
            case 36:
               $bc = 39;
               $f_ip = 37;
               break;
            case 40:
               $bc = 43;
               $f_ip = 41;
               break;
            case 44:
               $bc = 47;
               $f_ip = 45;
               break;
            case 48:
               $bc = 51;
               $f_ip = 49;
               break;
            case 52:
               $bc = 55;
               $f_ip = 53;
               break;
            case 56:
               $bc = 59;
               $f_ip = 57;
               break;
            case 60:
               $bc = 63;
               $f_ip = 61;
               break;
            case 64:
               $bc = 67;
               $f_ip = 65;
               break;
            case 68:
               $bc = 71;
               $f_ip = 69;
               break;
            case 72:
               $bc = 75;
               $f_ip = 73;
               break;
            case 76:
               $bc = 79;
               $f_ip = 77;
               break;
            case 80:
               $bc = 83;
               $f_ip = 81;
               break;
            case 84:
               $bc = 87;
               $f_ip = 85;
               break;
            case 88:
               $bc = 91;
               $f_ip = 89;
               break;
            case 92:
               $bc = 95;
               $f_ip = 93;
               break;
            case 96:
               $bc = 99;
               $f_ip = 97;
               break;
            case 100:
               $bc = 103;
               $f_ip = 101;
               break;
            case 104:
               $bc = 107;
               $f_ip = 105;
               break;
            case 108:
               $bc = 111;
               $f_ip = 109;
               break;
            case 112:
               $bc = 115;
               $f_ip = 113;
               break;
            case 116:
               $bc = 119;
               $f_ip = 117;
               break;
            case 120:
               $bc = 123;
               $f_ip = 121;
               break;
            case 124:
               $bc = 127;
               $f_ip = 125;
               break;
            case 128:
               $bc = 131;
               $f_ip = 129;
               break;
            case 132:
               $bc = 135;
               $f_ip = 133;
               break;
            case 136:
               $bc = 139;
               $f_ip = 137;
               break;
            case 140:
               $bc = 143;
               $f_ip = 141;
               break;
            case 144:
               $bc = 147;
               $f_ip = 145;
               break;
            case 148:
               $bc = 151;
               $f_ip = 149;
               break;
            case 152:
               $bc = 155;
               $f_ip = 153;
               break;
            case 156:
               $bc = 159;
               $f_ip = 157;
               break;
            case 160:
               $bc = 163;
               $f_ip = 161;
               break;
            case 164:
               $bc = 167;
               $f_ip = 165;
               break;
            case 168:
               $bc = 171;
               $f_ip = 169;
               break;
            case 172:
               $bc = 175;
               $f_ip = 173;
               break;
            case 176:
               $bc = 179;
               $f_ip = 177;
               break;
            case 180:
               $bc = 183;
               $f_ip = 181;
               break;
            case 184:
               $bc = 187;
               $f_ip = 185;
               break;
            case 188:
               $bc = 191;
               $f_ip = 189;
               break;
            case 192:
               $bc = 195;
               $f_ip = 193;
               break;
            case 196:
               $bc = 199;
               $f_ip = 197;
               break;
            case 200:
               $bc = 203;
               $f_ip = 201;
               break;
            case 204:
               $bc = 207;
               $f_ip = 205;
               break;
            case 208:
               $bc = 211;
               $f_ip = 209;
               break;
            case 212:
               $bc = 215;
               $f_ip = 213;
               break;
            case 216:
               $bc = 219;
               $f_ip = 217;
               break;
            case 220:
               $bc = 223;
               $f_ip = 221;
               break;
            case 224:
               $bc = 227;
               $f_ip = 225;
               break;
            case 228:
               $bc = 231;
               $f_ip = 229;
               break;
            case 232:
               $bc = 235;
               $f_ip = 233;
               break;
            case 236:
               $bc = 239;
               $f_ip = 237;
               break;
            case 240:
               $bc = 243;
               $f_ip = 241;
               break;
            case 244:
               $bc = 247;
               $f_ip = 245;
               break;
            case 248:
               $bc = 251;
               $f_ip = 249;
               break;
            default:
               $bc = 255;
               $f_ip = 253;
         }
      } elseif ($prefix == 31) {
         switch ($ipnetwork[3]) {
            case 0:
               $bc = 3;
               $f_ip = 1;
               break;
            case 2:
               $bc = 3;
               $f_ip = 1;
               break;
            case 4:
               $bc = 7;
               $f_ip = 5;
               break;
            case 6:
               $bc = 7;
               $f_ip = 5;
               break;
            case 8:
               $bc = 11;
               $f_ip = 9;
               break;
            case 10:
               $bc = 11;
               $f_ip = 9;
               break;
            case 12:
               $bc = 15;
               $f_ip = 13;
               break;
            case 14:
               $bc = 15;
               $f_ip = 13;
               break;
            case 16:
               $bc = 19;
               $f_ip = 17;
               break;
            case 18:
               $bc = 19;
               $f_ip = 17;
               break;
            case 20:
               $bc = 23;
               $f_ip = 21;
               break;
            case 22:
               $bc = 23;
               $f_ip = 21;
               break;
            case 24:
               $bc = 27;
               $f_ip = 25;
               break;
            case 26:
               $bc = 27;
               $f_ip = 25;
               break;
            case 28:
               $bc = 31;
               $f_ip = 29;
               break;
            case 30:
               $bc = 31;
               $f_ip = 29;
               break;
            case 32:
               $bc = 35;
               $f_ip = 33;
               break;
            case 34:
               $bc = 35;
               $f_ip = 33;
               break;
            case 36:
               $bc = 39;
               $f_ip = 37;
               break;
            case 38:
               $bc = 39;
               $f_ip = 37;
               break;
            case 40:
               $bc = 43;
               $f_ip = 41;
               break;
            case 42:
               $bc = 43;
               $f_ip = 41;
               break;
            case 44:
               $bc = 47;
               $f_ip = 45;
               break;
            case 46:
               $bc = 47;
               $f_ip = 45;
               break;
            case 48:
               $bc = 51;
               $f_ip = 49;
               break;
            case 50:
               $bc = 51;
               $f_ip = 49;
               break;
            case 52:
               $bc = 55;
               $f_ip = 53;
               break;
            case 54:
               $bc = 55;
               $f_ip = 53;
               break;
            case 56:
               $bc = 59;
               $f_ip = 57;
               break;
            case 58:
               $bc = 59;
               $f_ip = 57;
               break;
            case 60:
               $bc = 63;
               $f_ip = 61;
               break;
            case 62:
               $bc = 63;
               $f_ip = 61;
               break;
            case 64:
               $bc = 67;
               $f_ip = 65;
               break;
            case 66:
               $bc = 67;
               $f_ip = 65;
               break;
            case 68:
               $bc = 71;
               $f_ip = 69;
               break;
            case 70:
               $bc = 71;
               $f_ip = 69;
               break;
            case 72:
               $bc = 75;
               $f_ip = 73;
               break;
            case 74:
               $bc = 75;
               $f_ip = 73;
               break;
            case 76:
               $bc = 79;
               $f_ip = 77;
               break;
            case 78:
               $bc = 79;
               $f_ip = 77;
               break;
            case 80:
               $bc = 83;
               $f_ip = 81;
               break;
            case 82:
               $bc = 83;
               $f_ip = 81;
               break;
            case 84:
               $bc = 87;
               $f_ip = 85;
               break;
            case 86:
               $bc = 87;
               $f_ip = 85;
               break;
            case 88:
               $bc = 91;
               $f_ip = 89;
               break;
            case 90:
               $bc = 91;
               $f_ip = 89;
               break;
            case 92:
               $bc = 95;
               $f_ip = 93;
               break;
            case 94:
               $bc = 95;
               $f_ip = 93;
               break;
            case 96:
               $bc = 99;
               $f_ip = 97;
               break;
            case 98:
               $bc = 99;
               $f_ip = 97;
               break;
            case 100:
               $bc = 103;
               $f_ip = 101;
               break;
            case 102:
               $bc = 103;
               $f_ip = 101;
               break;
            case 104:
               $bc = 107;
               $f_ip = 105;
               break;
            case 106:
               $bc = 107;
               $f_ip = 105;
               break;
            case 108:
               $bc = 111;
               $f_ip = 109;
               break;
            case 110:
               $bc = 111;
               $f_ip = 109;
               break;
            case 112:
               $bc = 115;
               $f_ip = 113;
               break;
            case 114:
               $bc = 115;
               $f_ip = 113;
               break;
            case 116:
               $bc = 119;
               $f_ip = 117;
               break;
            case 118:
               $bc = 119;
               $f_ip = 117;
               break;
            case 120:
               $bc = 123;
               $f_ip = 121;
               break;
            case 122:
               $bc = 123;
               $f_ip = 121;
               break;
            case 124:
               $bc = 127;
               $f_ip = 125;
               break;
            case 126:
               $bc = 127;
               $f_ip = 125;
               break;
            case 128:
               $bc = 131;
               $f_ip = 129;
               break;
            case 130:
               $bc = 131;
               $f_ip = 129;
               break;
            case 132:
               $bc = 135;
               $f_ip = 133;
               break;
            case 134:
               $bc = 135;
               $f_ip = 133;
               break;
            case 136:
               $bc = 139;
               $f_ip = 137;
               break;
            case 138:
               $bc = 139;
               $f_ip = 137;
               break;
            case 140:
               $bc = 143;
               $f_ip = 141;
               break;
            case 142:
               $bc = 143;
               $f_ip = 141;
               break;
            case 144:
               $bc = 147;
               $f_ip = 145;
               break;
            case 146:
               $bc = 147;
               $f_ip = 145;
               break;
            case 148:
               $bc = 151;
               $f_ip = 149;
               break;
            case 150:
               $bc = 151;
               $f_ip = 149;
               break;
            case 152:
               $bc = 155;
               $f_ip = 153;
               break;
            case 154:
               $bc = 155;
               $f_ip = 153;
               break;
            case 156:
               $bc = 159;
               $f_ip = 157;
               break;
            case 158:
               $bc = 159;
               $f_ip = 157;
               break;
            case 160:
               $bc = 163;
               $f_ip = 161;
               break;
            case 162:
               $bc = 163;
               $f_ip = 161;
               break;
            case 164:
               $bc = 167;
               $f_ip = 165;
               break;
            case 166:
               $bc = 167;
               $f_ip = 165;
               break;
            case 168:
               $bc = 171;
               $f_ip = 169;
               break;
            case 170:
               $bc = 171;
               $f_ip = 169;
               break;
            case 172:
               $bc = 175;
               $f_ip = 173;
               break;
            case 174:
               $bc = 175;
               $f_ip = 173;
               break;
            case 176:
               $bc = 179;
               $f_ip = 177;
               break;
            case 178:
               $bc = 179;
               $f_ip = 177;
               break;
            case 180:
               $bc = 183;
               $f_ip = 181;
               break;
            case 182:
               $bc = 183;
               $f_ip = 181;
               break;
            case 184:
               $bc = 187;
               $f_ip = 185;
               break;
            case 186:
               $bc = 187;
               $f_ip = 185;
               break;
            case 188:
               $bc = 191;
               $f_ip = 189;
               break;
            case 190:
               $bc = 191;
               $f_ip = 189;
               break;
            case 192:
               $bc = 195;
               $f_ip = 193;
               break;
            case 194:
               $bc = 195;
               $f_ip = 193;
               break;
            case 196:
               $bc = 199;
               $f_ip = 197;
               break;
            case 198:
               $bc = 199;
               $f_ip = 197;
               break;
            case 200:
               $bc = 203;
               $f_ip = 201;
               break;
            case 202:
               $bc = 203;
               $f_ip = 201;
               break;
            case 204:
               $bc = 207;
               $f_ip = 205;
               break;
            case 206:
               $bc = 207;
               $f_ip = 205;
               break;
            case 208:
               $bc = 211;
               $f_ip = 209;
               break;
            case 210:
               $bc = 211;
               $f_ip = 209;
               break;
            case 212:
               $bc = 215;
               $f_ip = 213;
               break;
            case 214:
               $bc = 215;
               $f_ip = 213;
               break;
            case 216:
               $bc = 219;
               $f_ip = 217;
               break;
            case 218:
               $bc = 219;
               $f_ip = 217;
               break;
            case 220:
               $bc = 223;
               $f_ip = 221;
               break;
            case 222:
               $bc = 223;
               $f_ip = 221;
               break;
            case 224:
               $bc = 227;
               $f_ip = 225;
               break;
            case 226:
               $bc = 227;
               $f_ip = 225;
               break;
            case 228:
               $bc = 231;
               $f_ip = 229;
               break;
            case 230:
               $bc = 231;
               $f_ip = 229;
               break;
            case 232:
               $bc = 235;
               $f_ip = 233;
               break;
            case 234:
               $bc = 235;
               $f_ip = 233;
               break;
            case 236:
               $bc = 239;
               $f_ip = 237;
               break;
            case 238:
               $bc = 239;
               $f_ip = 237;
               break;
            case 240:
               $bc = 243;
               $f_ip = 241;
               break;
            case 242:
               $bc = 243;
               $f_ip = 241;
               break;
            case 244:
               $bc = 247;
               $f_ip = 245;
               break;
            case 246:
               $bc = 247;
               $f_ip = 245;
               break;
            case 248:
               $bc = 251;
               $f_ip = 249;
               break;
            case 250:
               $bc = 251;
               $f_ip = 249;
               break;
            case 252:
               $bc = 251;
               $f_ip = 249;
               break;
            default:
               $bc = 255;
               $f_ip = 253;
         }
      }

      if ($prefix >= 24 && $prefix <= 31) {
         if ($ipoktet[3] == $f_ip) {
            $data["f_host"][] = $ipoktet[0] . "." . $ipoktet[1] . "." . $ipoktet[2] . "." . ($ipnetwork[3] + 1);
            $data["l_host"][] = $ipoktet[0] . "." . $ipoktet[1] . "." . $ipoktet[2] . "." . ($bc - 1);
            $data["ranges"][] = $data["f_host"][0] . "-" . $data["l_host"][0];
         } elseif ($ipoktet[3] == ($f_ip + 1)) {
            $data["f_host"][] = $ipoktet[0] . "." . $ipoktet[1] . "." . $ipoktet[2] . "." . ($ipnetwork[3] + 1);
            $data["f_host"][] = $ipoktet[0] . "." . $ipoktet[1] . "." . $ipoktet[2] . "." . ($ipoktet[3] + 1);
            $data["l_host"][] = $ipoktet[0] . "." . $ipoktet[1] . "." . $ipoktet[2] . "." . ($bc - 1);
            $data["ranges"][] = $data["f_host"][0];
            $data["ranges"][] = $data["f_host"][1] . "-" . $data["l_host"][0];
         } else {
            $data["f_host"][] = $ipoktet[0] . "." . $ipoktet[1] . "." . $ipoktet[2] . "." . ($ipnetwork[3] + 1);
            $data["l_host"][] = $ipoktet[0] . "." . $ipoktet[1] . "." . $ipoktet[2] . "." . ($ipoktet[3] - 1);
            $data["f_host"][] = $ipoktet[0] . "." . $ipoktet[1] . "." . $ipoktet[2] . "." . ($ipoktet[3] + 1);
            $data["l_host"][] = $ipoktet[0] . "." . $ipoktet[1] . "." . $ipoktet[2] . "." . ($bc - 1);
            $data["ranges"][] = $data["f_host"][0] . "-" . $data["l_host"][0];
            $data["ranges"][] = $data["f_host"][1] . "-" . $data["l_host"][1];
         }
      } else {
         $data["f_host"][] = "";
         $data["l_host"][] = "";
         $data["ranges"][] = "";
      }
      return $data;
   }
}

