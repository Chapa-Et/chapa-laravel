<h1 align="center">
<div align="center">
  <a href="http://chapa.co/" target="_blank">
    <img src="https://assets.chapa.co/assets/images/chapa-logo.svg" width="320" alt="Chapa Logo"/>
  </a>
  <p align="center">Official Laravel package for Chapa's API (Laravel 5,6,7,9,10,11,12)</p>
</div>
</h1>

If your are doing a Laravel project and want to integrate Chapa's payment
solution, this package would help big time.

Go to [Chapa](https://dashboard.chapa.co/) to signup and get your secret key

## Documentation

Please visit [Chapa](https://developer.chapa.co/docs/accept-payments/) for full documentation.

## Guide

Please visit [Developers Guide](https://developer.chapa.co/laravel-sdk/) for full guide and examples.

### Usage

You can check [this](https://github.com/Chapa-Et/sdk-examples/tree/master/chapa-laravel-example) sample Laravel code as a reference.

### Configuration

Open your .env file and add your public key, secret keys, and other environment variables like this:

```
CHAPA_SECRET_KEY=CHAPA-SECK-xxxxxxxxxxxxxxxxxxxxx-X
```

## Features

The current features have been implemented

- Initiate Payment
- Payment verification
- Create a Transfer
- Verify a Transfer
- Validate a Webhook Request
- Bulk Transfer
- Create Subaccount

## API Reference
### Create Subaccount

```https
  GET https://api.chapa.dev/v1/transaction/sub-accounts
```

| Parameter | Type     | Required | Description                                                                                                          |
| :-------- | :------- | :------- | :------------------------------------------------------------------------------------------------------------------- |
| `business_name`     | `string` | **Yes**. | The name of the business or vendor. |
| `account_name`     | `string` | **Yes**. | The name of the account holder, which must match the bank account details. |
| `bank_code`     | `integer` | **Yes**. | The ID of the bank where the subaccount is held. You can retrieve this from the "Get Banks" endpoint. |
| `account_number`     | `string` | **Yes**. | The bank account number for the subaccount |
| `split_type`     | `string` | **Yes**. | Defines how the payment will be split. Can be "percentage" or "flat". |
| `split_value`     | `float` | **Yes**. | The value of the split. If split_type is percentage, this represents a percentage (e.g., 0.03 for 3%). |
|     |  |  | If split_type is flat, this represents a fixed amount in ETB (e.g., 25 for 25 Birr). |

### Collecting Customer Information

```https
  POST https://api.chapa.co/v1/transaction/initialize
```

| Parameter                    | Type     | Required | Description                                                                                                                                                                                         |
| :--------------------------- | :------- | :------- | :-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `key`                        | `string` | **Yes**. | This will be your public key from Chapa. When on test mode use the test key, and when on live mode use the live key.                                                                                |
| `email`                      | `string` | **No**. | A customer’s email. address.                                                                                                                                                                        |
| `phone_number`                      | `numeric` | **No**. | A customer’s phone number. address.                                                                                                                                                                        |
| `amount`                     | `string` | **Yes**. | The amount you will be charging your customer.                                                                                                                                                      |
| `first_name`                 | `string` | **No**. | A customer’s first name.                                                                                                                                                                            |
| `last_name`                  | `string` | **No**. | A customer’s last name.                                                                                                                                                                             |
| `tx_ref`                     | `string` | **Yes**. | A unique reference given to each transaction.                                                                                                                                                       |
| `callback_url`               | `string` | **No**. | Function that runs when payment is successful. This should ideally be a script that uses the verify endpoint on the Chapa API to check the status of the transaction.    |
| `return_url`               | `string` | **No**. | A web address provided by the merchant to a payment gateway during payment integration. It serves as the destination where the payment gateway sends the customer after completing a payment transaction.                          |
| `currency`                   | `string` | **Yes**. | The currency in which all the charges are made. Currency allowed is ETB.                                                                                                                            |
| `customization[tiitle] `     | `string` | **No**.  | The customizations field (optional) allows you to customize the look and feel of the payment modal. You can set a logo, the store name to be displayed (title), and a description for the payment. |
| `customization[description]` | `string` | **No**.  | The customizations field (optional) allows you to customize the look and feel of the payment modal.                                                                                                 |
| `customization[subaccounts][id] `     | `string` | **Yes**.  | The customization field is optional. The subaccounts field within customization is also optional, but if provided, each subaccount must include an id (required). The payment will be split among the subaccounts based on the specified split_type and split_value. |

### Verify Payments

```https
  GET https://api.chapa.dev/v1/transaction/verify/{tx-ref}
```

| Parameter | Type     | Required | Description                                                                                                          |
| :-------- | :------- | :------- | :------------------------------------------------------------------------------------------------------------------- |
| `key`     | `string` | **Yes**. | This will be your public key from Chapa. When on test mode use the test key, and when on live mode use the live key. |



### Verify Transfers

```https
  GET https://api.chapa.dev/v1/transfers/verify/{tx-ref}
```

| Parameter | Type     | Required | Description                                                                                                          |
| :-------- | :------- | :------- | :------------------------------------------------------------------------------------------------------------------- |
| `key`     | `string` | **Yes**. | This will be your public key from Chapa. When on test mode use the test key, and when on live mode use the live key. |



### Bulk Transfers

```https
  GET https://api.chapa.dev/v1/bulk-transfers
```

| Parameter | Type     | Required | Description                                                                                                          |
| :-------- | :------- | :------- | :------------------------------------------------------------------------------------------------------------------- |
| `title`     | `string` | **Yes**. | This will be your title or desciption for bulk transfer. |
| `currency`     | `string` | **Yes**. | The currency for the transfers (e.g., ETB for Ethiopian Birr). |
| `bulk_data`     | `array` | **Yes**. |An array of individual transfers containing the following fields: |
| `bulk_data[].account_name`     | `string` | **Yes**. | The name of the account holder for the transfer. |
| `bulk_data[].account_number`     | `string` | **Yes**. | The account number to which the transfer will be made. |
| `bulk_data[].amount`     | `integer` | **Yes**. | The amount to be transferred. |
| `bulk_data[].reference`     | `string` | **Yes**. | A unique reference for the individual transfer (used for tracking purposes). |
| `bulk_data[].bank_code`     | `integer` | **Yes**. | The bank code of the recipient's bank (get the code via Chapa’s banks API). |


### Get Banks
The Get Banks API retrieves a list of banks available for transfers via the Chapa API. This is useful when you need to display a list of banks for users to select from or when you need bank codes for initiating transactions.



### Verify Webhooks
This function validates incoming webhook events from Chapa to ensure their authenticity. It checks the signature of the webhook against an expected signature, generated using the webhook payload and a secret key. If the signatures match, the webhook is considered valid.

#### Parameters:
- **`$request`** *(Illuminate\Http\Request)*: The incoming HTTP request containing the webhook payload and headers. The key header used for validation is:
  - **`x-chapa-signature`**: A header sent by Chapa containing the HMAC-SHA256 signature of the webhook payload. This signature is used to verify the authenticity of the webhook.

#### Returns:
- **`boolean`**:
  - `true`: If the webhook is valid (i.e., the signature matches).
  - `false`: If the webhook is invalid (i.e., the signature does not match).



### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

### Security

If you discover any security related issues, please email kidusy@chapa.co instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
