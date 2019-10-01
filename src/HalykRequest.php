<?php

namespace Studioone\Halyk;

class HalykRequest
{

    use Validation;

    /**
     * gateway endpoint
     * @var string
     */
//    protected $endpoint = 'https://securepay.ufc.ge:18443/ecomm2/MerchantHandler';
    protected $endpoint = "https://ecommerce.ufc.ge:18443/ecomm2/MerchantHandler";
    /**
     * OK url
     * @var string
     */
    protected $successUrl;

    /**
     * FAIL url
     * @var string
     */
    protected $failUrl;

    /**
     * path to certificate
     * @var string
     */
    protected $certPath;

    /**
     * certificate password
     * @var string
     */
    protected $certPass;

    /**
     * transaction amount in fractional units, mandatory
     * 100 = 1 unit of currency. e.g. 1 gel = 100.
     * @var numeric
     */
    protected $amount;

    /**
     * transaction currency code (ISO 4217), mandatory
     * http://en.wikipedia.org/wiki/ISO_4217
     * GEL = 981 e.g.
     * @var numeric
     */
    protected $currency;

    /**
     * client IP address, mandatory
     * @var string
     */
    protected $clientIpAddress;

    /*
     * Msg type
     * @var string
     */
    protected $msgType = 'SMS';

    /**
     * transaction details, optional (up to 125 characters)
     * @var string
     */
    protected $description;

    /**
     * authorization language identifier, optional (up to 32 characters)
     * EN, GE e.g,
     * @var string
     */
    protected $language;

    /**
     * unique trans_id
     * @var string
     */
    protected $transactionId;

    /**
     * required fields
     * @var array
     */
    protected $requiredFields = [
        'certPath',
        'certPass',
        'amount',
        'currency',
        'clientIpAddress',
    ];

    public function __construct(
        $successUrl,
        $failUrl,
        $certPath,
        $certPass,
        $clientIpAddress,
        $msgType,
        $amount,
        $currency,
        $description,
        $language,
        $transactionId
    )
    {
        $this->successUrl = $successUrl;
        $this->failUrl = $failUrl;
        $this->certPath = $certPath;
        $this->certPass = $certPass;
        $this->clientIpAddress = $clientIpAddress;
        $this->msgType = $msgType;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->description = $description;
        $this->language = $language;
        $this->transactionId = $transactionId;
    }

    /**
     * @return array
     */
    private function getRequestData(array $data) {

        if (count($data) > 0) {
            $this->requiredFields = [];
            $fields               = $data;
        } else {
            $fields = [
                'command'        => 'v',
                'amount'         => $this->amount,
                'currency'       => $this->currency,
                'client_ip_addr' => $this->clientIpAddress,
                'language'       => $this->language,
                'description'    => $this->description,
                'msg_type'       => $this->msgType,
            ];
        }
        $this->validateFields();
        return $fields;
    }

    /**
     * send request
     */
    public function send(array $data = [])
    {
        $requestData = $this->getRequestData($data);
        $postFields = http_build_query($requestData);

        $url        = $this->endpoint;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSLVERSION, 4);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_CAINFO, $this->certPath);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSLCERT, $this->certPath);
        curl_setopt($curl, CURLOPT_SSLKEY, $this->certPath);
        curl_setopt($curl, CURLOPT_SSLKEYPASSWD, $this->certPass);
        curl_setopt($curl, CURLOPT_URL, $url);
        $exec = curl_exec($curl);
        $info = curl_getinfo($curl);

        if (curl_errno($curl)) {
            $error = curl_error($curl);
            throw new \Exception($error);
        }

        curl_close($curl);

        $execData = explode(PHP_EOL, trim($exec));
        $result = [];
        foreach ($execData as $key => $value) {
            $execValue = explode(':', $value);
            if (!empty($execValue[1])) {
                $result[$execValue[0]] = trim($execValue[1]);
            }
        }

        return $result;

        //        if (curl_errno($curl)) {
//            $error = curl_error($curl);
//            throw new \Exception($error);
//        }
//
//        curl_close($curl);
//
//        $transId = null;
//        if ( ! empty($exec) && $exec !== false) {
//            $result = explode(' ', $exec);
//            if (trim($result[0], ':') !== 'error') {
//                $transId = $result[1];
//            }
//        }
//
//        return $transId;
    }

    /**
     * close day
     */
    public function closeDay()
    {

        $requestData = [
            'command' => 'b'
        ];

        $result = $this->send($requestData);

        return $result;
    }

}
