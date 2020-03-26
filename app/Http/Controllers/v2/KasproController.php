<?php

namespace App\Http\Controllers\v2;

use Illuminate\Http\Request;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;

class KasproController extends Controller
{
    public function register(Request $request)
    {
        $input = $request->all();
        $json = $request->json()->all();
        // return $json;
        $endpoint = 'http://dev.kaspro.id/api/593203123454/partner/subscribers';

        try {
            $client = new \GuzzleHttp\Client();
            // $request = $client->post($endpoint);
            // $request->setBody(json_decode($json));
            $response = $client->request('POST', $endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Token' => 'WLu28cXFYvrdtQ7KFNxDUI3hpufmj+EbNknAEL9i7pfdjx69s/lnu3YSScaxUv+7Iere9Or5f1AvNC3rO8l+U3gkcU87vUrlHu6llGJeZiolpM2mD1ZePTlPyjVrArkmlK5Ui8vnGmu55anh2jq2Y4KD9HIj2FI8ENzfFqPX3/vmVH2e8ImkxsDuK1Ot+oH6BVxUKThhqcVPFfv3Qe52AA==',
                ],
                'json' => $json,
                // 'json' => $json,
                'timeout' => 30,
            ]);
            // $response = $request->send();
             return  $response;
        } catch (ClientException $e) {
            // For handling exception.
            echo Psr7\str($e->getRequest());
            echo Psr7\str($e->getResponse());
        }
     
    }

    public function agentRegister(Request $request)
    {
        $input = $request->all();
        $json = $request->json()->all();

        $endpoint = env('KASPRO_PROD').'agents';
        $tokenKaspro = env('KASPRO_TOKEN');

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', $endpoint, [
                'headers' => [
                    'content-type' => 'application/json',
                    'token' => $tokenKaspro,
                    'accept-langauge' => 'ID',
                    'nonce' => 'nonce',
                ],
                'json' => [
                    'subscriber' => [
                        'password' => $input['subscriber']['password'],
                        'middle-name' => $input['subscriber']['middle-name'],
                        'valid-id-desc' => $input['subscriber']['valid-id-desc'],
                        'resident_address' => [
                            'specific-address' => $input['subscriber']['resident-address']['specific-address'],
                            'region-code' => $input['subscriber']['resident-address']['region-code'],
                            'coordinates' => $input['subscriber']['resident-address']['coordinates'],
                            'postal-code' => $input['subscriber']['resident-address']['postal-code'],
                            'city-code' => $input['subscriber']['resident-address']['city-code'],
                        ],
                        'business-name' => $input['subscriber']['business-name'],
                        'valid-id' => $input['subscriber']['valid-id'],
                        'account-name' => $input['subscriber']['account-name'],
                        'authorized-mobile' => $input['subscriber']['authorized-mobile'],
                        'first-name' => $input['subscriber']['first-name'],
                        'last-name' => $input['subscriber']['last-name'],
                    ],
                    'auth' => [
                        'password' => $input['auth']['password'],
                        'partner-otp' => $input['auth']['partner-otp'],
                    ],
                    'request-id' => $input['request-id'],
                ],
                // 'json' => $json,
                'timeout' => 30,
            ]);

            return  $json_decode($response->getBody());
        } catch (ClientException $e) {
            // For handling exception.
            echo Psr7\str($e->getRequest());
            echo Psr7\str($e->getResponse());
        }
    }

    public function payment(Request $request)
    {
        try {
            //code...
            $input = $request->all();
            $json = $request->json()->all();
            // return $json;
            $endpoint = env('KASPRO_PROD').'partner/subscribers';
    
          
                $client = new \GuzzleHttp\Client();
                // $request = $client->post($endpoint);
                // $request->setBody(json_decode($json));
                $response = $client->request('POST', $endpoint, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Token' => 'WLu28cXFYvrdtQ7KFNxDUI3hpufmj+EbNknAEL9i7pfdjx69s/lnu3YSScaxUv+7Iere9Or5f1AvNC3rO8l+U3gkcU87vUrlHu6llGJeZiolpM2mD1ZePTlPyjVrArkmlK5Ui8vnGmu55anh2jq2Y4KD9HIj2FI8ENzfFqPX3/vmVH2e8ImkxsDuK1Ot+oH6BVxUKThhqcVPFfv3Qe52AA==',
                    ],
                    'json' => $json,
                    // 'json' => $json,
                    'timeout' => 30,
                ]);
                // $response = $request->send();
                 return  $response;
        } catch (ClientException $e) {
            // For handling exception.
            echo Psr7\str($e->getRequest());
            echo Psr7\str($e->getResponse());
        }
    }

    public function upgrade(Request $request)
    {
        try {
            //code...
            $input = $request->all();
            $json = $request->json()->all();
            // return $json;
            $endpoint = env('KASPRO_PROD').'premium/upgrade';
            // return $endpoint;
          
                $client = new \GuzzleHttp\Client();
                // $request = $client->post($endpoint);
                // $request->setBody(json_decode($json));
                $response = $client->request('POST', $endpoint, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Token' => env('KASPRO_TOKEN'),
                        'Accept-Langauge' => 'EN',
                        'Nonce' => 'nonce'
                    ],
                    'json' => $json,
                    // 'json' => $json,
                    'timeout' => 50,
                ]);
                // $response = $request->send();
                 return  $response;
        } catch (ClientException $e) {
            // For handling exception.
            echo Psr7\str($e->getRequest());
            echo Psr7\str($e->getResponse());
        }
    }

    public function paymentP2P(Request $request)
    {
        try {
            //code...
            $input = $request->all();
            $json = $request->json()->all();
            // return $json;
            $endpoint = env('KASPRO_PROD').'kaspro/transfers';
            // return $endpoint;
          
                $client = new \GuzzleHttp\Client();
                // $request = $client->post($endpoint);
                // $request->setBody(json_decode($json));
                $response = $client->request('POST', $endpoint, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Token' => env('KASPRO_TOKEN'),
                        'Accept-Langauge' => 'EN',
                        'Nonce' => 'nonce'
                    ],
                    'json' => $json,
                    // 'json' => $json,
                    'timeout' => 30,
                ]);
                // $response = $request->send();
                 return  $response;
        } catch (ClientException $e) {
            // For handling exception.
            echo Psr7\str($e->getRequest());
            echo Psr7\str($e->getResponse());
        }
    }

    public function getFund(Request $request)
    {
        try {
            //code...
            $input = $request->all();
            $json = $request->json()->all();
            // return $json;
            $endpoint = env('KASPRO_PROD').'wallet/deallocate';
            // return $endpoint;
          
                $client = new \GuzzleHttp\Client();
                // $request = $client->post($endpoint);
                // $request->setBody(json_decode($json));
                $response = $client->request('POST', $endpoint, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Token' => env('KASPRO_TOKEN'),
                        'Accept-Langauge' => 'EN',
                        'Nonce' => 'nonce'
                    ],
                    'json' => $json,
                    // 'json' => $json,
                    'timeout' => 30,
                ]);
                // $response = $request->send();
                 return  $response;
        } catch (ClientException $e) {
            // For handling exception.
            echo Psr7\str($e->getRequest());
            echo Psr7\str($e->getResponse());
        }
    }
    public function subscribersAccountInquiry(Request $request)
    {

        try {
            //code...

        $input = http_build_query($request->query());
        $client = new \GuzzleHttp\Client();
        $endpoint = env('KASPRO_PROD').'partner/subscriber/wallet?'.$input;
            // return $endpoint;
        
        $response = $client->request('GET', $endpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic YWNjb3VudG51bWJlcjpwYXNz',
                'Token' => env('KASPRO_TOKEN'),  
                'Accept-Langauge' => 'EN'      
        ],
            'timeout' => 30,
        ]);

        return  $response;
        } catch (ClientException $e) {
            // For handling exception.
            echo Psr7\str($e->getRequest());
            echo Psr7\str($e->getResponse());
        }
    }

    public function login(Request $request)
    {

        try {
            //code...

        $headers = $request->headers->all(); // array
        $client = new \GuzzleHttp\Client();
        $endpoint = 'http://dev.kaspro.id/api/'.$request->input('msisdn').'/payu/session';

        $response = $client->request('GET', $endpoint, [
            'headers' => [
                'Token' => env('KASPRO_TOKEN'),
                'Accept-Langauge' => 'EN',
                'Authorization' => $request->input('pin'),
                ],
            'timeout' => 30,
        ]);

        return  $response;
    } catch (ClientException $e) {
        // For handling exception.
        echo Psr7\str($e->getRequest());
        echo Psr7\str($e->getResponse());
    }
       
    }

    public function P2PTransfer(Request $request)
    {
        $headers = $request->headers->all(); // array
        $input = $request->all();
        //return json_encode($input);
        $client = new \GuzzleHttp\Client();
        $endpoint = env('KASPRO_URL').'kaspro/transfers';

        $response = $client->request('POST', $endpoint, [
            'headers' => [
                'Content-Type' => $headers['content-type'],
                'Token' => $headers['token'],
                
                'Accept-Langauge' => $headers['accept-language'],
                'Nonce' => $headers['nonce'],
                ],
            'json' => $input,
            'timeout' => 30,
        ]);

        return json_encode($request->json()->all());
    }

    public function getOtp(Request $request)
    {
        try {
            //code...

        $headers = $request->headers->all(); // array
        $input = $request->all();
        //return json_encode($input);
        $client = new \GuzzleHttp\Client();
        $endpoint = 'http://dev.kaspro.id/api/'.$request->input('msisdn').'/register/otp';
        $response = $client->request('GET', $endpoint, [
            'headers' => [
                'Token' => env('KASPRO_TOKEN'),
                'Content-Type' => 'application/json'
                ],
            'timeout' => 30,
        ]);
         return $response;
        } catch (ClientException $e) {
            // For handling exception.
            echo Psr7\str($e->getRequest());
            echo Psr7\str($e->getResponse());
        }
    }

    public function kasproAgent(Request $request)
    {
        $headers = $request->headers->all(); // array
        $input = $request->all();
        //return json_encode($input);
        $client = new \GuzzleHttp\Client();
        $endpoint = env('KASPRO_PROD').'kasproagent';
        $response = $client->request('GET', $endpoint, [
            'headers' => [
                'token' => $tokenKaspro = env('KASPRO_TOKEN'),
                'Accept-Langauge' => 'ID',
                'Nonce' => 'nonce',
                ],
            'timeout' => 30,
        ]);

        return $response;
    }
}
