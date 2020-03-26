<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class bKashController extends Controller
{
    protected $isProduction = false;
    protected $sandbox_url = "https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/";
    protected $production_url = "https://checkout.pay.bka.sh/v1.2.0-beta/checkout/";
    protected $action_url = [
        'createURL' => 'payment/create',
        'executeURL' => 'payment/execute/',
        'tokenURL' => 'token/grant',
        'searchURL' => 'payment/search/'
    ];
    protected $merchant = null;

    public function __construct(Merchant $merchant)
    {
        if(!$merchant) { throw new \Exception("Merchant information required", '500'); }
        $this->isProduction = env("APP_ENV") == "production" ? true : false;
        $this->merchant = $merchant;
    }

    public function searchTransaction(string $trxid): array {
        $merchant = $this->merchant;
        $creds = $this->loadCredentials();
        $token = $this->readToken();
        if($token) {
            $headers = [
                'authorization' => $token,
                'x-app-key' => isset($creds['app_key']) ? $creds['app_key'] : ''
            ];
            $url = $this->constructURL('searchURL', $trxid);
            $data = $this->send($url, 'GET', [], $headers);
            if(is_string($data)){
                $data = json_decode($data, true);
            }
            if(isset($data['trxID'])){
                return $data;
            } else {
                return ["payment not found in bKash"];
            }
        } else {
            throw new \Exception("Token is not available", 500);
        }
    }







    public function getToken(): string
    {
        $merchant = $this->merchant;
        $creds = $this->loadCredentials($merchant);

        $body = array(
            'app_key' => $creds["app_key"],
            'app_secret' => !empty($creds["app_secret"]) ? Crypt::decrypt($creds["app_secret"]) : ''
        );
        $header = array(
            'Content-Type' => 'application/json',
            'password' => !empty($creds["password"]) ? Crypt::decrypt($creds["password"]) : '',
            'username' => $creds["username"]
        );

        $url = $this->constructURL('tokenURL');
        $resp = $this->send($url, 'POST', $body, $header);
        if (is_string($resp)) {
            $resp = json_decode($resp, true);
        }
        if (isset($resp['id_token'])) {
            $expires_in = isset($resp['expires_in']) ? (int)$resp['expires_in'] : 0;
            $time_to_expire = time() + $expires_in;
            if (isset($merchant->id) && !empty($merchant->id)) {
                $this->writeConfig([
                    'token' => $resp['id_token'],
                    'expires' => $time_to_expire
                ], $merchant->id
                );
            } else {
                return null;
            }
            return $resp['id_token'];
        }
        return null;
    }
    public function readToken(): string
    {
        $merchant = $this->merchant;
        if (isset($merchant->id) && !empty($merchant->id)) {
            $tokenData = $this->readConfig($merchant->id);
            if(isset($tokenData['token']) && !empty($tokenData['token'])) {
                $expires = isset($tokenData['expires']) ? $tokenData['expires'] : 0;
                if($expires > time()){
                    return $tokenData['token'];
                } else {
                    return $this->getToken();
                }
            } else {
                return $this->getToken();
            }
        }
        return "";
    }


    public function constructURL($action_url, $data = "")
    {
        $baseURL = $this->isProduction ? $this->production_url : $this->sandbox_url;
        return $baseURL . $this->action_url[$action_url] . $data;
    }
    public function loadCredentials()
    {
        $merchant = $this->merchant;
        if ($merchant) {
            return [
                'app_key' => isset($merchant->app_key) ? $merchant->app_key : '',
                'app_secret' => isset($merchant->app_secret) ? $merchant->app_secret : '',
                'username' => isset($merchant->bkash_username) ? $merchant->bkash_username : '',
                'password' => isset($merchant->bkash_password) ? $merchant->bkash_password : '',
            ];
        }
        return null;
    }


    public function send($url, $method = "GET", array $body = [], array $header = []): string
    {
        // dd($url);
        try {
            $client = new \GuzzleHttp\Client(['verify' => false]);
            $request = $client->request($method, $url, [
                'debug' => false,
                'json' => $body,
                'headers' => $header,
                'verify' => false,
            ]);
            return $request->getBody()->getContents();
        } catch (\Exception $e) {
            return $response = $e->getMessage();
        }
    }

    public function writeConfig(array $data, string $id): void
    {
        if (Storage::disk('local')->exists($id . '/bkash.txt')) {
            Storage::disk('local')->put($id . '/bkash.txt', json_encode($data));
        } else {
            Storage::makeDirectory($id);
            Storage::disk('local')->put($id . '/bkash.txt', json_encode($data));
        }
    }

    public function readConfig(string $id): array
    {
        if (Storage::disk('local')->exists($id . '/bkash.txt')) {
            return json_decode(Storage::disk('local')->get($id . '/bkash.txt'), true);
        } else {
            return [];
        }
    }
}
