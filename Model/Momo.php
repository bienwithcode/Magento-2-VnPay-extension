<?php

namespace MG\VnPay\Model;

/**
 * Pay In Store payment method model
 */
class Momo extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'momo';
}