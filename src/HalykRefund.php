<?php

namespace Studioone\Halyk;

class HalykRefund
{

    use Validation;

    /**
     * gateway endpoint
     * @var string
     */
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
        'transactionId',
    ];

    public function __construct(
        $successUrl,
        $failUrl,
        $certPath,
        $certPass,
        $amount,
        $transactionId
    )
    {
        $this->successUrl = $successUrl;
        $this->failUrl = $failUrl;
        $this->certPath = $certPath;
        $this->certPass = $certPass;
        $this->amount = $amount ?? '';
        $this->transactionId = $transactionId;
    }

//    /**
//     * @return array
//     */
    private function getRequestData() {

        $this->validateFields();

        $fields = [
            'command' => 'k',
            'trans_id' => $this->transactionId,
            'amount' => $this->amount,
        ];

        return $fields;
    }

    /**
     * send request for refund
     */
    public function send()
    {

        $requestData = $this->getRequestData();
        $postFields = http_build_query($requestData);

        $url  = $this->endpoint;
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

        $execData = explode(PHP_EOL, trim($exec));
        $result = [];
        foreach ($execData as $key => $value) {
            $execValue = explode(':', $value);
            if (!empty($execValue[1])) {
                $result[$execValue[0]] = trim($execValue[1]);
            }
        }

        return $result;
    }
}
