<?php

namespace Studioone\Halyk;

class HalykForm
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
    private function getFormData(array $data) {

        if (count($data) > 0) {
            $this->requiredFields = [];
            $fields               = $data;
        } else {
            $fields = [
                'command' => 'v',
                'amount' => $this->amount,
                'currency' => $this->currency,
                'client_ip_addr' => $this->clientIpAddress,
                'language' => $this->language,
                'description' => $this->description,
                'msg_type' => $this->msgType,
                'returnOkUrl' => $this->successUrl,
                'returnFailUrl' => $this->failUrl,
            ];
        }
        $this->validateFields();
        return $fields;
    }

    /**
     * returns form with fields
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCustom($formMethod = 'POST', $submitText = 'Pay', $endpoint = null)
    {
        $formData = $this->getFormData();

        $url = $endpoint ?? $this->endpoint;

        $rules = [
            "merchantId" => 'required',
            "secretKey" => 'required',
            "amount" => 'required',
            "language" => 'required',
            "transactionId" => 'required',
            "returnOkUrl" => 'required|url',
            "returnFailUrl" => 'required|url',
        ];

        $validator = \Validator::make($formData, $rules, [
            'merchantId.required' => 'Please add merchantId key in halyk config file',
            'secretKey.required' => 'Please add secretKey key in halyk config file'
        ]);

        if ($validator->fails()) {
            $validationErrors = $validator->errors();
        }

        return view('halyk::halyk_form', [
            'formData' => $formData,
            'url' => $url,
            'formMethod' => $formMethod,
            'submitText' => $submitText,
            'errors' => $validationErrors ?? ''
        ]);
    }

    /**
     * returns form button with hidden inputs
     * @return string
     */
    public function send(array $data = [], $formMethod = 'POST', $submitText = 'Pay')
    {

        $formData   = $this->getFormData($data);
        $postFields = http_build_query($formData);

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

        if (curl_errno($curl)) {
            $error = curl_error($curl);
            throw new \Exception($error);
        }

        curl_close($curl);

//        $transId = null;
//        if ( ! empty($exec) && $exec !== false) {
//            $result = explode(' ', $exec);
//            if(trim($result[0], ':') !== 'error') {
//                $transId = $result[1];
//            }
//        }
//


        $execData = explode(PHP_EOL, trim($exec));
        $result = [];
        foreach ($execData as $key => $value) {
            $execValue = explode(':', $value);
            if (!empty($execValue[1])) {
                $result[$execValue[0]] = trim($execValue[1]);
            }
        }

        $transId = $result['TRANSACTION_ID'];

        if (!isset($transId)) {
            throw new \Exception('Transaction id is null');
        }

        return view('halyk::halyk_readdr_form', [
            'transactionId' => $transId,
            'formMethod'    => $formMethod,
            'submitText'    => $submitText,
        ]);
    }
}
