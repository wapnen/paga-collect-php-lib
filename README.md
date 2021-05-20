# Paga Collect PHP API Library v1.0.0

## Business Services exposed by the library

- paymentRequest
- getBanks
- paymentStatus
- paymentHistory

For more information on the services listed above, visit the [Paga DEV website](https://developer-docs.paga.com/docs/php-library-1)

## How to use

`composer require paga/paga-collect`

```
require_once __DIR__ .'/vendor/autoload.php'


$collectClient = PagaCollectClient::builder()
                ->setApiKey("<apiKey>")
                ->setClientId("<publicId>")
                ->setPassword("<password>")
                ->setTest(true)
                ->build();
```

As shown above, you set the publicId, apiKey, password given to you by Paga, If you pass true as the value for setIsTest(), the library will use the test url as the base for all calls. Otherwise setting it to false will use the live url value you **pass** as the base.

### Paga Collect Service Functions

**Request Payment**

Registers a new request for payment between a payer and a payee. Once a payment request is initiated successfully, the payer is notified by the platform (this can be suppressed) and can proceed to authorize/execute the payment. Once the payment is fulfilled, a notification is sent to the supplied callback URL.

To make use of this function, call the **paymentRequest** inside PagaCollectClient which will return a JSONObject.

```
$data = ["referenceNumber" => "908w1111000001129",
    "amount" => 200,
    "callBackUrl" => "http://localhost:5000/core/webhook/paga",
    "currency" => "NGN",
    "expiryDateTimeUTC" => "2021-05-20T19:35:47",
    "isAllowPartialPayments" => false,
    "isSuppressMessages" => false,
    "payee" => ["bankAccountNumber"=>"XXXXXXXXX",
              "bankId" => "XXXXX-XXX-XXX-XXX-XXXXXX",
              "name" => "John Doe",
              "phoneNumber" => "XXXXXXXXXXX",
            "accountNumber" => "XXXXXXXXXXX"],
    "payer" => ["email" => "johndoe@gmail.com", 
                "name"=> "Foo Bar", 
                "bankId"=> "XXXXX-XXX-XXX-XXX-XXXXXX",
      ],
    "payerCollectionFeeShare"=> 1.0,
    "recipientCollectionFeeShare"=> 0.0,
    "paymentMethods"=> ["BANK_TRANSFER", "FUNDING_USSD"]
    ];

$paymentRequest = $collectClient->paymentRequest($data);$response = 

```

**Get Banks**

Retrieve a list of supported banks and their complementary unique ids on the bank. This is required for populating the payer (optional) and payee objects in the payment request model.
To make use of this function, call the **getBanks** inside PagaCollectClient which will return a JSONObject.

```
$data = ['referenceNumber' => "234455555"];
$getBanks = $collectAPI ->getBanks($data);
```

**Query Payment Request Status**

Query the current status of a submitted payment request.
To make use of this function, call the **paymentStatus** inside PagaCollectClient which will return a JSONObject.

```
$data = ['referenceNumber' => "234455555"];
$paymentStatus = $collectAPI ->paymentStatus($data);
```

**Payment Request History**

Get payment requests for a period between to give start and end dates. The period window should not exceed 1 month.
To make use of this function, call the **paymentHistory** inside PagaCollectClient which will return a JSONObject.

```
$data = [
    "referenceNumber" => "8235346400000099",
    "startDateTimeUTC" => "2021-04-21T19:15:22",
    "endDateTimeUTC" => "2021-05-18T19:15:22"
];
$paymentStatus = $collectAPI ->paymentHistory($data);
```

![Packagist Downloads](https://img.shields.io/packagist/dm/paga/paga-business?style=plastic)
![Packagist License](https://img.shields.io/packagist/l/paga/paga-business?style=plastic)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/paga/paga-business?style=plastic)
![Packagist Version](https://img.shields.io/packagist/v/paga/paga-business?style=plastic)
