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

        $this->secretKey = env('CHAPA_SECRET_KEY');
        $this->baseUrl = 'https://api.chapa.co/v1';
    }

    public static function generateReference(String $transactionPrefix = NULL)
    {
        if ($transactionPrefix) {
            return $transactionPrefix . '_' . uniqid(time());
        }

        return env('APP_NAME') . '_' . 'chapa_' . uniqid(time());
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
        $data =  Http::withToken($this->secretKey)->get($this->baseUrl . "/transaction/" . 'verify/' . $id)->json();
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
        $data =  Http::withToken($this->secretKey)->get($this->baseUrl . "/transfers/" . 'verify/' . $id)->json();
        return $data;
    }

    /**
     * Lists all banks that are supported
     * @return object
     */
    public function getBanks()
    {
        return Http::withToken($this->secretKey)->get($this->baseUrl . "/banks")->json();
    }
}
