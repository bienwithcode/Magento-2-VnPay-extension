<?php

namespace MG\VnPay\Controller\Payment;


use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\Controller\ResultFactory;
use MG\VnPay\Helper\Data;

class Momo extends Action
{
    protected $_helper;

    protected $_checkoutSession;

    protected $_orderFactory;

    protected $_resultFactory;

    protected $_messageManager;

    public function __construct(
        Data $data,
        Session $checkoutSession,
        OrderFactory $orderFactory,
        ResultFactory $resultFactory,
        ManagerInterface $messageManager,
        Context $context
    )
    {
        $this->_helper = $data;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_resultFactory = $resultFactory;
        $this->_messageManager = $messageManager;
        return parent::__construct($context);
    }

    public function execute()
    {
        $order       = $this->getOrder();

        $endpoint = "https://test-payment.momo.vn/gw_payment/transactionProcessor";
        $partnerCode = $this->_helper->getPartnerCode();
        $accessKey = $this->_helper->getAccessKey();
        $serectkey = $this->_helper->getSecretKey();
        $orderInfo = "pay with MoMo";
        $returnUrl = "https://momo.vn/return";
        $notifyurl = "https://dummy-url.vn/notify";
        $amount = $order->getGrandTotal();
        $orderid = $order->getRealOrderId();
        $requestId = time()."";
        $requestType = "captureMoMoWallet";
        $extraData = "merchantName=;merchantId=";//pass empty value if your merchant does not have stores else merchantName=[storeName]; merchantId=[storeId] to identify a transaction map with a physical store

        $rawHash = "partnerCode=".$partnerCode."&accessKey=".$accessKey."&requestId=".$requestId."&amount=".$amount."&orderId=".$orderid."&orderInfo=".$orderInfo."&returnUrl=".$returnUrl."&notifyUrl=".$notifyurl."&extraData=".$extraData;

        $signature = hash_hmac("sha256", $rawHash, $serectkey);
        $data =  array('partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderid,
            'orderInfo' => $orderInfo,
            'returnUrl' => $returnUrl,
            'notifyUrl' => $notifyurl,
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature);

        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult =json_decode($result,true);

        if($jsonResult['message'] === 'success') {
            $this->_redirect($jsonResult['payUrl']);
        }
        else {
            $message = "Can't process the payment.";
            foreach ($jsonResult['details'] as $val) {
                $message .= $val['description'];
            }
            $this->_messageManager->addErrorMessage($message);
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

            return $resultRedirect;
        }

    }

    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }

    protected function getOrder()
    {
        $orderId = $this->_checkoutSession->getLastRealOrderId();
        if (!isset($orderId)) {
            return null;
        }
        $order = $this->_orderFactory->create()->loadByIncrementId(
            $orderId
        );
        if (!$order->getId()) {
            return null;
        }
        return $order;
    }

}