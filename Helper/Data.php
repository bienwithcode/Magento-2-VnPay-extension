<?php

namespace MG\VnPay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{

    protected $customer;

    protected $customerSession;

    protected $storeManager;

    protected $httpcontext;

    protected $filesystem;

    protected $urlInterface;

    protected $customerurl;

    protected $buttons = null;

    public function __construct(
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Http\Context $httpcontext,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Customer\Model\Url $customerurl,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->customer = $customer;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->httpcontext = $httpcontext;
        $this->filesystem = $filesystem;
        $this->urlInterface = $context->getUrlBuilder();
        $this->customerurl = $customerurl;
        parent::__construct($context);
    }

    public function getConfig($path, $store = null, $scope = null)
    {
        if ($scope === null) {
            $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        }
        return $this->scopeConfig->getValue($path, $scope, $store);
    }

    public function IsEnable()
    {
        return (bool)$this->getConfig('mgvnpay/general/activate');
    }
    public function IsEnableMomoPayment()
    {
        return (bool)$this->getConfig('payment/momo/active');
    }

    public function getPartnerCode()
    {
        return  $this->getConfig('payment/momo/partner_code');
    }

    public function getAccessKey()
    {
        return  $this->getConfig('payment/momo/access_key');
    }

    public function getSecretKey()
    {
        return  $this->getConfig('payment/momo/secret_key');
    }

    public function getApiEndpoint()
    {
        return  $this->getConfig('payment/momo/api_endpoint');
    }

    public function isSecure()
    {
        return (bool)$this->getConfig('web/secure/use_in_frontend');
    }
}