<?php

namespace Omnipay\BarclaysEpdq\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * BarclaysEpdq Purchase Response
 */
class EssentialRefundResponse extends AbstractResponse implements RedirectResponseInterface 
{
    public function isSuccessful()
    {
        $refundResponses = [
            8, 81, 84
        ];

        return in_array($this->data['STATUS'], $refundResponses);
    }

    public function isRedirect()
    {
        return true;
    }

    public function getRedirectUrl()
    {
        return $this->getRequest()->getEndpoint();
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {
        return $this->getData();
    }
}
