<?php

namespace Omnipay\BarclaysEpdq\Message;

use Omnipay\Common\Message\AbstractRequest;

class EssentialRefundRequest extends AbstractRequest {
    protected $liveEndpoint = 'https://payments.epdq.co.uk/ncol/prod/maintenancedirect.asp';
    protected $testEndpoint = 'https://mdepayments.epdq.co.uk/ncol/test/maintenancedirect.asp';

    public function getClientId() {
        return $this->getParameter('clientId');
    }

    public function setClientId($value) {
        return $this->setParameter('clientId', substr($value, 0, 30));
    }

    public function getLanguage()
    {
        return $this->getParameter('language');
    }

    public function setLanguage($value)
    {
        return $this->setParameter('language', $value);
    }

    public function getData() {
        $data = [];

        $data['PSPID'] = $this->getClientId();
        $data['ORDERID'] = $this->getPaymentId();
        $data['AMOUNT'] = $this->getAmountInteger();

        $data['OPERATION'] = "RFD";
        $data['PSWD'] = $this->getRefundPassword();
        $data['USERID'] = $this->getRefundUser();

        $data['SHASIGN'] = $this->calculateSha($data, $this->getShaIn());

        return $data;
    }

    public function getRefundPassword() {
        return "10ngLongBARC!";
    }

    public function getRefundUser() {
        return "refund";
    }

    public function sendData($data) {
        $response = $this->sendRequest(
            $this->getEndpoint(),
            '',
            $this->getData()
        );

        $data = (array) $response->xml();

        if (!empty($data['@attributes'])) {
            $data = $data['@attributes'];
        } else {
            $data = [];
        }

        return $this->response = new EssentialRefundResponse($this, $data);
    }

    public function getEndpoint() {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    public function calculateSha($data, $shaKey)
    {
        uksort($data, "strnatcmp");

        $shaString = '';
        foreach ($data as $key => $value) {
            $shaString .= sprintf('%s=%s%s', strtoupper($key), $value, $shaKey);
        }

        return strtoupper(sha1($shaString));
    }

    public function getShaIn()
    {
        return $this->getParameter('shaIn');
    }

    public function getOperation()
    {
        return $this->getParameter('OPERATION');
    }

    public function setOperation($value)
    {
        return $this->setParameter('OPERATION', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('PSWD');
    }

    public function setPassword($value)
    {
        return $this->setParameter('PSWD', $value);
    }

    public function getUserid()
    {
        return $this->getParameter('USERID');
    }

    public function setUserid($value)
    {
        return $this->setParameter('USERID', $value);
    }

    public function getPaymentid()
    {
        return $this->getParameter('ORDERID');
    }

    public function setPaymentid($value)
    {
        return $this->setParameter('ORDERID', $value);
    }

	protected function sendRequest(string $method, string $url, $data)
    {
        $headers = [];

        $req = $this->httpClient->post(
            $method,
            $url,
            $data,
            array_merge(
                ['Content-Type' => 'application/json'],
                $headers
            )
        );

        return $req->send();
    }

}
