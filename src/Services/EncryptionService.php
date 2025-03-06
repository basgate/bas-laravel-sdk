<?php

namespace Bas\LaravelSdk\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EncryptionService
{
    protected $merchantKey;

    private $iv;

    public function __construct()
    {

        $this->iv = config('bas.iv');

        $this->merchantKey = config('bas.merchant_key');


    }

    public function generateSignature($params) {
        if(!is_array($params) && !is_string($params)){
            throw new Exception("string or array expected, ".gettype($params)." given");
        }
        if(is_array($params)){

            $params = $this->getStringByParams($params);
        }
        return $this->generateSignatureByString($params);
    }
    private function getStringByParams($params) {
        ksort($params);
        $params = array_map(function ($value){
            return ($value !== null && strtolower($value) !== "null") ? $value : "";
        }, $params);
        return implode("|", $params);
    }
    private function generateSignatureByString($params){
        $salt = $this->generateRandomString(4);
        return $this->calculateChecksum($params,$salt);
    }
    private function generateRandomString($length) {
        $data = "9876543210ZYXWVUTSRQPONMLKJIHGFEDCBAabcdefghijklmnopqrstuvwxyz!@#$&_";
        return substr(str_shuffle(str_repeat($data, $length)), 0, $length);

    }
    private function calculateChecksum($params, $salt){
        $hashString = $this->calculateHash($params, $salt);
        return $this->encrypt($hashString);
    }
    private function calculateHash($params, $salt) {
        return hash("sha256", $params . "|" . $salt) . $salt;
    }
    private function encrypt($input) {
        $key = html_entity_decode($this->merchantKey);
        $password = substr(hash('sha256', $key, true), 0, 32);
        $data = openssl_encrypt($input, 'aes-256-cbc', $password, OPENSSL_RAW_DATA, $this->iv);
        return base64_encode($data);
    }
    public function verifySignature($params, $checksum){
        if(!is_array($params) && !is_string($params)){
            throw new Exception("string or array expected, ".gettype($params)." given");
        }
        if(isset($params['CHECKSUMHASH'])){
            unset($params['CHECKSUMHASH']);
        }
        if(is_array($params)){
            $params = $this->getStringByParams($params);
        }
        return $this->verifySignatureByString($params,$checksum);
    }

    private function verifySignatureByString($params, $checksum)
    {
        $bas_hash = $this->decrypt($checksum);

        $salt = substr($bas_hash, -4);
        return $bas_hash === $this->calculateHash($params, $salt);
    }
    private function decrypt($encrypted) {
        $key = html_entity_decode($this->merchantKey);
        $password = substr(hash('sha256', $key, true), 0, 32);
        return openssl_decrypt($encrypted , "aes-256-cbc" ,$password,0, $this->iv);

    }

}
