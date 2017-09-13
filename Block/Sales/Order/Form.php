<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace BluePay\Payment\Block\Sales\Order;

class Form extends \Magento\Payment\Block\Form\Cc
{
    /**
     * @var string
     */
    protected $_template = 'BluePay_Payment::form/cc.phtml';

    /**
     * Payment config model
     *
     * @var \Magento\Payment\Model\Config
     */
    protected $_paymentConfig;

    private $_scopeConfiguration;

    private $_customerRegistry;

    private $_backend;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Backend\Model\Session\Quote $backend,
        array $data = []
    ) {
        parent::__construct($context, $paymentConfig, $data);
        $this->_paymentConfig = $paymentConfig;
        $this->_scopeConfiguration = $context->getScopeConfig();
        $this->_customerRegistry = $customerRegistry;
        $this->_backend = $backend;
    }

    public function getStoredPaymentAccts()
    {
        $customer = $this->_customerRegistry->retrieve($this->_backend->getQuote()->getCustomerId());
        $customerData = $customer->getDataModel();
        $paymentAcctString = $customerData->getCustomAttribute('bluepay_stored_accts') ?
            $customerData->getCustomAttribute('bluepay_stored_accts')->getValue() : '';
        $options = [];
        if (strpos($paymentAcctString, '|') !== false) {
                $paymentAccts = explode('|', $paymentAcctString);
                foreach ($paymentAccts as $paymentAcct) {
                    if (strlen($paymentAcct) < 2) {
                        continue;
                    }
                    $paymentAccount = explode(',', $paymentAcct);
                    $val = ['label' => __($paymentAccount[0]), 'value' => $paymentAccount[1]];
                    array_push($options, $val);
                }
        }
        return $options;
    }

    public function getGrandTotal()
    {
        return $this->_backend->getQuote()->getGrandTotal();
    }

    public function getTpsDef()
    {
        return "MERCHANT COMPANY_NAME ADDR1 CITY ZIPCODE MODE";
    }

    public function getCustomerEmail()
    {
        $customer = $this->_customerRegistry->retrieve($this->_backend->getQuote()->getCustomerId());
        return $customer->getEmail();
    }

    public function getTps()
    {
        $customer = $this->_customerRegistry->retrieve($this->_backend->getQuote()->getCustomerId());
        $customerData = $customer->getDataModel();
        $hashstr = $this->_scopeConfiguration->getValue(
            'payment/bluepay_payment/secret_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ) .
            $this->_scopeConfiguration->getValue(
                'payment/bluepay_payment/account_id',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ) .
            $customerData->getAddresses()[0]->getCompany() . 
            $customerData->getAddresses()[0]->getStreet()[0] . 
            $customerData->getAddresses()[0]->getCity() .
            //$customerData->getAddresses()[0]->getRegion()->getRegionId() .
            $customerData->getAddresses()[0]->getPostCode() .
            $this->_scopeConfiguration->getValue(
            'payment/bluepay_payment/trans_mode',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return hash('sha512', $hashstr);
    }
}
