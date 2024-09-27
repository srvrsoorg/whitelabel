<?php

namespace App\Http\Utilities;
use App\Models\Admin\{BasicDetail, SiteSetting};
use Aws\Lightsail\LightsailClient;
use Storage;
use App\Enums\CloudProvider as CloudProviderEnum;

class Client {

    // Make a request to the Serveravatar API
    public static function serveravatar($endpoint, $method, $requestData = null)
    {
        try {
            // Get the Serveravatar API key from the database
            $licenseKey = BasicDetail::where('key', 'license_key')->first();
            
            if (!$licenseKey) {
                // Return an error response if the API key is not found
                return ["error" => true, "message" => "Please set license key."];
            }

            // Initialize GuzzleHttp client with headers
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'Authorization' =>  $licenseKey->value,
                    'content-type' => 'application/json',
                    'accept' => 'application/x-www-form-urlencoded',
                    'User-Agent' => 'selfhosted'
                ]
            ]);

            // Make a request to the Serveravatar API endpoint
            $response = $client->request($method, config('app.serveravatar') . "/" . $endpoint, [
                'connect_timeout' => 5000,
                'form_params' => $requestData,
            ]);
            
            if($response->getStatusCode() == 200 || $response->getStatusCode() == 201 || $response->getStatusCode() == 202) {
                return json_decode($response->getBody(), true);
            } else {
                return ["error" => true, "body" => (string)$response->getBody()];
            }        
            
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            report($e);
            $response = $e->getResponse();
            $responseBody = $response->getBody()->getContents();

            $errorResponse = json_decode($responseBody, true);

            // Extract the error message
            $errorMessage = isset($errorResponse['message']) ? $errorResponse['message'] : 'Unknown server error.';

            // Handle the error message as needed, and return it with the same status code
            return ["error" => true, 'message' => $errorMessage];
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            report($e);
            $exception = (string)$e->getResponse()->getBody();
            $exception = json_decode($exception);
            return ["error" => true, "message" => isset($exception->message) ? $exception->message : "Something went wrong."];
        } catch (\Exception $e) {
            report($e->getMessage());
            // Handle other exceptions
            return ["error" => true, "message" => $e->getMessage()??"Something went wrong."];
        }
    }

    // Amazon lightsail driver
    public static function lightsail($key, $secret, $region)
    {
        return new LightsailClient([
            'version' => 'latest',
            'region' => $region,
            'credentials' => array(
                'key' => $key,
                'secret' => $secret
            )
        ]);
    }

    // Linode Client
    public static function linode($endpoint, $method, $token, $requestData = null)
    {
        try {
            // GuzzleHttp
            $client = new \GuzzleHttp\Client(['verify'=>false,'headers' => ['Authorization' =>  "Bearer ".$token, 'content-type' => 'application/json']]);
            $response = $client->request($method, "https://api.linode.com/v4/".$endpoint,[
                'connect_timeout' => 300,
                'body' =>  $requestData,
            ]);

            if($response->getStatusCode() == 200 || $response->getStatusCode() == 201 || $response->getStatusCode() == 202) {
                return (string)$response->getBody();
            } else {
                return ["error" => true, "body" => (string)$response->getBody()];
            }
        } catch (\GuzzleHttp\Exception\ConnectException $ex) {
            // Error response
            return ["error" => true, "message" => "Something went really wrong while creating instance in linode!"];
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $exception = (string)$e->getResponse()->getBody();
            $exception = json_decode($exception);
            report($e);
           
            if(isset($exception->errors) && isset($exception->errors[0]->reason)) {
                return ["error" => true, "message" => $exception->errors[0]->reason];
            } else {
                return ["error" => true, "message" => "Something went really wrong!"];
            }
        } catch(\Exception $e){
            $exception = (string)$e->getResponse()->getBody();
            $exception = json_decode($exception);
            report($e);

            if(isset($exception->errors) && isset($exception->errors[0]->reason)) {
                return ["error" => true, "message" => $exception->errors[0]->reason];
            } else {
                return ["error" => true, "message" => "Something went really wrong!"];
            }
        }
    }

    // hetzner Client
    public static function hetzner($endpoint, $method, $token, $requestData = null)
    {
        try {
            // GuzzleHttp
            $client = new \GuzzleHttp\Client(['verify'=>false,'headers' => ['Authorization' =>  "Bearer ".$token, 'content-type' => 'application/json']]);
            $response = $client->request($method, "https://api.hetzner.cloud/v1/".$endpoint,[
                'connect_timeout' => 300,
                'body' =>  $requestData,
            ]);

            if($response->getStatusCode() == 200 || $response->getStatusCode() == 201 || $response->getStatusCode() == 202) {
                return (string)$response->getBody();
            } else {
                return ["error" => true, "message" => (string)$response->getBody()];
            }
        } catch (\GuzzleHttp\Exception\ConnectException $ex) {
            // Error response
            return ["error" => true, "message" => "Something went really wrong while creating instance in hetzner!"];
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $exception = (string)$e->getResponse()->getBody();
            $exception = json_decode($exception);
            report($e);

            return ["error" => true, "message" => $exception->error->message];
        } catch(\Exception $e){
            report($e);
            return ["error" => true, "message" => "Something went really wrong!"];
        }
    }

    // Vultr Client
    public static function vultr($endpoint, $method, $token, $requestData = null)
    {
        try {
            // GuzzleHttp
            $client = new \GuzzleHttp\Client(['verify'=>false,'headers' => ['Authorization' =>  "Bearer ".$token, 'content-type' => 'application/json']]);
            $response = $client->request($method, "https://api.vultr.com/v2/".$endpoint,[
                'connect_timeout' => 300,
                'body' =>  $requestData,
            ]);

            if($response->getStatusCode() == 200 || $response->getStatusCode() == 201 || $response->getStatusCode() == 202) {
                return (string)$response->getBody();
            } else {
                return ["error" => true, "body" => (string)$response->getBody()];
            }
        } catch (\GuzzleHttp\Exception\ConnectException $ex) {
            // Error response
            return ["error" => true, "message" => "Something went really wrong while creating instance in Vultr!"];
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $exception = (string)$e->getResponse()->getBody();
            $exception = json_decode($exception);
            report($e);

            return ["error" => true, "message" => $exception->error];
        } catch(\Exception $e){
            report($e);
            return ["error" => true, "message" => "Something went really wrong!"]; 
        }
    }

    //digital ocean
    public static function digitalOcean($endpoint, $method, $token, $requestData = null)
    {
        try {
            // GuzzleHttp
            $client = new \GuzzleHttp\Client(['verify'=>false,'headers' => ['Authorization' =>  "Bearer ".$token, 'content-type' => 'application/json']]);
            $response = $client->request($method, "https://api.digitalocean.com/v2/".$endpoint,[
                'connect_timeout' => 300,
                'body' =>  $requestData,
            ]);

            if($response->getStatusCode() == 200 || $response->getStatusCode() == 201 || $response->getStatusCode() == 202 || $response->getStatusCode() == 204) {
                return (string)$response->getBody();
            } else {
                return ["error" => true, "body" => (string)$response->getBody()];
            }
        } catch (\GuzzleHttp\Exception\ConnectException $ex) {
            // Error response
            return ["error" => true, "message" => "Something went really wrong while creating droplet in DigitalOcean!"];
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $exception = (string)$e->getResponse()->getBody();
            $exception = json_decode($exception);
            report($e);

            return ["error" => true, "message" => $exception->message];
        } catch(\Exception $e){
            $exception = (string)$e->getResponse()->getBody();
            $exception = json_decode($exception);
            report($e);

            return ["error" => true, "message" => $exception->message];
        }
    }

    // store ssh key
    public static function storeServerInstanceSshKey($key, $cloudProvider, $keyName, $region = null)
    {
        try {
            $publicKey = Storage::disk('cloudProviderSSHKeys')->get($key.'.pub');

            if($cloudProvider->provider == CloudProviderEnum::LIGHTSAIL()) {

                try {
                    $client = self::lightsail($cloudProvider->access_key, $cloudProvider->access_secret, $region);

                    return $client->importKeyPair([
                        "keyPairName" =>  $keyName,
                        "publicKeyBase64" => $publicKey
                    ]);
                } catch(\Aws\Exception\AwsException $aws) {
                    return ["error" => true, "message" => $aws->getAwsErrorMessage()];
                }

            } else if($cloudProvider->provider == CloudProviderEnum::DIGITALOCEAN()) {

                $requestData = ['name' => $keyName, 'public_key' => $publicKey];
                return self::digitalOcean("account/keys", "POST", $cloudProvider->access_key, json_encode($requestData));

            } else if($cloudProvider->provider == CloudProviderEnum::LINODE()) {

                $requestData = ['label' => $keyName, 'ssh_key' => $publicKey];
                return self::linode("profile/sshkeys", "POST", $cloudProvider->access_key, json_encode($requestData));
                        
            } else if($cloudProvider->provider == CloudProviderEnum::HETZNER()) {

                $requestData = ['name' => $keyName, 'public_key' => $publicKey];
                return self::hetzner("ssh_keys", "POST", $cloudProvider->access_key, json_encode($requestData));

            } else if($cloudProvider->provider == CloudProviderEnum::VULTR()) {

                $requestData = ['name' => $keyName, 'ssh_key' => $publicKey];
                return self::vultr("ssh-keys", "POST", $cloudProvider->access_key, json_encode($requestData));
            }
        } catch(\Exception $e) {
            return ["error" => true, "message" => $e->getMessage()];
        }
    }

    // Delete Server Instance
    public static function deleteServerInstance($serverInstance)
    {
        try {
            $cloudProvider = $serverInstance->cloudProvider;
           
            if($cloudProvider) {
                if($cloudProvider->provider == CloudProviderEnum::LIGHTSAIL()) {
                    try {
                        $client = self::lightsail($cloudProvider->access_key, $cloudProvider->access_secret, $serverInstance->region);
                        $client->deleteInstance([
                            'instanceName' => $serverInstance->name,
                        ]);
                    } catch (Aws\Exception\AwsException $aws) {
                        return true;
                    }
                } else if($cloudProvider->provider == CloudProviderEnum::DIGITALOCEAN()) {
                    try {
                        self::digitalOcean("droplets/{$serverInstance->instance_id}", "delete", $cloudProvider->access_key);
                    } catch (\Exception $e) {
                        return true;
                    }
                } else if($cloudProvider->provider == CloudProviderEnum::LINODE()) {
                    try {
                        self::linode("linode/instances/{$serverInstance->instance_id}", "delete", $cloudProvider->access_key);
                    } catch (\Exception $e) {
                        return true;
                    }
                } else if($cloudProvider->provider == CloudProviderEnum::HETZNER()) {
                    try {
                        self::hetzner("servers/{$serverInstance->instance_id}", "delete", $cloudProvider->access_key);
                    } catch (\Exception $e) {
                        return true;
                    }
                } else if($cloudProvider->provider == CloudProviderEnum::VULTR()) {
                    try {
                        self::vultr("instances/{$serverInstance->instance_id}", "delete", $cloudProvider->access_key);
                    } catch (\Exception $e) {
                        return true;
                    }
                }
            }

            $serverInstance->delete();
            return true;
        } catch(\Exception $e) {
            if($serverInstance) {
                $serverInstance->delete();
            }
            return true;
        }
    }
}