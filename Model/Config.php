<?php
namespace BluePay\Payment\Model;

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
 
class Config
{
    private static $_methods;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    private $objectManager;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->objectManager= $objectManager;
    }
    public function getActiveMethods($store = null)
    {
        $methods = [];
        $config = $this->scopeConfig->getValue(
            'payment',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        foreach ($config as $code => $methodConfig) {
            if ($this->scopeConfig->getValue(
                'payment/bluepay_payment/active',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ) == 1) {
                $methods[$code] = $this->_getMethod($code, $methodConfig);
            }
        }
        return $methods;
    }

    public function getAllMethods($store = null)
    {
        $methods = [];
        $config = $this->scopeConfig->getValue(
            'payment',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        foreach ($config as $code => $methodConfig) {
            $methods[$code] = $this->_getMethod($code, $methodConfig);
        }
        return $methods;
    }

    public function getAccountTypes()
    {
        $types = ['CHECKING' => 'Checking', 'BUSINESSCHECKING' => 'Business checking', 'SAVINGS' => 'Savings'];
        return $types;
    }
}
