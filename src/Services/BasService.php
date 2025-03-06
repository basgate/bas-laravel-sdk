<?php

namespace Bas\LaravelSdk\Services;

use Composer\InstalledVersions;
use Exception;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use JsonException;
use RuntimeException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;
use InvalidArgumentException;
class BasService
{
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;
    protected $appId;

    protected $callbackUrl;
    protected $EncryptionService;
    protected $header;

    protected $tokenEndpoint;
    protected $userInfoEndpoint;
    protected $refundPaymentEndpoint;
    protected $transactionStatusEndpoint;
    protected $transactionInitiateEndpoint;

    protected $notificationsEndpoint;
    protected $environment;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->EncryptionService = $encryptionService;



        $this->baseUrl = config('bas.base_url');
        $this->clientId = config('bas.client_id');
        $this->clientSecret = config('bas.client_secret');
        $this->appId = config('bas.app_id');
        $this->callbackUrl = config('bas.callback_uri');
        $this->environment = config('bas.environment');
        $this->tokenEndpoint = config('bas.token_endpoint');
        $this->userInfoEndpoint = config('bas.user_info_endpoint');
        $this->refundPaymentEndpoint = config('bas.refund_payment_endpoint');
        $this->transactionStatusEndpoint = config('bas.transaction_status_endpoint');
        $this->transactionInitiateEndpoint = config('bas.transaction_initiate_endpoint');
        $this->notificationsEndpoint = config('bas.notifications_endpoint');


        $this->header = [
            'Content-Type' => 'application/json',
            'x-client-id' => $this->clientId,
            'x-app-id' => $this->appId,
            'x-sdk-version' => InstalledVersions::getPrettyVersion('basgate/laravel-sdk'),
            'x-environment' => '$this->environment',
            'correlationId' => '',
            'x-sdk-type' => 'Laravel',
        ];
    }



    public function generateFetchAuthCodeJS(string $clientId = null): string
    {
        $clientId = $clientId ?: $this->clientId;
        return <<<JS
    async function basFetchAuthCode(){
        try {
            const result = await JSBridge.call('basFetchAuthCode', {
                clientId: "{$clientId}"
            });
            console.log('clientId :', "{$clientId}");
            console.log('basFetchAuthCode result:',JSON.stringify(result));
            if (result && result.data.auth_id) {
               // alert(JSON.stringify(result));
                console.log("✅ Received auth_code:", result.data.auth_id);
                return result.data.auth_id;
            } else {
                console.error("❌ auth_code not received.");
                return null;
            }
        } catch (error) {
            console.error("❌ Error in basFetchAuthCode:", error);
            return null;
        }
    }

    window.addEventListener('JSBridgeReady', async function(event) {
        console.log('JSBridgeReady fired');
        const authCodeResult = await basFetchAuthCode();
        if (authCodeResult) {
            // Send authCodeResult to the server and handle the response
            fetch('get-user-info', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ authCode: authCodeResult })
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });



JS;
    }



    public function getUserInfo($authCode)
    {
        $endpoint = $this->userInfoEndpoint;

        $body = [

            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'authorization_code',
            'code' => $authCode,
            'redirect_uri' =>  $this->callbackUrl,
        ];
        return $this->callApi($endpoint, $body,['as_form' => true]);

    }

    public function generateBasPaymentJS($trxToken ,$order )
    {

        return <<<JS
        function basPayment(){JSBridge.call('basPayment', {
                    "amount": {
                        "value": "{$order['amount']['value']}",
                        "currency": "{$order['amount']['currency']}"
                    },
                    "orderId": "{$order['orderId']}",
                    "trxToken": "{$trxToken}",
                    "appId": "{$this->appId}"
        }).then(function(result) {
                console.log('basPayment result:', JSON.stringify(result));

                if (result && result.status === 1) {
                      console.log('Seccses Payment:', JSON.stringify(result));
                    return result;
                } else {
                            console.log('Filed Payment:', JSON.stringify(result));
                    return result;
                }
            });
                }
                    window.addEventListener('JSBridgeReady', function(event) {
                    console.log('JSBridgeReady fired');
                     basPayment()
                });
        JS;
    }


    public function refund($trxToken, $reason = 'test')
    {
        $endpoint = $this->refundPaymentEndpoint;
        $body = [
            'trxToken' => $trxToken,
            'reason' => $reason
        ];
        return $this->callApi($endpoint, $body, ['as_form' => false], true, 'POST');


    }

    /**
     * @throws Exception
     */
    public function sendNotifications($orderId, $text)
    {
        $body = [
            'orderId' => $orderId,
            'templateName' => '',
            'appId' => $this->appId,
            'firebasePayload' => [
                'deepLink' => '',
                'service' => '',
            ],
            'orderParams' => [
                'text' => $text,

            ],
            'extraPayload' => [
                'extraValue' => '',
                'extraIntValue' => 1,
            ],
        ];
        return $this->callApi($this->notificationsEndpoint, $body, ['as_form' => false], true, 'POST');


    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function checkTransactionStatus($orderId)
    {



        $requestTimestamp = time() * 1000;

        $body = [
            'appId' => $this->appId,
            'orderId' => $orderId,
            'requestTimestamp' => $requestTimestamp
        ];
        $encodeBody = json_encode($body, JSON_THROW_ON_ERROR);

        $signature = $this->EncryptionService->generateSignature($encodeBody);

        $data = [
            'head' => [
                'signature' => $signature,
                'requestTimestamp' => $requestTimestamp,
            ],
            'body' => $body
        ];

        $response = $this->callApi($this->transactionStatusEndpoint, $data, ['as_form' => false], false, 'POST');
        return $this->handleTransactionResponse($response);
    }


    /**
     * @throws JsonException
     * @throws Exception
     */
    public function initiateTransaction($orderId,$amount, $currency )
    {
        $endpoint = $this->transactionInitiateEndpoint;
        $requestTimestamp = time() * 1000;

        $body = [
            'amount' => ['value' => $amount, 'currency' => $currency],
            'ordertype' => 'PayBill',
            'orderId' => $orderId,
            'requestTimestamp' => $requestTimestamp,
            'appId' => $this->appId
        ];

        $signature = $this->EncryptionService->generateSignature(json_encode($body, JSON_THROW_ON_ERROR));
        $data = ['head' => ['signature' => $signature, 'requestTimestamp' => $requestTimestamp], 'body' => $body];
        $response = $this->callApi($endpoint, $data);

        return $this->handleTransactionResponse($response);
    }



    public function requestNewToken()
    {
        $endpoint = $this->tokenEndpoint;

        $body = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => "client_credentials",
            'redirect_uri' =>  $this->callbackUrl,
        ];
        $response = $this->callApi($endpoint, $body,['as_form' => true]);
        return $response['access_token'];

    }
    /**
     * @throws Exception
     */

    private function handleTransactionResponse($response)
    {
        if ($response['status'] === 1 && $response['code'] === '1111') {
            $params = $response['body']['trxToken'] . $response['body']['trxStatus'] . $response['body']['order']['orderId'];
            if ($this->EncryptionService->verifySignature($params, $response['head']['signature'])) {
                return $response;
            }
            //throw new ApiException('Signature verification failed for transaction response.', $response, 400); // رمي استثناء
            return ['status' => 'error', 'message' => 'Signature verification failed', 'response' => $response]; // إرجاع مصفوفة خطأ
        }
        if ($response['status'] === 0) {
            return $response;
        }

        throw new RuntimeException('Transaction failed: ' . json_encode($response)); // تضمين الاستجابة في رسالة الخطأ
    }




    public function callApi(
        string $endpoint,
        mixed $data,
        array $options = [],
        bool $token = false,
        string $method = 'POST'

    ) {
        try {
            $headers = $this->header;
            // التحقق من صحة HTTP method
            $method = strtoupper($method);
            $validMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
            if (!in_array($method, $validMethods)) {
                throw new InvalidArgumentException("Invalid HTTP method: $method");
            }

            // إذا كان الإرسال كـ form-data، قم بإزالة Content-Type
            if ($options['as_form'] ?? false) {
                unset($headers['Content-Type']);
            }

            $http = Http::timeout(20)
                ->withHeaders($headers);

            if ($options['as_form'] ?? false) {
                $http->asForm();
            }
            if ($token) {
                //$http->withToken($this->requestNewToken());
                $http->withHeaders([
                    'Authorization' => 'Bearer '.$this->requestNewToken()
                ]);
            }

            // تسجيل الطلبات للتصحيح
            $http->beforeSending(function ($request) use ($endpoint) {
                Log::debug("API Request to $endpoint", [
                    'url' => $request->url(),
                    'headers' => $request->headers(),
                    'body' => $request->body()
                ]);
            });

            $response = $http->$method(
                $this->baseUrl . $endpoint,
                $data
            );

            // تسجيل الاستجابة
            Log::debug("API Response from $endpoint", [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            $errorMessage = "API Error [{$response->status()}]: " . $response->body();
            throw new Exception($errorMessage);

        } catch (ConnectException $e) {
            $message = "Connection failed: " . $e->getMessage();
            Log::error($message);
            throw new Exception($message);
        } catch (RequestException $e) {
            $message = "Request error: " . $e->getMessage();
            Log::error($message);
            throw new Exception($message);
        } catch (Exception $e) {
            Log::error('Unexpected error: ' . $e->getMessage());
            throw new Exception('API communication error');
        }
    }


}
