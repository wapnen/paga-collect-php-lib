<?php
/**
 * Paga Collect Library.
 *
 * PHP version >=5
 *
 * @category  PHP
 * @package   PagaBusiness
 * @author    PagaDevComm <devcomm@paga.com>
 * @copyright 2020 Pagatech Financials
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      https://packagist.org/packages/paga/paga-business
 */

 namespace PagaCollect;

 use Exception;
 use Monolog\Handler\StreamHandler;
 use Monolog\Logger;

 $logger = new Logger('stderr');
 $logger->pushHandler(new StreamHandler('php://stderr', Logger::WARNING));

    /**
     * PagaBusinessClient  class
     *
     * @category  PHP
     * @package   PagaCollect
     * @author    PagaDevComm <devcomm@paga.com>
     * @copyright 2020 Pagatech Financials
     * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
     * @link      https://packagist.org/packages/paga/paga-business
     * @since     1.0.0
     */
class PagaCollectClient
{
    public $test_server = "https://beta-collect.paga.com/";
    public $live_server = "https://collect.paga.com/";



    /**
     * __construct function
     *
     * @param object $builder Builder Object
     */
    public function __construct($builder)
    {
        $this->clientId =$builder->clientId;
        $this->password = $builder->password;
        $this->apiKey= $builder->apiKey;
        $this->test = $builder->test;
    }

    /**
     * Builder function
     *
     * @return new Builder()
     */
    public static function builder()
    {
        return new Builder();
    }

    /**
     * BuildRequest function
     *
     * @param string  $url  Authorization code url
     * @param string  $hash sha512 encoding of the required parameters
     *                      and the clientAPI key
     * @param mixed[] $data request body data
     *
     * @return $curl
     */
    public function buildRequest($url, $hash, $data = null)
    {
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array("content-type: application/json",
            "Accept: application/json",
            "hash:$hash",
            "Authorization:Basic ".base64_encode("$this->clientId:$this->password")),
            

            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_VERBOSE => 1,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120
            )
        );
        curl_setopt($curl, CURLOPT_POST, 1);

        if ($data != null) {
            $data_string = json_encode($data);
            print_r($data_string);

            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        }

    

        return $curl;
    }

    /**
     * Create Hash  function
     *
     * @param array $data parameters for hashing
     *
     * @return $hash
     */
    public function createHash($data)
    {
        $hash ="";
        foreach ($data as $key => $value) {
            $hash .= $value;
        }
        echo $hash;
        $hash=$hash.$this->apiKey;
        $hash = hash('sha512', $hash);

        return $hash;
    }

    /**
     * Paymeny Request function
     *
     * @param array $data Payment request object
     *
     * @return JSON Object with List of Banks integrated with paga
     */
    public function paymentRequest($data)
    {
        try {
            $server = ($this->test) ? $this->test_server : $this->live_server;
            $url = $server."paymentRequest";
            $data['currency'] ??= null;
            extract($data);
            $payer['email'] ??= null;
            $payer['phoneNumber'] ??= null;
            $payer['bankId'] ??= null;
            $payee['accountNumber'] ??= null;
            $payee['phoneNumber'] ??= null;
            $payee['bankId'] ??= null;
            $payee['bankAccountNumber'] ??= null;
            $payee['financialIdentificationNumber'] ??= null;
            

            extract($payee, EXTR_PREFIX_ALL, "payee");
            extract($payer, EXTR_PREFIX_ALL, "payer");

          

            $payee_details = [
                "bankAccountNumber"=>$payee_bankAccountNumber,
                "bankId" => $payee_bankId,
                "name" => $payee_name,
                "phoneNumber" => $payee_phoneNumber,
                "accountNumber" => $payee_accountNumber,
                
            ];
        

            $payer_details = [
                'email' => $payer_email,
                'name' => $payer_name,
                'bankId' => $payer_bankId,
                'phoneNumber' => $payer_phoneNumber

            ];
        
            

            $request_data = [
                'referenceNumber'=>$referenceNumber,
                'amount' => $amount,
                'currency' => $currency,
                'payee' => array_filter($payee_details) ,
                'payer' => array_filter($payer_details),
                'payerCollectionFeeShare' => $payerCollectionFeeShare,
                'recipientCollectionFeeShare' => $recipientCollectionFeeShare,
                'paymentMethods' => $paymentMethods
            ];
                
            $hash_params= [
                $referenceNumber, $amount, $currency, $payer_phoneNumber,
                $payer_email, $payee_accountNumber, $payee_phoneNumber,
                $payee_bankId, $payee_bankAccountNumber
            ];
            
      
            $hash = $this->createHash(array_filter($hash_params));
            $curl = $this->buildRequest($url, $hash, array_filter($request_data));
            $response = curl_exec($curl);
            $this->checkCURL($curl, json_decode($response, true));
            return $response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


      /**
       * Register Persistent Payment Account function
       *
       * @param array $data Register Persistent Payment request
       * 
       * @return JSON Object with List of Banks integrated with paga
       * 
       * 
       */
    public function registerPersistentPaymentAccount($data)
    {
        try {
              $server = ($this->test) ? $this->test_server : $this->live_server;
              $url = $server."registerPersistentPaymentAccount";
              $data['creditBankId'] ??= null;
              $data['creditBankAccountNumber'] ??= null;
              $data['callbackUrl'] ??= null;
              extract($data);
              var_dump($data);
              
              $request_data = [
                  'referenceNumber'=>$referenceNumber,
                  'phoneNumber'=>$phoneNumber,
                  'firstName'=>$firstName, 
                  'lastName'=>$lastName,
                  'accountName'=>$accountName, 
                  'financialIdentificationNumber'=>$financialIdentificationNumber,
                  'accountReference'=>$accountReference,
                  'creditBankId' =>$creditBankId,
                  'creditBankAccountNumber' => $creditBankAccountNumber,
                  'callbackUrl' => $callbackUrl
              ];
  
              $hash_params= array(
                  $referenceNumber,
                  $accountReference,
                  $financialIdentificationNumber,
                  $creditBankId,
                  $creditBankAccountNumber,
                  $callbackUrl
                          
              );
  
              $hash = $this->createHash(array_filter($hash_params));
              $curl = $this->buildRequest($url, $hash, array_filter($request_data));
              $response = curl_exec($curl);
              $this->checkCURL($curl, json_decode($response, true));
              return $response;
        } catch (Exception $e) {
              return $e->getMessage();
        }
    }


    /**
     * Payment Request History function
     *
     * @param array $data Get Bank request
     *
     * @return JSON Object with List of Banks integrated with paga
     */
    public function paymentHistory($data)
    {
        try {
            $server = ($this->test) ? $this->test_server : $this->live_server;
            $url = $server."history";
            $data['endDateTimeUTC'] ??= null;
            extract($data);
            $request_data = [
                'referenceNumber' => $referenceNumber,
                'startDateTimeUTC' => $startDateTimeUTC,
                'endDateTimeUTC' => $endDateTimeUTC
            ];
            $hash_params = ['referenceNumber' => $referenceNumber];
            $hash = $this->createHash($hash_params);
            $curl = $this->buildRequest($url, $hash, array_filter($request_data));
            $response = curl_exec($curl);
            $this->checkCURL($curl, json_decode($response, true));
            return $response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    

    /**
     * Get Banks function
     *
     * @param array $data Get Bank request
     *
     * @return JSON Object with List of Banks integrated with paga
     */
    public function getBanks($data)
    {
        try {
            $server = ($this->test) ? $this->test_server : $this->live_server;
            $url = $server."banks";
            extract($data);
            $request_data = ['referenceNumber' => $referenceNumber];
            $hash = $this->createHash($request_data);
            $curl = $this->buildRequest($url, $hash, $request_data);
  
            //   print_r($url);
            $response = curl_exec($curl);
            $this->checkCURL($curl, json_decode($response, true));
            return $response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    
    /**
     * Payment Status function
     *
     * @param array $data A unique reference number provided by
     *                    the clientto uniquely identify the transaction
     *
     * @return JSON Object with List of Banks integrated with paga
     */
    public function paymentStatus($data)
    {
        try {
            $server = ($this->test) ? $this->test_server : $this->live_server;
            $url = $server."status";
            extract($data);
            $request_data = ['referenceNumber' => $referenceNumber];
            $hash = $this->createHash($request_data);
            $curl = $this->buildRequest($url, $hash, $request_data);
            $response = curl_exec($curl);
            $this->checkCURL($curl, json_decode($response, true));
            return $response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Check  CURL
     *
     * @param object $curl     CURL
     * @param object $response API response
     *
     * @return void
     */
    public function checkCURL($curl, $response)
    {
        $logger = new Logger('stderr');
        
        $logger->pushHandler(new StreamHandler('php://stderr'));
        if (curl_errno($curl)) {
            $logger->error('response: '.curl_error($response));
        }

        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($httpcode == 200) {
            $logger->info('response:', [$response]);
        }

        return curl_close($curl);
    }
}

/**
 * Builder Class
 *
 * @category  PHP
 * @package   PagaMerchant
 * @author    PagaDevComm <devcomm@paga.com>
 * @copyright 2020 Pagatech Financials
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      https://packagist.org/packages/paga/paga-merchant
 */
class Builder
{

    /**
     * __construct
     */
    public function __construct()
    {
    }

    /**
     * Set ClientId function
     *
     * @param string $clientId Merchant Principal
     *
     * @return void
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * Set Password function
     *
     * @param string $password Merchant secretKey
     *
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }


    /**
     * Set APIKey function
     *
     * @param string $apiKey Merchant HMAC
     *
     * @return void
     */
    public function setAPIKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }


    /**
     * Set Test function
     *
     * @param string $test test to set testing or live(true for test,false for live)
     *
     * @return void
     */
    public function setTest($test)
    {
        $this->test = $test;
        return $this;
    }

    /**
     * Build function
     *
     * @return void
     */
    public function build()
    {
        return new PagaCollectClient($this);
    }
}
