<?php

namespace Chapa\Chapa;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Chapa
{

    /**
     * Generates a unique reference
     * @param $transactionPrefix
     * @return string
     */


    protected $secretKey;
    protected $baseUrl;


    function __construct()
    {

        $this->secretKey = config('chapa.secretKey');
        $this->baseUrl = 'https://api.chapa.co/v1';

    }

    public static function generateReference(?string $transactionPrefix = NULL)
    {
        if ($transactionPrefix) {
            return $transactionPrefix . '_' . uniqid(time());
        }


        // Fallback to app name (config or default)
        $appName = config('app.name', 'Laravel');
        $appName = trim($appName);
        // Sanitize the app name to avoid invalid characters
        $appName = preg_replace('/[^A-Za-z0-9_-]+/', '_', $appName);

        return $appName . '_' . 'chapa_' . uniqid(time());
    }

    /**
     * Create Subaccount.
     * @param array $data
     * @return array
     */
    public function createSubaccount(array $data)
    {
        $response = Http::withToken($this->secretKey)->post(
            $this->baseUrl . '/sub-accounts',
            $data
        )->json();

        return $response;
    }

    /**
     * Reaches out to Chapa to initialize a payment
     * @param $data
     * @return object
     */
    public function initializePayment(array $data)
    {

        $payment = Http::withToken($this->secretKey)->post(
            $this->baseUrl . '/transaction/initialize',
            $data
        )->json();

        return $payment;
    }

    /**
     * Gets a transaction ID depending on the redirect structure
     * @return string
     */
    public function getTransactionIDFromCallback()
    {
        $transactionID = request()->trx_ref;

        if (!$transactionID) {
            $transactionID = json_decode(request()->resp)->data->id;
        }

        return $transactionID;
    }

    /**
     * Reaches out to Chapa to verify a transaction
     * @param $id
     * @return object
     */
    public function verifyTransaction($id)
    {
        $data = Http::withToken($this->secretKey)->get($this->baseUrl . "/transaction/" . 'verify/' . $id)->json();
        return $data;
    }

    /**
     * Reaches out to Chapa to create a transfer to a bank account or wallet
     * @param $data
     * @return object
     */
    public function createTransfer(array $data)
    {
        $transfer = Http::withToken($this->secretKey)->post(
            $this->baseUrl . '/transfers',
            $data
        )->json();

        return $transfer;
    }

    /**
     * Reaches out to Chapa to verify a transfer
     * @param $id
     * @return object
     */
    public function verifyTransfer($id)
    {
        $data = Http::withToken($this->secretKey)->get($this->baseUrl . "/transfers/" . 'verify/' . $id)->json();
        return $data;
    }

    /**
     * Validate incoming webhook event is from Chapa.
     *
     * @param $request
     * @return boolean
     */
    public function validateWebhook($request)
    {
        $signature = $request->header('x-chapa-signature');
        $expectedSignature = hash_hmac('sha256', $request->getContent(), config('chapa.webhookSecret'));

        if ($signature !== $expectedSignature) {
            return false;
        }
        return true;
    }

    /**
     * Initiates a bulk transfer.
     * @param array $data
     * @return array
     */
    public function bulkTransfer(array $data)
    {
        $response = Http::withToken($this->secretKey)->post(
            $this->baseUrl . '/bulk-transfers',
            $data
        )->json();

        return $response;
    }

    /**
     * Gets a list of banks
     * 
     * @return array
     */
    public function getBanks()
    {
        // Send a GET request to Chapa's /v1/banks endpoint
        $response = Http::withToken($this->secretKey)
            ->get($this->baseUrl . "/banks")
            ->json();

        // Return the response data
        return $response;
    }

}
