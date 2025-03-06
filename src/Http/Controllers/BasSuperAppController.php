<?php

namespace Bas\LaravelSdk\Http\Controllers;

use App\Models\User;
use bas;

use Bas\LaravelSdk\Services\BasService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class BasSuperAppController extends BaseController
{
    protected $basService;

    public function __construct(BasService $basService)
    {
        $this->basService = $basService;
    }

    public function dashboard()
    {


        return view('bas::dashboard');
    }

    public function basLogin() // Example action name
    {

        $authCodeJS = $this->basService->generateFetchAuthCodeJS();

        // Pass the JavaScript code to your Blade view
        return view('bas::login', ['authCodeJS' => $authCodeJS]);
    }

    public function getUserInfo(Request $request)
    {
        $authCode = $request->input('authCode');
        $userInfo = $this->basService->getUserInfo($authCode);
        if (isset($userInfo['data']['profile'])) {
            $profile = $userInfo['data']['profile'];
            $openId = $profile['open_id'];
            $userName = $profile['user_name'];
            $name = $profile['name'];
            $phone = $profile['phone'];

        // Save the user to your database
        $user = User::updateOrCreate([
            'email' => $phone."@bas.com",
        ], [
            'name' => $name,
            'password' => bcrypt($openId),
        ]);

        Auth::login($user);
        logger('User logged in successfully');
        // Redirect the user to the intended URL
        return redirect()->intended('/bas');
        } else {
            return redirect()->route('operation.failed');
            // throw new Exception('Profile data not found in the response');
        }
    }


    public function initiatePayment()
    {
        $orderId = rand(100000, 999999);
        $amount = rand(100, 999);
        $currency = 'YER';

        try {
            $transaction = $this->basService->initiateTransaction($orderId, $amount, $currency);

            if (isset($transaction['status']) && $transaction['status'] == 1) {

                $paymentJS =  $this->basService->generateBasPaymentJS($transaction['body']['trxToken'], $transaction['body']['order']);
                //dd($paymentJS);
                return view('bas::payment', ['paymentJS' => $paymentJS]);
            }

            return $transaction;
        } catch (\Exception $e) {
            return redirect()->route('operation.failed');
        }
    }
}
