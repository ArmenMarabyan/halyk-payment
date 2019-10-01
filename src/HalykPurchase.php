<?php

namespace Studioone\Halyk;

class HalykPurchase
{

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
     * @param string  $certPath
     * @param string  $certPass
     * @return self
     */
    public static function get($certPath, $certPass)
    {
        return new self($certPath, $certPass);
    }

    public function __construct($certPath, $certPass)
    {
        $this->certPath = $certPath;
        $this->certPass = $certPass;
    }

    /**
     * @param string $amount
     * @return HalykPurchase
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @param string $language
     * @return HalykPurchase
     */
    public function setLocale($language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @param string $description
     * @return HalykPurchase
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param string $msgType
     * @return HalykPurchase
     */
    public function setMessageType($msgType)
    {
        $this->msgType = $msgType;
        return $this;
    }

    /**
     * @param string $clientIpAddress
     * @return HalykPurchase
     */
    public function setClientIpAddress($clientIpAddress)
    {
        $this->clientIpAddress = $clientIpAddress;
        return $this;
    }

    /**
     * @param string $transactionId
     * @return HalykPurchase
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    /**
     * @param string $failUrl
     * @return HalykPurchase
     */
    public function setFailUrl($failUrl)
    {
        $this->failUrl = $failUrl;
        return $this;
    }

    /**
     * @param string $currency
     * @return HalykPurchase
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @param string $successUrl
     * @return HalykPurchase
     */
    public function setSuccessUrl($successUrl)
    {
        $this->successUrl = $successUrl;
        return $this;
    }

    /**
     * @return HalykForm
     */
    public function getForm() {
        return new HalykForm(
            $this->successUrl,
            $this->failUrl,
            $this->certPath,
            $this->certPass,
            $this->clientIpAddress,
            $this->msgType,
            $this->amount,
            $this->currency,
            $this->description,
            $this->language,
            $this->transactionId
        );
    }

    /**
     * @return HalykRequest
     */
    public function getRequest() {
        return new HalykRequest(
            $this->successUrl,
            $this->failUrl,
            $this->certPath,
            $this->certPass,
            $this->clientIpAddress,
            $this->msgType,
            $this->amount,
            $this->currency,
            $this->description,
            $this->language,
            $this->transactionId
        );
    }

    /**
     * @return HalykRefund
     */
    public function getRefund() {
        return new HalykRefund(
            $this->successUrl,
            $this->failUrl,
            $this->certPath,
            $this->certPass,
            $this->amount,
            $this->transactionId
        );
    }
}
