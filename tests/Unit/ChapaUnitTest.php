<?php

use Chapa\Chapa\Chapa;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->chapa = new Chapa();
});

it('generates reference', function ($transactionPrefix) {
    $reference = Chapa::generateReference($transactionPrefix);

    expect($reference)
        ->toBeString()
        ->toContain($transactionPrefix);
})->with([
    'prefix',
    null
]);

describe('Payment', function () {
    it('initializes payment', function () {
        Http::fake([
            'https://api.chapa.co/v1/transaction/initialize' => Http::response([
                'status' => 'success',
                'message' => 'Hosted Link',
                'data' => [
                    'checkout_url' => 'https://checkout.chapa.co/checkout/payment/V38JyhpTygC9QimkJrdful9oEjih0heIv53eJ1MsJS6xG',
                ]
            ], 200)
        ]);

        $paymentData = [
            "amount" => "10",
            "currency" => "ETB",
            "email" => "abebech_bekele@gmail.com",
            "first_name" => "Bilen",
            "last_name" => "Gizachew",
            "phone_number" => "0912345678",
            "tx_ref" => "chewatatest-6669",
            "callback_url" => "https://webhook.site/077164d6-29cb-40df-ba29-8a00e59a7e60",
            "return_url" => "https://www.google.com/",
            "customization" => [
                "title" => "Payment for my favourite merchant",
                "description" => "I love online payments"
            ]
        ];
        $result = $this->chapa->initializePayment($paymentData);

        Http::assertSent(function (Request $request) use ($paymentData) {
            return $request->hasHeader('Authorization') &&
                $request->url() === 'https://api.chapa.co/v1/transaction/initialize' &&
                $request->body() === json_encode($paymentData);
        });

        expect($result)->toBeArray()
            ->and($result['status'])->toBe('success')
            ->and($result['message'])->toBe('Hosted Link')
            ->and($result['data'])->toHaveKeys(['checkout_url'])
            ->and($result['data']['checkout_url'])->toBe('https://checkout.chapa.co/checkout/payment/V38JyhpTygC9QimkJrdful9oEjih0heIv53eJ1MsJS6xG');
    });

    it('gets transaction ID from callback', function () {
        $tx_ref = 'test_transaction_123';

        request()->merge(['trx_ref' => $tx_ref]);
        expect($this->chapa->getTransactionIDFromCallback())->toBe($tx_ref);

        $respData = ['data' => ['id' => $tx_ref]];
        request()->merge(['resp' => json_encode($respData), 'trx_ref' => null]);
        expect($this->chapa->getTransactionIDFromCallback())->toBe($tx_ref);
    });

    it('verifies transaction', function () {
        $tx_ref = 'test_transaction_789';
        Http::fake([
            "https://api.chapa.co/v1/transaction/verify/{$tx_ref}" => Http::response([
                'message' => 'Payment details',
                'status' => 'success',
                'data' => [
                    "first_name" => "Bilen",
                    "last_name" => "Gizachew",
                    "email" => "abebech_bekele@gmail.com",
                    "currency" => "ETB",
                    "amount" => 100,
                    "charge" => 3.5,
                    "mode" => "test",
                    "method" => "test",
                    "type" => "API",
                    "status" => "success",
                    "reference" => "6jnheVKQEmy",
                    "tx_ref" => $tx_ref,
                    "created_at" => "2023-02-02T07:05:23.000000Z",
                    "updated_at" => "2023-02-02T07:05:23.000000Z"
                ]
            ], 200)
        ]);

        $result = $this->chapa->verifyTransaction($tx_ref);

        Http::assertSent(function (Request $request) use ($tx_ref) {
            return $request->hasHeader('Authorization') && $request->url() === "https://api.chapa.co/v1/transaction/verify/{$tx_ref}";
        });

        expect($result)->toBeArray()
            ->and($result['status'])->toBe('success')
            ->and($result['data']['tx_ref'])->toBe($tx_ref);
    });
});

describe('Transfer', function () {
    it('creates transfer', function () {
        $reference = '3241342142sfdd';

        Http::fake([
            'https://api.chapa.co/v1/transfers' => Http::response([
                "message" => 'Transfer Queued Successfully',
                'status' => 'success',
                "data" => $reference
            ], 200)
        ]);

        $transferData = [
            "account_name" => "Israel Goytom",
            "account_number" => "32423423",
            "amount" => "1",
            "currency" => "ETB",
            "reference" => $reference,
            "bank_code" => 656
        ];

        $result = $this->chapa->createTransfer($transferData);

        Http::assertSent(function (Request $request) use ($transferData) {
            return $request->hasHeader('Authorization') && $request->url() === 'https://api.chapa.co/v1/transfers' && $request->body() === json_encode($transferData);
        });

        expect($result)->toBeArray()
            ->and($result['status'])->toBe('success')
            ->and($result['data'])->toBe($reference);
    });

    it('verifies transfer', function () {
        $tx_ref = 'chewatatest-6669';

        Http::fake([
            "https://api.chapa.co/v1/transfers/verify/{$tx_ref}" => Http::response([
                "message" => "Transfer details",
                "status" => "success",
                "data" => [
                    "account_name" => "Israel Goytom",
                    "account_number" => "21312331234123",
                    "mobile" => null,
                    "currency" => "ETB",
                    "amount" => 100,
                    "charge" => 0,
                    "mode" => "live",
                    "transfer_method" => "bank",
                    'narration' => null,
                    "chapa_transfer_id" => "4d6a7cb7-0d51-4c27-9a19-cc3f066c85a3",
                    "bank_code" => 128,
                    "bank_name" => "Bunna Bank",
                    "cross_party_reference" => null,
                    "ip_address" => "UNKNOWN",
                    "status" => "success",
                    "tx_ref" => $tx_ref,
                    "created_at" => "2022-07-26T16:38:32.000000Z",
                    "updated_at" => "2023-01-10T07:09:08.000000Z"
                ]
            ], 200)
        ]);


        $result = $this->chapa->verifyTransfer($tx_ref);

        Http::assertSent(function (Request $request) use ($tx_ref) {
            return $request->hasHeader('Authorization') && $request->url() === "https://api.chapa.co/v1/transfers/verify/{$tx_ref}";
        });

        expect($result)->toBeArray()
            ->and($result['status'])->toBe('success')
            ->and($result['data']['tx_ref'])->toBe($tx_ref);
    });

    it('lists banks', function () {
        Http::fake([
            'https://api.chapa.co/v1/banks' => Http::response([
                'message' => 'Banks retrieved',
                'data' => [
                    [
                        "id" => 946,
                        "slug" => "cbe_bank",
                        "swift" => "CBETETAA",
                        "name" => "Commercial Bank of Ethiopia (CBE)",
                        "acct_length" => 13,
                        "country_id" => 1,
                        "is_mobilemoney" => null,
                        "is_active" => 1,
                        "is_rtgs" => null,
                        "active" => 1,
                        "is_24hrs" => 1,
                        "created_at" => "2022-03-17T04:21:18.000000Z",
                        "updated_at" => "2024-08-03T05:56:23.000000Z",
                        "currency" => "ETB"
                    ]
                ]
            ], 200)
        ]);

        $result = $this->chapa->getBanks();

        Http::assertSent(function (Request $request) {
            return $request->hasHeader('Authorization') && $request->url() === 'https://api.chapa.co/v1/banks';
        });

        expect($result)->toBeArray()
            ->and($result['message'])->toBe('Banks retrieved');
    });
});
