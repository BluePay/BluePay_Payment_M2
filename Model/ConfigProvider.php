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

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Payment\Model\CcConfig;
use Magento\Framework\View\Asset\Source;
 
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfiguration;

    private $ccConfig;

    private $customerRegistry;

    private $_coreRegistry = null;

    private $_customerSession;

    private $session;

    private $storeManager;

    private $cart;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfiguration
     * @codeCoverageIgnore
     */
    public function __construct(
        CcConfig $ccConfig,
        Source $assetSource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $session,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfiguration,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->ccConfig = $ccConfig;
        $this->_customerSession = $customerSession;
        $this->session = $session;
        $this->customerRegistry = $customerRegistry;
        $this->scopeConfiguration = $scopeConfiguration;
        $this->assetSource = $assetSource;
        $this->storeManager = $storeManager;
        $this->cart = $cart;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $paymentAcctString = $this->_customerSession->getCustomerDataObject()
            ->getCustomAttribute('bluepay_stored_accts') ?
            $this->_customerSession->getCustomerDataObject()->getCustomAttribute('bluepay_stored_accts')
            ->getValue() : '';
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
        $name1 = '';
        $name2 = '';
        $company = '';
        $street = '';
        $city = '';
        $state = '';
        $zip = '';
        $email = '';
        $customerId = $this->cart->getQuote()->getCustomerId();
        if (!$customerId) {
            $name1 = $this->session->getQuote()->getBillingAddress()->getFirstName();
            $name2 = $this->session->getQuote()->getBillingAddress()->getLastName();
            $company = $this->session->getQuote()->getBillingAddress()->getCompany() != null ? $this->session->getQuote()->getBillingAddress()->getCompany() : '';
            $street = $this->session->getQuote()->getBillingAddress()->getStreet()[0];
            $city = $this->session->getQuote()->getBillingAddress()->getCity() != null ? $this->session->getQuote()->getBillingAddress()->getCity() : '';
            $state = $this->session->getQuote()->getBillingAddress()->getRegion();
            $zip = $this->session->getQuote()->getBillingAddress()->getPostCode();
            $email = $this->session->getQuote()->getBillingAddress()->getEmail();
        } else {
            $customer = $this->customerRegistry->retrieve($customerId);
            $customerData = $customer->getDataModel();
            $name1 = $customerData->getAddresses() != null ? $customerData->getAddresses()[0]->getFirstName() : '';
            $name2 = $customerData->getAddresses() != null ? $customerData->getAddresses()[0]->getLastName() : '';
            $company = $customerData->getAddresses() != null && $customerData->getAddresses()[0]->getCompany() != null ? $customerData->getAddresses()[0]->getCompany() : '';
            $street = $customerData->getAddresses() != null ? $customerData->getAddresses()[0]->getStreet()[0] : '';
            $city = $customerData->getAddresses() != null ? $customerData->getAddresses()[0]->getCity() : '';
            $state = $customerData->getAddresses() != null ? $customerData->getAddresses()[0]->getRegion()->getRegionCode() : '';
            $zip = $customerData->getAddresses() != null ? $customerData->getAddresses()[0]->getPostCode() : '';
            $email = $customerData->getEmail();
        }

        $transType = $this->scopeConfiguration->getValue(
            'payment/bluepay_payment/payment_action',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) == "authorize" ? "AUTH" : "SALE";
        
        $secretKey = $this->scopeConfiguration->getValue(
            'payment/bluepay_payment/secret_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    
        $accountID = $this->scopeConfiguration->getValue(
            'payment/bluepay_payment/account_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $transMode = $this->scopeConfiguration->getValue(
            'payment/bluepay_payment/trans_mode',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $hashstr = $secretKey . $accountID . $transMode;
        $tpsDef = "MERCHANT MODE";
        $tps = hash('sha512', $hashstr);
        $shpfTPSHashType = "SHA512";
        $useCvv2 = $this->scopeConfiguration->getValue(
            'payment/bluepay_payment/useccv',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $this->cart->getQuote()->reserveOrderId()->save();
        
        $reservedOrderId = $this->cart->getQuote()->getReservedOrderId();
        $shpfTPSDef = "SHPF_FORM_ID SHPF_ACCOUNT_ID TAMPER_PROOF_SEAL MERCHANT USE_CVV2 MODE ORDER_ID TPS_DEF SHPF_TPS_DEF";
        $shpfHashString = $secretKey . "magento2" . $accountID . $tps . $accountID . $useCvv2 . $transMode . $reservedOrderId . $tpsDef . $shpfTPSDef;
        $shpfTPS = hash('sha512', $shpfHashString);
        $returnShpfTps = hash('sha512', $secretKey . $accountID);
        $returnShpfTpsDef = "SHPF_ACCOUNT_ID";
        $i = 1;
        $level3 = [];
        // foreach ($this->cart->getQuote()->getAllItems() as $item) {
        //     $level3["LV3_ITEM".$i."_PRODUCT_CODE"] = htmlentities($item->getSku());
        //     $level3["LV3_ITEM".$i."_UNIT_COST"] = $item->getPrice();
        //     $level3["LV3_ITEM".$i."_QUANTITY"] = $item->getQty();
        //     $level3["LV3_ITEM".$i."_ITEM_DESCRIPTOR"] = htmlentities($item->getName());
        //     $level3["LV3_ITEM".$i."_MEASURE_UNITS"] = 'EA';
        //     $level3["LV3_ITEM".$i."_COMMODITY_CODE"] = '-';
        //     $level3["LV3_ITEM".$i."_TAX_AMOUNT"] = round($item->getPrice() * ($item->getTaxPercent() / 100), 2);
        //     $level3["LV3_ITEM".$i."_TAX_RATE"] = $item->getTaxPercent() . '%';
        //     $level3["LV3_ITEM".$i."_ITEM_DISCOUNT"] = '';
        //     $level3["LV3_ITEM".$i."_LINE_ITEM_TOTAL"] = $item->getPrice() * $item->getQty();
        //     $i++;
        // }

        $config = [
            'payment' => [
                'bluepay_payment' => [
                    'accountId' => $accountID,
                    'tps' => $tps,
                    'tpsDef' => $tpsDef,
                    'transType' => $transType,
                    'transMode' => $transMode,
                    'cctypes' => $this->scopeConfiguration->getValue(
                        'payment/bluepay_payment/cctypes',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    ),
                    'availableTypes' => $this->getCcAvailableTypes('bluepay_payment'),
                    'months' => $this->getCcMonths(),
                    'years' => $this->getCcYears(),
                    'hasVerification' => $this->hasVerification('bluepay_payment'),
                    'hasSsCardType' => $this->hasSsCardType('bluepay_payment'),
                    'ssStartYears' => $this->getSsStartYears(),
                    'cvvImageUrl' => $this->getCvvImageUrl(),
                    'active' => $this->scopeConfiguration->getValue(
                        'payment/bluepay_payment/active',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    ),
                    'paymentTypes' => $this->scopeConfiguration->getValue(
                        'payment/bluepay_payment/payment_type',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    ),
                    'allowAccountsStorage' => $this->scopeConfiguration->getValue(
                        'payment/bluepay_payment/tokenization',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    ),
                    'storedAccounts' => $options,
                    'isCustomerLoggedIn' => $this->_customerSession->isLoggedIn(),
                    'enableIframe' => $this->scopeConfiguration->getValue(
                        'payment/bluepay_payment/use_iframe',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    ),
                    'iframeUrl' => 'https://secure.bluepay.com/interfaces/shpf?SHPF_FORM_ID=magento2',
                    'useCvv2' => $useCvv2,
                    'level3' => $level3,
                    'reservedOrderId' => $reservedOrderId,
                    'quoteData' => $this->cart->getQuote()->getData(),
                    'customerName1' => $name1,
                    'customerName2' => $name2,
                    'customerCompany' => $company,
                    'customerStreet' => $street,
                    'customerCity' => $city,
                    'customerRegion' => $state,
                    'customerZip' => $zip,
                    'customerEmail' => $email,
                    'shpfTPSHashType' => $shpfTPSHashType,
                    'shpfTPSDef' => $shpfTPSDef,
                    'shpfTPS' => $shpfTPS,
                    'returnShpfTPS' => $returnShpfTps,
                    'returnShpfTpsDef' => $returnShpfTpsDef
                ],
                'ccform' => [
                    'icons' => $this->getIcons(),
                    'availableTypes' => ['bluepay_payment' => $this->getCcAvailableTypes('bluepay_payment')]
                ]
            ]
    ];
        return $config;
    }

    /**
     * Get instructions text from config
     *
     * @param string $code
     * @return string
     */
    public function getInstructions($code)
    {
        return nl2br($this->escaper->escapeHtml($this->methods[$code]->getInstructions()));
    }

    /**
     * Solo/switch card start years
     *
     * @return array
     */
    public function getSsStartYears()
    {
        return $this->ccConfig->getSsStartYears();
    }

    /**
     * Retrieve credit card expire months
     *
     * @return array
     */
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

    /**
     * Retrieve CVV tooltip image url
     *
     * @return string
     */
    public function getCvvImageUrl()
    {
        return $this->ccConfig->getCvvImageUrl();
    }

    /**
     * Retrieve availables credit card types
     *
     * @param string $methodCode
     * @return array
     */
    public function getCcAvailableTypes($methodCode)
    {
        $methodCode = null;
        $types = $this->ccConfig->getCcAvailableTypes();
        $availableTypes = $this->scopeConfiguration->getValue(
            'payment/bluepay_payment/cctypes',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($availableTypes) {
            $availableTypes = explode(',', $availableTypes);
            foreach (array_keys($types) as $code) {
                if (!in_array($code, $availableTypes)) {
                    unset($types[$code]);
                }
            }
        }
        return $types;
    }

    /**
     * Retrieve has verification configuration
     *
     * @param string $methodCode
     * @return bool
     */
    public function hasVerification($methodCode)
    {
        $methodCode = null;
        $result = $this->ccConfig->hasVerification();
        $configData = $this->scopeConfiguration->getValue(
            'payment/bluepay_payment/useccv',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($configData !== null) {
            $result = (bool)$configData;
        }
        return $result;
    }

    /**
     * Whether switch/solo card type available
     *
     * @param string $methodCode
     * @return bool
     */
    public function hasSsCardType($methodCode)
    {
        $methodCode = null;
        $result = false;
        $availableTypes = explode(',', $this->scopeConfiguration->getValue(
            'payment/bluepay_payment/cctypes',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ));
        $ssPresentations = array_intersect(['SS', 'SM', 'SO'], $availableTypes);
        if ($availableTypes && count($ssPresentations) > 0) {
            $result = true;
        }
        return $result;
    }

    /**
     * Get icons for available payment methods
     *
     * @return array
     */
    public function getIcons()
    {
        $icons = [];
        $types = $this->ccConfig->getCcAvailableTypes();
        foreach (array_keys($types) as $code) {
            if (!array_key_exists($code, $icons)) {
                $asset = $this->ccConfig->createAsset('Magento_Payment::images/cc/' . strtolower($code) . '.png');
                $placeholder = $this->assetSource->findRelativeSourceFilePath($asset);
                if ($placeholder) {
                    list($width, $height) = getimagesize($asset->getSourceFile());
                    $icons[$code] = [
                        'url' => $asset->getUrl(),
                        'width' => $width,
                        'height' => $height
                    ];
                }
            }
        }
        return $icons;
    }
}
