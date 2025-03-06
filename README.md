# Laravel BAS SDK

This SDK simplifies integration with the BAS Mini Apps Platform in Laravel applications. It provides convenient services and facades to interact with BAS APIs for authentication, payment, and other functionalities.

## Installation

1.  **Require the package via Composer:**

    ```bash
    composer require basgate/laravel-sdk
    ```



3.  **Configure your BAS Credentials:**

    You **must** configure your BAS API credentials by adding environment variables to your application's `.env` file.

## Configuration

1.  **Edit your `.env` file:**

    Open your Laravel application's `.env` file and add the following environment variables, replacing the placeholder values with your actual BAS credentials.

    ```env
    BAS_BASE_URL=
    BAS_CLIENT_ID=
    BAS_CLIENT_SECRET=
    BAS_APP_ID=
    BAS_MERCHANT_KEY=
    BAS_ENVIRONMENT=staging # or production
    BAS_CALLBACK_URI=

    ```

2.  **Environment Variable Descriptions:**

    *   **`BAS_BASE_URL`**:  The base URL for the BAS API platform. This should be set to your Staging or Production API endpoint (e.g., `https://api-tst.basgate.com`).
    *   **`BAS_CLIENT_ID`**: Your Mini App's Client ID (App ID) provided by BAS when you register your Mini App.
    *   **`BAS_CLIENT_SECRET`**: Your Mini App's Client Secret . Keep this secret and do not share it publicly. Provided by BAS.
    *   **`BAS_APP_ID=`**: Your Mini App ID . Provided by BAS.
    *   **`BAS_MERCHANT_KEY`**: Merchant Key used to generate checksum/signature for API requests. Provided by BAS.
    *   **`BAS_ENVIRONMENT`**:  The environment your application is running in. Set to `staging` for development and testing, or `production` for live environments.

    **Important Security Notes:**

    *   **Never hardcode your BAS credentials directly into your code or configuration files.** Always use environment variables to keep your credentials secure and separate from your codebase.
    *   **Keep your `BAS_CLIENT_SECRET`,  `BAS_MERCHANT_KEY` secret and protected.** Do not commit them to public Git repositories or share them insecurely.




## Usage



*   `BAS`: For authentication-related functionalities (Login Flow), payment-related functionalities (Payment Flow) , and general BAS service functionalities.

**Example 

**PHP (Controller/Service):**

```php
use BAS;

// ... in your controller or service ...

$transactionStatus = BAS::checkTransactionStatus($orderId);

// Send refund request

$trxToken="Yac4bNFV3Yi3CsnMO9mLR4WRcJPPTqjGUFkzMDc0MTU=";
$refund= BAS::refund($trxToken);

// initiate Transaction and generateBasPaymentJS
        try {
            $transaction = BAS::initiateTransaction($orderId, $amount, $currency);

            if (isset($transaction['status']) && $transaction['status'] == 1) {

              $paymentJS =  BAS::generateBasPaymentJS($transaction['body']['trxToken'], $transaction['body']['order']);

              return view('bas::payment', ['paymentJS' => $paymentJS]);
            }

```

## Demo for login and payment
http://your_app_url/bas

- Here we present a simplified model for the process of obtaining the customer’s approval, requesting his data, and registering him as a new user in the system.:
- Create a new order from Bas and show payment methods
- You will find all this inside the BasSuperAppController.php file inside the library.

Steps to prepare the experimental environment on the simulator

![image](https://github.com/user-attachments/assets/6ca22c8a-d6be-4b4a-b3ea-904a6bb08787)

Here you should put your system link followed by /bas

![image](https://github.com/user-attachments/assets/47d2ffc4-32f5-4c2c-875c-943f70cebda3)

![image](https://github.com/user-attachments/assets/44a31ceb-cb27-48aa-9d0e-785c190e4b90)

![image](https://github.com/user-attachments/assets/45cdd6ad-5e6d-4a18-be5d-fcce7f23acb3)

try payment

![image](https://github.com/user-attachments/assets/8f0d290a-be58-4335-a1ba-1402c89559e0)




