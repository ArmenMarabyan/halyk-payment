<?php namespace WeAreDe\TbcPay;

class TbcPayProcessor
{

    public $submit_url = 'https://securepay.ufc.ge:18443/ecomm2/MerchantHandler';

    private $cert_path;

    private $cert_pass;

    private $client_ip_addr;

    public $amount;

    public $currency;

    public $description;

    public $language;

    public $biller;

    public function __construct($cert_path, $cert_pass, $client_ip_addr)
    {
        $this->cert_path      = $cert_path;
        $this->cert_pass      = $cert_pass;
        $this->client_ip_addr = $client_ip_addr;
    }

    private function curl($query_string)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POSTFIELDS, $query_string);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl, CURLOPT_CAINFO, $this->cert_path); // because of Self-Signed certificate at payment server.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSLCERT, $this->cert_path);
        curl_setopt($curl, CURLOPT_SSLKEY, $this->cert_path);
        curl_setopt($curl, CURLOPT_SSLKEYPASSWD, $this->cert_pass);
        curl_setopt($curl, CURLOPT_URL, $this->submit_url);
        $result = curl_exec($curl);
        return $result;
    }

    private function build_query_string($post_fields)
    {
        return http_build_query($post_fields);
    }

    private function parse_result($string)
    {
        $array1 = explode(PHP_EOL, trim($string));
        $result = array();
        foreach ($array1 as $key => $value) {
            $array2 = explode(':', $value);
            if (!empty($array2[1])) {
                $result[ $array2[0] ] = trim($array2[1]);
            }
        }
        return $result;
    }

    private function process($post_fields)
    {
        $string = $this->build_query_string($post_fields);
        $result = $this->curl($string);
        $parsed = $this->parse_result($result);
        return $parsed;
    }

    public function sms_start_transaction()
    {
        $post_fields = array(
            'command'        => 'v', // identifies a request for transaction registration
            'amount'         => $this->amount,
            'currency'       => $this->currency,
            'client_ip_addr' => $this->client_ip_addr,
            'description'    => $this->description,
            'language'       => $this->language,
            'biller'         => $this->biller,
            'msg_type'       => 'SMS'
        );
        return $this->process($post_fields);
    }

    public function dms_start_authorization()
    {
        $post_fields = array(
            'command'        => 'a', // identifies a request for transaction registration
            'amount'         => $this->amount,
            'currency'       => $this->currency,
            'client_ip_addr' => $this->client_ip_addr,
            'description'    => $this->description,
            'language'       => $this->language,
            'biller'         => $this->biller,
            'msg_type'       => 'DMS'
        );



        return $this->process($post_fields);
    }

    public function dms_make_transaction($trans_id)
    {
        $post_fields = array(
            'command'        => 't', // identifies a request for transaction registration
            'trans_id'       => $trans_id,
            'amount'         => $this->amount,
            'currency'       => $this->currency,
            'client_ip_addr' => $this->client_ip_addr,
            'description'    => $this->description,
            'language'       => $this->language,
            'msg_type'       => 'DMS'
        );


        return $this->process($post_fields);
    }

    public function get_transaction_result($trans_id)
    {
        $post_fields = array(
            'command'        => 'c', // identifies a request for transaction registration
            'trans_id'       => $trans_id,
            'client_ip_addr' => $this->client_ip_addr
        );
        return $this->process($post_fields);
    }

    public function reverse_transaction($trans_id, $amount = '', $suspected_fraud = '')
    {
        $post_fields = array(
            'command'         => 'r', // identifies a request for transaction registration
            'trans_id'        => $trans_id,
            'amount'          => $amount,
            'suspected_fraud' => $suspected_fraud
        );
        return $this->process($post_fields);
    }

    public function refund_transaction($trans_id, $amount = '')
    {
        $post_fields = array(
            'command'         => 'k', // identifies a request for transaction registration
            'trans_id'        => $trans_id,
            'amount'          => $amount
        );
        return $this->process($post_fields);
    }

    public function credit_transaction($trans_id, $amount = '')
    {
        $post_fields = array(
            'command'         => 'g', // identifies a request for transaction registration
            'trans_id'        => $trans_id,
            'amount'          => $amount
        );
        return $this->process($post_fields);
    }

    public function close_day()
    {
        $post_fields = array(
            'command'         => 'b' // identifies a request for transaction registration
        );
        return $this->process($post_fields);
    }
    // Regular payments need to be implemented!
}
