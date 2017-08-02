<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    BluePay
 * @package     BluePay_CreditCard
 * @copyright   Copyright (c) 2016 BluePay Processing, LLC (http://www.bluepay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace BluePay\Payment\Block\Customer;

use Magento\Payment\Model\CcConfig;

class Form extends \Magento\Framework\View\Element\Template
{
    private $customerSession;
    private $customerRegistry;
    private $addressRegistry;
    private $storeManager;
    private $scopeConfiguration;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Customer\Model\AddressRegistry $addressRegistry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        CcConfig $ccConfig,
        array $data = []
    ) {
    parent::__construct($context, $data);
    $this->customerSession = $customerSession;
    $this->customerRegistry = $customerRegistry;
    $this->addressRegistry = $addressRegistry;
    $this->objectManager = $objectManager;
    $this->storeManager = $context->getStoreManager();
    $this->ccConfig = $ccConfig;
    $this->scopeConfiguration = $context->getScopeConfig();
    }

    public function _prepareLayout()
    {
        $this->setCcMonths($this->getCcMonths());
        $this->setCcYears($this->getCcYears());
        $this->setStoredAccounts($this->getStoredAccounts());
    }

    public function getCcMonths()
    {
        return $this->ccConfig->getCcMonths();
    }

    /**
     * Retrieve credit card expire years
     *
     * @return array
     */
    public function getCcYears()
    {
        return $this->ccConfig->getCcYears();
    }

    public function getStoredAccounts()
    {
        $paymentAcctString = $this->customerSession->getCustomerDataObject()
            ->getCustomAttribute('bluepay_stored_accts') ?
            $this->customerSession->getCustomerDataObject()
            ->getCustomAttribute('bluepay_stored_accts')->getValue() : '';
        $options = [];
        if (strpos($paymentAcctString, '|') !== false) {
            $paymentAccts = explode('|', $paymentAcctString);
            foreach ($paymentAccts as $paymentAcct) {
                if (strlen($paymentAcct) < 2) {
                    continue;
                }
                $paymentAccount = explode(',', $paymentAcct);
                $val = ['text' => __($paymentAccount[0]), 'value' => $paymentAccount[1]];
                array_push($options, $val);
            }
        }
        return $options;
    }

    public function getStoreUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    public function getConfigData($value)
    {
        return $this->scopeConfiguration->getValue('payment/bluepay_payment/' . $value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getTpsDef()
    {
        return "MERCHANT NAME1 NAME2 COMPANY_NAME MODE";
    }

    public function getTps()
    {
        $customer = $this->customerRegistry->retrieve($this->customerSession->getCustomerId());
        $customerData = $customer->getDataModel();
        $hashstr = $this->scopeConfiguration->getValue(
            'payment/bluepay_payment/secret_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ) .
            $this->scopeConfiguration->getValue(
                'payment/bluepay_payment/account_id',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ) .
            $this->getCustomerData()->getFirstName() .
            $this->getCustomerData()->getLastName() .
            $this->getCustomerData()->getCompany() .
            $this->scopeConfiguration->getValue(
            'payment/bluepay_payment/trans_mode',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return hash('sha512', $hashstr);
    }

    public function getCustomerName()
    {
        $customer = $this->customerRegistry->retrieve($this->customerSession->getCustomerId());
        return $customer->getDataModel();
    }

    public function getCustomerData()
    {
        $customer = $this->customerRegistry->retrieve($this->customerSession->getCustomerId());
        $customerData = $customer->getDataModel();
        $address = $this->addressRegistry->retrieve($this->customerSession->getCustomer()->getDefaultBilling());
        return $address->getDataModel();
    }

    public function getStoredPaymentAccts()
    {
        $customer = $this->customerRegistry->retrieve($this->customerSession->getCustomerId());
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
}
