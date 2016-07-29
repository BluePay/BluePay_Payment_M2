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
 * @package     BluePay_Payment
 * @copyright   Copyright (c) 2016 BluePay Processing, LLC (http://www.bluepay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
namespace BluePay\Payment\Model;

class Payment extends \Magento\Payment\Model\Method\Cc
{
    const CGI_URL = 'https://secure.bluepay.com/interfaces/bp10emu';
    const STQ_URL = 'https://secure.bluepay.com/interfaces/stq';
    const CURRENT_VERSION = '1.0.0.0';

    const CODE = 'bluepay_payment';

    const REQUEST_METHOD_CC     = 'CREDIT';
    const REQUEST_METHOD_ECHECK = 'ACH';

    const REQUEST_TYPE_AUTH_CAPTURE = 'SALE';
    const REQUEST_TYPE_AUTH_ONLY    = 'AUTH';
    const REQUEST_TYPE_CAPTURE_ONLY = 'CAPTURE';
    const REQUEST_TYPE_CREDIT       = 'REFUND';
    const REQUEST_TYPE_VOID         = 'VOID';
    const REQUEST_TYPE_PRIOR_AUTH_CAPTURE = 'PRIOR_AUTH_CAPTURE';

    const ECHECK_ACCT_TYPE_CHECKING = 'CHECKING';
    const ECHECK_ACCT_TYPE_BUSINESS = 'BUSINESSCHECKING';
    const ECHECK_ACCT_TYPE_SAVINGS  = 'SAVINGS';

    const ECHECK_TRANS_TYPE_CCD = 'CCD';
    const ECHECK_TRANS_TYPE_PPD = 'PPD';
    const ECHECK_TRANS_TYPE_TEL = 'TEL';
    const ECHECK_TRANS_TYPE_WEB = 'WEB';

    const RESPONSE_DELIM_CHAR = ',';

    const RESPONSE_CODE_APPROVED = 'APPROVED';
    const RESPONSE_CODE_DECLINED = 'DECLINED';
    const RESPONSE_CODE_ERROR    = 'ERROR';
    const RESPONSE_CODE_MISSING  = 'MISSING';
    const RESPONSE_CODE_HELD     = 4;

<<<<<<< HEAD
    private $responseHeaders;
    private $tempVar;

    public $_code  = 'bluepay_payment';
    public static $_dupe = true;
    public static $_underscoreCache = [];

    private $_countryFactory;

    private $_minAmount = null;
    private $_maxAmount = null;
    public $_supportedCurrencyCodes = ['USD'];
=======
    protected $responseHeaders;
    protected $tempVar;

    protected $_code  = 'bluepay_payment';
    //protected $_formBlockType = 'creditcard/form';
    protected static $_dupe = true;
    protected static $_underscoreCache = [];

    protected $_stripeApi = false;

    protected $_countryFactory;

    protected $_minAmount = null;
    protected $_maxAmount = null;
    protected $_supportedCurrencyCodes = ['USD'];
>>>>>>> origin/master

    /**
     * Availability options
     */
<<<<<<< HEAD
    public $_isGateway               = true;
    public $_canAuthorize            = true;
    public $_canCapture              = true;
    public $_canCapturePartial       = true;
    public $_canRefund               = true;
    public $_canRefundInvoicePartial = true;
    public $_canVoid                 = true;
    public $_canUseInternal          = true;
    public $_canUseCheckout          = true;
    public $_canUseForMultishipping  = true;
    public $_canSaveCc               = false;

    public $_allowCurrencyCode = ['USD'];
=======
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc       = false;

    protected $_allowCurrencyCode = ['USD'];
>>>>>>> origin/master

    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
<<<<<<< HEAD
    public $_debugReplacePrivateDataKeys = ['ach_account'];

    private $customerRegistry;
=======
    protected $_debugReplacePrivateDataKeys = ['ach_account'];

    protected $customerRegistry;
>>>>>>> origin/master

    /**
     * @var \Magento\Authorizenet\Helper\Data
     */
<<<<<<< HEAD
    private $dataHelper;
=======
    protected $dataHelper;
>>>>>>> origin/master

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
<<<<<<< HEAD
    private $checkoutCartHelper;

    private $request;
=======
    protected $checkoutCartHelper;
>>>>>>> origin/master

    /**
     * Request factory
     *
<<<<<<< HEAD
     * @var \BluePay\Payment\Model\RequestFactory
     */
    private $requestFactory;
=======
     * @var \Magento\Authorizenet\Model\RequestFactory
     */
    protected $requestFactory;
>>>>>>> origin/master

    /**
     * Response factory
     *
<<<<<<< HEAD
     * @var \BluePay\Payment\Model\ResponseFactory
     */
    private $responseFactory;
=======
     * @var \Magento\Authorizenet\Model\ResponseFactory
     */
    protected $responseFactory;
>>>>>>> origin/master

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Checkout\Helper\Cart $checkoutCartHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Session\Generic $generic,
<<<<<<< HEAD
        \Magento\Framework\App\Request\Http $request,
=======
>>>>>>> origin/master
        \BluePay\Payment\Model\Request\Factory $requestFactory,
        \BluePay\Payment\Model\Response\Factory $responseFactory,
        \Magento\Framework\HTTP\ZendClientFactory $zendClientFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->customerRegistry = $customerRegistry;
        $this->checkoutCartHelper = $checkoutCartHelper;
        $this->checkoutSession = $checkoutSession;
        $this->generic = $generic;
<<<<<<< HEAD
        $this->request = $request;
=======
>>>>>>> origin/master
        $this->requestFactory = $requestFactory;
        $this->responseFactory = $responseFactory;
        $this->zendClientFactory = $zendClientFactory;

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $moduleList,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );

<<<<<<< HEAD
=======


>>>>>>> origin/master
        $this->_minAmount = $this->getConfigData('min_order_total');
        $this->_maxAmount = $this->getConfigData('max_order_total');
    }

/**
     * Determine method availability based on quote amount and config data
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        if ($quote && (
            $quote->getBaseGrandTotal() < $this->_minAmount
            || ($this->_maxAmount && $quote->getBaseGrandTotal() > $this->_maxAmount))
        ) {
            return false;
        }
        if (!$this->getConfigData('account_id')) {
            return false;
        }

        return parent::isAvailable($quote);
    }

    /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return boolean
     */
    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->getAcceptedCurrencyCodes())) {
            return false;
        }
        return true;
    }

    /**
     * Return array of currency codes supplied by Payment Gateway
     *
     * @return array
     */
    public function getAcceptedCurrencyCodes()
    {
        if (!$this->hasData('_accepted_currency')) {
            $acceptedCurrencyCodes = $this->_allowCurrencyCode;
            $acceptedCurrencyCodes[] = $this->getConfigData('currency');
            $this->setData('_accepted_currency', $acceptedCurrencyCodes);
        }
        return $this->_getData('_accepted_currency');
    }

    /**
     * Send authorize request to gateway
    */
    
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        if ($amount <= 0) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid amount for authorization.'));
        }
        $payment->setTransactionType(self::REQUEST_TYPE_AUTH_ONLY);
        $payment->setAmount($amount);
        $request= $this->_buildRequest($payment);
        $result = $this->_postRequest($request);
        $payment->setCcApproval($result->getAuthCode())
            ->setLastTransId($result->getRrno())
            ->setTransactionId($result->getRrno())
            ->setIsTransactionClosed(0)
            ->setCcTransId($result->getRrno())
            ->setCcAvsStatus($result->getAvs())
            ->setCcCidStatus($result->getCvv2());
        if ($payment->getCcType() == '') {
$payment->setCcType($result->getCardType());
        }
        if ($payment->getCcLast4() == '') {
$payment->setCcLast4(substr($result->getCcNumber(), -4));
        }
        switch ($result->getResult()) {
            case self::RESPONSE_CODE_APPROVED:
                if ($result->getMessage() != 'DUPLICATE') {
                    $payment->setStatus(self::STATUS_APPROVED);
                } else {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Error: ' . $result->getMessage()));
                }
                return $this;
            case self::RESPONSE_CODE_DECLINED:
                throw new \Magento\Framework\Exception\LocalizedException(__('The transaction has been declined'));
            case self::RESPONSE_CODE_ERROR:
                throw new \Magento\Framework\Exception\LocalizedException(__('Error: ' . $result->getMessage()));
            case self::RESPONSE_CODE_MISSING:
                throw new \Magento\Framework\Exception\LocalizedException(__('Error: ' . $result->getMessage()));
            default:
<<<<<<< HEAD
                throw new \Magento\Framework\Exception\LocalizedException(__(
                    'An error has occured with your payment.'
                ));
=======
                throw new \Magento\Framework\Exception\LocalizedException(__('An error has occured with your payment.'));
>>>>>>> origin/master
        }
    }

    /**
     * Send capture request to gateway
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
    $payment->setAmount($amount);
<<<<<<< HEAD
=======
    //$result =$this->_checkDuplicate($payment);
>>>>>>> origin/master
        if ($payment->getCcTransId()) {
            $payment->setTransactionType(self::REQUEST_TYPE_CAPTURE_ONLY);
        } else {
            $payment->setTransactionType(self::REQUEST_TYPE_AUTH_CAPTURE);
        }
    $payment->setRrno($payment->getCcTransId());
        $request = $this->_buildRequest($payment);
        $result = $this->_postRequest($request);
        if ($result->getResult() == self::RESPONSE_CODE_APPROVED) {
            $payment->setStatus(self::STATUS_APPROVED);
            if ($payment->getCcType() == '') {
<<<<<<< HEAD
                $payment->setCcType($result->getCardType());
            }
            if ($payment->getCcLast4() == '') {
                $payment->setCcLast4(substr($result->getCcNumber(), -4));
            }
=======
$payment->setCcType($result->getCardType());
            }
            if ($payment->getCcLast4() == '') {
$payment->setCcLast4(substr($result->getCcNumber(), -4));
            }
            ////$payment->setCcTransId($result->getTransactionId());
>>>>>>> origin/master
            $payment->setLastTransId($result->getRrno());
            if (!$payment->getParentTransactionId() || $result->getRrno() != $payment->getParentTransactionId()) {
                $payment->setTransactionId($result->getRrno());
            }
            return $this;
        }
    switch ($result->getResult()) {
        case self::RESPONSE_CODE_DECLINED:
            throw new \Magento\Framework\Exception\LocalizedException(__('The transaction has been declined.'));
<<<<<<< HEAD
        case self::RESPONSE_CODE_ERROR:
=======
        case self::RESPONSE_CODE_ERROR || self::RESPONSE_CODE_MISSING:
>>>>>>> origin/master
            if ($result->getMessage() == 'Already%20Captured') {
                $payment->setTransactionType(self::REQUEST_TYPE_AUTH_CAPTURE);
                $request=$this->_buildRequest($payment);
                $result =$this->_postRequest($request);
<<<<<<< HEAD
                        if ($result->getResult() == self::RESPONSE_CODE_APPROVED &&
                            $result->getMessage() != 'DUPLICATE') {
                                $payment->setStatus(self::STATUS_APPROVED);
                                $payment->setLastTransId($result->getRrno());
                                if (!$payment->getParentTransactionId() ||
                                    $result->getRrno() != $payment->getParentTransactionId()) {
=======
                        if ($result->getResult() == self::RESPONSE_CODE_APPROVED && $result->getMessage() != 'DUPLICATE') {
                                $payment->setStatus(self::STATUS_APPROVED);
                                $payment->setLastTransId($result->getRrno());
                                if (!$payment->getParentTransactionId() || $result->getRrno() != $payment->getParentTransactionId()) {
>>>>>>> origin/master
                                    $payment->setTransactionId($result->getRrno());
                                }
                                return $this;
                        } else {
<<<<<<< HEAD
                        throw new \Magento\Framework\Exception\LocalizedException(__(
                            'Error: ' . $result->getMessage()
                        ));
=======
                        throw new \Magento\Framework\Exception\LocalizedException(Mage::helper('paygate')->__('Error: ' . $result->getMessage()));
>>>>>>> origin/master
                        }
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__('Error: ' . $result->getMessage()));
            }
<<<<<<< HEAD
        case self::RESPONSE_CODE_MISSING:
            throw new \Magento\Framework\Exception\LocalizedException(__('Error: ' . $result->getMessage()));
=======
>>>>>>> origin/master
        default:
            throw new \Magento\Framework\Exception\LocalizedException(__('An error has occured with your payment.'));
    }
        throw new \Magento\Framework\Exception\LocalizedException(__('Error in capturing the payment.'));
    }
    
<<<<<<< HEAD
=======

>>>>>>> origin/master
    /**
     * Void the payment through gateway
     */
    public function void(\Magento\Payment\Model\InfoInterface $payment)
    {
        if ($payment->getParentTransactionId()) {
            $order = $payment->getOrder();
            $payment->setTransactionType(self::REQUEST_TYPE_CREDIT);
            $payment->setAmount($amount);
            $payment->setRrno($payment->getParentTransactionId());
            $request = $this->_buildRequest($payment);
            $result = $this->_postRequest($request);
            if ($result->getResult()==self::RESPONSE_CODE_APPROVED) {
                 $payment->setStatus(self::STATUS_APPROVED);
                 $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true)->save();
                 return $this;
            }
            $payment->setStatus(self::STATUS_ERROR);
            throw new \Magento\Framework\Exception\LocalizedException(__($result->getMessage()));
        }
        $payment->setStatus(self::STATUS_ERROR);
        throw new \Magento\Framework\Exception\LocalizedException(__('Invalid transaction ID.'));
    }

    /**
     * refund the amount with transaction id
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        if ($payment->getRefundTransactionId() && $amount > 0) {
            $payment->setTransactionType(self::REQUEST_TYPE_CREDIT);
            $payment->setRrno($payment->getRefundTransactionId());
            $payment->setAmount($amount);
            $request = $this->_buildRequest($payment);
            $request->setRrno($payment->getRefundTransactionId());
            $result = $this->_postRequest($request);
            if ($result->getResult()==self::RESPONSE_CODE_APPROVED) {
                $payment->setStatus(self::STATUS_SUCCESS);
                return $this;
            }
            if ($result->getResult()==self::RESPONSE_CODE_DECLINED) {
                throw new \Magento\Framework\Exception\LocalizedException($this->_wrapGatewayError('DECLINED'));
            }
            if ($result->getResult()==self::RESPONSE_CODE_ERROR) {
                throw new \Magento\Framework\Exception\LocalizedException($this->_wrapGatewayError('ERROR'));
            }
            throw new \Magento\Framework\Exception\LocalizedException($this->_wrapGatewayError($result->getRrno()));
        }
        throw new \Magento\Framework\Exception\LocalizedException(__('Error in refunding the payment.'));
    }

    /**
     * Prepare request to gateway
     */
<<<<<<< HEAD
    public function _buildRequest(\Magento\Payment\Model\InfoInterface $payment)
=======
    protected function _buildRequest(\Magento\Payment\Model\InfoInterface $payment)
>>>>>>> origin/master
    {
        $order = $payment->getOrder();
        $this->setStore($order->getStoreId());
        $request = $this->requestFactory->create();
        if (!$payment->getPaymentType() || $payment->getPaymentType() == 'CC') {
            $payment->setPaymentType(self::REQUEST_METHOD_CC);
        } else {
            $payment->setPaymentType(self::REQUEST_METHOD_ECHECK);
        }
        $request = $this->requestFactory->create();
        if ($order && $order->getIncrementId()) {
            $request->setInvoiceId($order->getIncrementId());
        }
        $request->setMode(($this->getConfigData('trans_mode') == 'TEST') ? 'TEST' : 'LIVE');
<<<<<<< HEAD
        $request->setTpsHashType('SHA512');
    if ($payment->getToken() != '' && !$payment->getRrno()) {
        $request->setRrno($payment->getToken());
=======

    if ($payment->getToken() != '' && !$payment->getRrno()) {
        $request->setRrno($payment->getToken());
        //$payment->setRrno($payment->getAdditionalData());
>>>>>>> origin/master
        $payment->setRrno($payment->getToken());
    }

        $request->setMerchant($this->getConfigData('account_id'))
            ->setTransactionType($payment->getTransactionType())
            ->setPaymentType($payment->getPaymentType())
            ->setResponseversion('3')
            ->setTamperProofSeal($this->calcTPS($payment));
        if ($payment->getAmount()) {
            $request->setAmount($payment->getAmount(), 2);
        }
        if ($payment->getCcTransId()) {
                $request->setRrno($payment->getCcTransId());
        }
        switch ($payment->getTransactionType()) {
            case self::REQUEST_TYPE_CREDIT:
            case self::REQUEST_TYPE_VOID:
            case self::REQUEST_TYPE_CAPTURE_ONLY:
                $request->setRrno($payment->getCcTransId());
                break;
        }
        $cart = $this->checkoutCartHelper->getCart()->getItemsCount();
        $cartSummary = $this->checkoutCartHelper->getCart()->getSummaryQty();
        $this->generic;
        $session = $this->checkoutSession;

        $comment = "";

        foreach ($session->getQuote()->getAllItems() as $item) {
            $comment .= $item->getQty() . ' ';
            $comment .= '[' . $item->getSku() . ']' . ' ';
            $comment .= $item->getName() . ' ';
            $comment .= $item->getDescription() . ' ';
            $comment .= $item->getAmount() . ' ';
        }

        if (!empty($order)) {
            $billing = $order->getBillingAddress();
            if (!empty($billing)) {
                $request->setCompanyName($billing->getCompany())
                    ->setCity($billing->getCity())
                    ->setState($billing->getRegion())
                    ->setZipcode($billing->getPostcode())
                    ->setCountry($billing->getCountry())
                    ->setPhone($billing->getTelephone())
                    ->setFax($billing->getFax())
                    ->setCustomId($billing->getCustomerId())
                    ->setComment($comment)
                    ->setEmail($order->getCustomerEmail());
                $request["name1"] = $billing->getFirstname();
                $request["name2"] = $billing->getLastname();
                $request["addr1"] = $billing->getStreetLine(1);
                $request["addr2"] = $billing->getStreetLine(2);
            }
        }
        $info = $this->getInfoInstance();
        switch ($payment->getPaymentType()) {
            case self::REQUEST_METHOD_CC:
                if ($payment->getCcNumber()) {
            $temp = $payment->getCcExpYear();
                $CcExpYear = str_split($temp, 2);
                    $request->setCcNum($payment->getCcNumber())
<<<<<<< HEAD
=======
                        //->setCcExpires(sprintf('%02d%02d', $payment->getCcExpMonth(), $CcExpYear[1]))
>>>>>>> origin/master
                        ->setCcExpires(sprintf('%02d%02d', $payment->getCcExpMonth(), $payment->getCcExpYear()));
                    $request['CVCCVV2'] = $payment->getCcCid();
                }
                break;

            case self::REQUEST_METHOD_ECHECK:
                $request->setAchRouting($info->getEcheckRoutingNumber())
                    ->setAchAccount($info->getEcheckAcctNumber())
                    ->setAchAccountType($info->getEcheckAcctType())
                    ->setDocType('WEB');
                break;
        }
        return $request;
    }

<<<<<<< HEAD
    public function _postRequest(\Magento\Framework\DataObject $request)
    {
        $result = $this->responseFactory->create();
        $postArray = $this->getRequest();
        $postResult = ($this->getRequest() !== null) ? $this->getRequest()->getPost("Result") : null;

    if (isset($postArray) && ($this->getRequest()->getPost("?Result")) !== null) {
        $this->getRequest()->setPost("Result", $this->getRequest()->getPost("?Result"));
        $this->getRequest()->setPost("?Result", null);
    }
    if (!isset($postArray) || ($postResult === null)) {
=======
    protected function _postRequest(\Magento\Framework\DataObject $request)
    {
        $result = $this->responseFactory->create();
    if (isset($_POST["?Result"])) {
        $_POST["Result"] = $_POST["?Result"];
        unset($_POST["?Result"]);
    }
    if (!isset($_POST["Result"])) {
>>>>>>> origin/master
            $client = $this->zendClientFactory->create();
            $uri = self::CGI_URL;
            $client->setUri($uri ? $uri : self::CGI_URL);
            $client->setConfig([
                'maxredirects'=>0,
                'timeout'=>15,
        'useragent'=>'BluePay Magento 2 Payment Plugin/' . self::CURRENT_VERSION,
            ]);
            $client->setParameterPost($request->getData());
<<<<<<< HEAD
            $client->setMethod(\Zend_Http_Client::POST);
            try {
                    $response = $client->request();
            } catch (\Exception $e) {
                    $debugData['result'] = $result->getData();
                    $this->_debug($debugData);
                    throw new \Magento\Framework\Exception\LocalizedException(
                        $this->_wrapGatewayError($e->getMessage())
                    );
            }
        $r = substr(
            $response->getHeader('location'),
            strpos($response->getHeader('location'), "?") + 1
        );
=======
            //$comma_separated = implode(",", $request->getData());
            $client->setMethod(\Zend_Http_Client::POST);
            try {
                    $response = $client->request();
            } catch (Exception $e) {
                    $debugData['result'] = $result->getData();
                    $this->_debug($debugData);
                    throw new \Magento\Framework\Exception\LocalizedException($this->_wrapGatewayError($e->getMessage()));
            }
        $r = substr($response->getHeader('location'), strpos($response->getHeader('location'), "?") + 1);
>>>>>>> origin/master
            if ($r) {
                    parse_str($r, $responseFromBP);
                    isset($responseFromBP["Result"]) ? $result->setResult($responseFromBP["Result"]) :
                        $result->setResult('');
                    isset($responseFromBP["INVOICE_ID"]) ? $result->setInvoiceId($responseFromBP["INVOICE_ID"]) :
                        $result->setInvoiceId('');
                    isset($responseFromBP["BANK_NAME"]) ? $result->setBankName($responseFromBP["BANK_NAME"]) :
                        $result->setBankName('');
                    isset($responseFromBP["MESSAGE"]) ? $result->setMessage($responseFromBP["MESSAGE"]) :
                        $result->setMessage('');
                    isset($responseFromBP["AUTH_CODE"]) ? $result->setAuthCode($responseFromBP["AUTH_CODE"]) :
                        $result->setAuthCode('');
                    isset($responseFromBP["AVS"]) ? $result->setAvs($responseFromBP["AVS"]) :
                        $result->setAvs('');
                    isset($responseFromBP["RRNO"]) ? $result->setRrno($responseFromBP["RRNO"]) :
                        $result->setRrno('');
                    isset($responseFromBP["AMOUNT"]) ? $result->setAmount($responseFromBP["AMOUNT"]) :
                        $result->setAmount('');
                    isset($responseFromBP["PAYMENT_TYPE"]) ? $result->setPaymentType($responseFromBP["PAYMENT_TYPE"]) :
                        $result->setPaymentType('');
                    isset($responseFromBP["ORDER_ID"]) ? $result->setOrderId($responseFromBP["ORDER_ID"]) :
                        $result->setOrderId('');
                    isset($responseFromBP["CVV2"]) ? $result->setCvv2($responseFromBP["CVV2"]) :
                        $result->setCvv2('');
<<<<<<< HEAD
                    isset($responseFromBP["PAYMENT_ACCOUNT"]) ?
                        $result->setPaymentAccountMask($responseFromBP["PAYMENT_ACCOUNT"]) :
=======
                    isset($responseFromBP["PAYMENT_ACCOUNT"]) ? $result->setPaymentAccountMask($responseFromBP["PAYMENT_ACCOUNT"]) :
>>>>>>> origin/master
                        $result->setPaymentAccountMask('');
                    isset($responseFromBP["CC_EXPIRES"]) ? $result->setCcExpires($responseFromBP["CC_EXPIRES"]) :
                        $result->setCcExpires('');
                    isset($responseFromBP["CARD_TYPE"]) ? $result->setCardType($responseFromBP["CARD_TYPE"]) :
                        $result->setCardType('');
            $this->assignBluePayToken($result->getRrno());
            } else {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Error in payment gateway.'));
            }

            if ($this->getConfigData('debug')) {
                $requestDebug = clone $request;
                foreach ($this->_debugReplacePrivateDataKeys as $key) {
                    if ($requestDebug->hasData($key)) {
                        $requestDebug->setData($key, '***');
                    }
                }
                $debugData = ['request' => $requestDebug];
                $debugData['result'] = $result->getData();
                $this->_debug($debugData);
            }
    } else {
<<<<<<< HEAD
        $result->setResult($this->getRequest()->getPost("Result"));
        $result->setMessage($this->getRequest()->getPost("MESSAGE"));
        $result->setRrno($this->getRequest()->getPost("RRNO"));
        $result->setCcNumber($this->getRequest()->getPost("PAYMENT_ACCOUNT"));
        $result->setCcExpMonth($this->getRequest()->getPost("CC_EXPIRES_MONTH"));
        $result->setCcExpYear($this->getRequest()->getPost("CC_EXPIRES_YEAR"));
        $result->setPaymentType($$this->getRequest()->getPost("PAYMENT_TYPE"));
        $result->setCardType($this->getRequest()->getPost("CARD_TYPE"));
        $result->setAuthCode($this->getRequest()->getPost("AUTH_CODE"));
        $result->setAvs($this->getRequest()->getPost("AVS"));
        $result->setCvv2($this->getRequest()->getPost("CVV2"));
=======
        $result->setResult($_POST["Result"]);
        $result->setMessage($_POST["MESSAGE"]);
        $result->setRrno($_POST["RRNO"]);
        $result->setCcNumber($_POST["PAYMENT_ACCOUNT"]);
        $result->setCcExpMonth($_POST["CC_EXPIRES_MONTH"]);
        $result->setCcExpYear($_POST["CC_EXPIRES_YEAR"]);
        $result->setPaymentType($_POST["PAYMENT_TYPE"]);
        $result->setCardType($_POST["CARD_TYPE"]);
        $result->setAuthCode($_POST["AUTH_CODE"]);
        $result->setAvs($_POST["AVS"]);
        $result->setCvv2($_POST["CVV2"]);
>>>>>>> origin/master
        $this->assignBluePayToken($result->getRrno());
    }
        if ($result->getResult() == 'APPROVED') {
            $this->saveCustomerPaymentInfo($result);
        }
        return $result;
    }

<<<<<<< HEAD
    public function _checkDuplicate(\Magento\Payment\Model\InfoInterface $payment)
    {
        if ($this->getConfigData('duplicate_check') == '0') {
            return;
        }
        $order = $payment->getOrder();
        $billing = $order->getBillingAddress();
        $reportStart = date("Y-m-d H:i:s", time() - (3600 * 5) - $this->getConfigData('duplicate_check'));
        $reportEnd = date("Y-m-d H:i:s", time() - (3600 * 5));
        $hashstr = $this->getConfigData('secret_key') . $this->getConfigData('account_id') .
        $reportStart . $reportEnd;
        $request = $this->requestFactory->create();
        $request->setData("MODE", $this->getConfigData('trans_mode') == 'TEST' ? 'TEST' : 'LIVE');
        $request->setData("TAMPER_PROOF_SEAL", bin2hex(hash('sha512', $hashstr)));
        $request->setData("ACCOUNT_ID", $this->getConfigData('account_id'));
        $request->setData("REPORT_START_DATE", $reportStart);
        $request->setData("REPORT_END_DATE", $reportEnd);
        $request->setData("EXCLUDE_ERRORS", 1);
        $request->setData("ISNULL_f_void", 1);
        $request->setData("name1", $billing['firstname']);
        $request->setData("name2", $billing['lastname']);
        $request->setData("amount", $payment->getAmount());
        $request->setData("status", '1');
        $request->setData("IGNORE_NULL_STR", '0');
        $request->setData("trans_type", "SALE");
        $client = $this->zendClientFactory->create();
=======
    protected function _checkDuplicate(\Magento\Payment\Model\InfoInterface $payment)
    {
    if ($this->getConfigData('duplicate_check') == '0') {
        return;
    }
    $order = $payment->getOrder();
    $billing = $order->getBillingAddress();
    $reportStart = date("Y-m-d H:i:s", time() - (3600 * 5) - $this->getConfigData('duplicate_check'));
    $reportEnd = date("Y-m-d H:i:s", time() - (3600 * 5));
    $hashstr = $this->getConfigData('secret_key') . $this->getConfigData('account_id') .
    $reportStart . $reportEnd;
    $request = $this->requestFactory->create();
        $request->setData("MODE", $this->getConfigData('trans_mode') == 'TEST' ? 'TEST' : 'LIVE');
        $request->setData("TAMPER_PROOF_SEAL", bin2hex(md5($hashstr, true)));
    $request->setData("ACCOUNT_ID", $this->getConfigData('account_id'));
    $request->setData("REPORT_START_DATE", $reportStart);
    $request->setData("REPORT_END_DATE", $reportEnd);
    $request->setData("EXCLUDE_ERRORS", 1);
    $request->setData("ISNULL_f_void", 1);
    $request->setData("name1", $billing['firstname']);
    $request->setData("name2", $billing['lastname']);
    $request->setData("amount", $payment->getAmount());
    $request->setData("status", '1');
    $request->setData("IGNORE_NULL_STR", '0');
    $request->setData("trans_type", "SALE");
    $client = $this->zendClientFactory->create();
>>>>>>> origin/master

        $client->setUri($uri ? $uri : self::STQ_URL);
        $client->setConfig([
            'maxredirects'=>0,
            'timeout'=>30,
        ]);
        $client->setParameterPost($request->getData());
        $client->setMethod(\Zend_Http_Client::POST);
        try {
            $response = $client->request();
<<<<<<< HEAD
        } catch (\Exception $e) {
            $this->_debug($debugData);
            throw new \Magento\Framework\Exception\LocalizedException($this->_wrapGatewayError($e->getMessage()));
        }
        $p = parse_str($client->request()->getBody());
        if ($id) {
            $conn = $this->resourceConnection->getConnection('core_read');
            $result = $conn->fetchAll("SELECT * FROM sales_payment_transaction WHERE txn_id='$id'");
        if ($result) {
            return;
=======
        } catch (Exception $e) {
            $this->_debug($debugData);
            throw new \Magento\Framework\Exception\LocalizedException($this->_wrapGatewayError($e->getMessage()));
        }
    $p = parse_str($client->request()->getBody());
        if ($id) {
        $conn = $this->resourceConnection->getConnection('core_read');
        $result = $conn->fetchAll("SELECT * FROM sales_payment_transaction WHERE txn_id='$id'");
        if ($result) {
        return;
>>>>>>> origin/master
        }
        self::$_dupe = true;
        $payment->setTransactionType(self::REQUEST_TYPE_CREDIT);
        $payment->setCcTransId($id);
        $payment->setRrno($id);
        $request = $this->_buildRequest($payment);
        $result = $this->_postRequest($request);
        $payment->setCcTransId('');
        }
    }
    
<<<<<<< HEAD
    /**
     * Gateway response wrapper
     */
    public function _wrapGatewayError($text)
    {
        return __('Gateway error: %s', $text);
    }
    
    final public function calcTPS(\Magento\Payment\Model\InfoInterface $payment)
=======

    /**
     * Gateway response wrapper
     */
    protected function _wrapGatewayError($text)
    {
        return Mage::helper('paygate')->__('Gateway error: %s', $text);
    }
    
    final protected function calcTPS(\Magento\Payment\Model\InfoInterface $payment)
>>>>>>> origin/master
    {
    
        $order = $payment->getOrder();
        $billing = $order->getBillingAddress();

        $hashstr = $this->getConfigData('secret_key') . $this->getConfigData('account_id') .
        $payment->getTransactionType() . $payment->getAmount() . $payment->getRrno() .
        $this->getConfigData('trans_mode');
<<<<<<< HEAD
        return hash('sha512', $hashstr);
    }
 
    public function parseHeader($header, $nameVal, $pos)
=======
        return bin2hex(md5($hashstr, true));
    }
 
    protected function parseHeader($header, $nameVal, $pos)
>>>>>>> origin/master
    {
        $nameVal = ($nameVal == 'name') ? '0' : '1';
        $s = explode("?", $header);
        $t = explode("&", $s[1]);
        $value = explode("=", $t[$pos]);
        return $value[$nameVal];
    }
    
    public function validate()
    {
        $info = $this->getInfoInstance();
        if ($info->getToken() == '' && $info->getPaymentType() == 'ACH') {
            if ($info->getEcheckAcctNumber() == '') {
                throw new \Magento\Framework\Exception\LocalizedException(__("Invalid account number."));
            }
            if ($info->getEcheckRoutingNumber() == '' || strlen($info->getEcheckRoutingNumber()) != 9) {
<<<<<<< HEAD
                throw new \Magento\Framework\Exception\LocalizedException(__(
                    "Invalid routing number."
                ));
=======
                throw new \Magento\Framework\Exception\LocalizedException(__("Invalid routing number."));
>>>>>>> origin/master
            }
            return $this;
        }
        $errorMsg = false;
        $availableTypes = explode(',', $this->getConfigData('cctypes'));

        $ccNumber = $info->getCcNumber();
        // remove credit card number delimiters such as "-" and space
        $ccNumber = preg_replace('/[\-\s]+/', '', $ccNumber);
        $info->setCcNumber($ccNumber);
        if ($info->getPaymentType() == 'CC' && $info->getToken() == '' && $ccNumber == '') {
<<<<<<< HEAD
            throw new \Magento\Framework\Exception\LocalizedException(__(
                "Invalid credit card number."
            ));
        }
        if ($info->getPaymentType() == 'CC' &&  $ccNumber != '' &&
            ($info->getCcExpMonth() == '' || $info->getCcExpYear() == '')) {
            throw new \Magento\Framework\Exception\LocalizedException(__("Invalid card expiration date."));
        } elseif ($info->getPaymentType() == 'CC' &&  $this->getConfigData('useccv') == '1' &&
            ($info->getCcCid() == '' || strlen($info->getCcCid()) < 3
=======
            throw new \Magento\Framework\Exception\LocalizedException(__("Invalid credit card number."));
        }
        if ($info->getPaymentType() == 'CC' &&  $ccNumber != '' && ($info->getCcExpMonth() == '' || $info->getCcExpYear() == '')) {
            throw new \Magento\Framework\Exception\LocalizedException(__("Invalid card expiration date."));
        } elseif ($info->getPaymentType() == 'CC' &&  $this->getConfigData('useccv') == '1' && ($info->getCcCid() == '' || strlen($info->getCcCid()) < 3
>>>>>>> origin/master
            || strlen($info->getCcCid()) > 4)) {
            throw new \Magento\Framework\Exception\LocalizedException(__("Invalid Card Verification Number."));
        }

        $ccType = '';
    
    if (in_array($info->getCcType(), $availableTypes)) {
            if ($this->validateCcNum($ccNumber)
                // Other credit card type number validation
                || ($this->OtherCcType($info->getCcType()) && $this->validateCcNumOther($ccNumber))) {
                $ccType = 'OT';
                $ccTypeRegExpList = [
                    // Solo only
                    'SO' => '/(^(6334)[5-9](\d{11}$|\d{13,14}$))|(^(6767)(\d{12}$|\d{14,15}$))/',
                    'SM' => '/(^(5[0678])\d{11,18}$)|(^(6[^05])\d{11,18}$)|(^(601)[^1]\d{9,16}$)|(^(6011)\d{9,11}$)'
                            . '|(^(6011)\d{13,16}$)|(^(65)\d{11,13}$)|(^(65)\d{15,18}$)'
                            . '|(^(49030)[2-9](\d{10}$|\d{12,13}$))|(^(49033)[5-9](\d{10}$|\d{12,13}$))'
                            . '|(^(49110)[1-2](\d{10}$|\d{12,13}$))|(^(49117)[4-9](\d{10}$|\d{12,13}$))'
                            . '|(^(49118)[0-2](\d{10}$|\d{12,13}$))|(^(4936)(\d{12}$|\d{14,15}$))/',
                    // Visa
                    'VI'  => '/^4[0-9]{12}([0-9]{3})?$/',
                    // Master Card
                    'MC'  => '/^5[1-5][0-9]{14}$/',
                    // American Express
                    'AE'  => '/^3[47][0-9]{13}$/',
                    // Discovery
                    'DI'  => '/^6011[0-9]{12}$/',
                    // JCB
                    'JCB' => '/^(3[0-9]{15}|(2131|1800)[0-9]{11})$/'
                ];

                foreach ($ccTypeRegExpList as $ccTypeMatch => $ccTypeRegExp) {
                    if (preg_match($ccTypeRegExp, $ccNumber)) {
                        $ccType = $ccTypeMatch;
                        break;
                    }
                }

        if (!$this->OtherCcType($info->getCcType()) && $ccType!=$info->getCcType()) {
                    $errorMsg = __('Credit card number mismatch with credit card type.');
        }
            } else {
                $errorMsg = __('Invalid Credit Card Number');
            }
    } else {
            $errorMsg = __('Credit card type is not allowed for this payment method.');
    }

        //validate credit card verification number
        if ($errorMsg === false && $this->hasVerification()) {
            $verifcationRegEx = $this->getVerificationRegEx();
            $regExp = isset($verifcationRegEx[$info->getCcType()]) ? $verifcationRegEx[$info->getCcType()] : '';
            if (!$info->getCcCid() || !$regExp || !preg_match($regExp, $info->getCcCid())) {
                $errorMsg = __('Please enter a valid credit card verification number.');
            }
        }

        if ($ccType != 'SS' && !$this->_validateExpDate($info->getCcExpYear(), $info->getCcExpMonth())) {
            $errorMsg = __('Incorrect credit card expiration date.');
        }

        if ($errorMsg) {
        if ($this->getConfigData('use_iframe') == '1') {
        $errorMsg = '';
        }
        }

        //This must be after all validation conditions
        if ($this->getIsCentinelValidationEnabled()) {
            $this->getCentinelValidator()->validate($this->getCentinelValidationData());
        }

        return $this;
    }

    public function assignData(\Magento\Framework\DataObject $data)
    {
        if (is_array($data)) {
            $this->getInfoInstance()->addData($data);
        } elseif ($data instanceof \Magento\Framework\DataObject) {
            $this->getInfoInstance()->addData($data->getData());
        }
        $info = $this->getInfoInstance();
        $info->setCcType($data->getCcType())
            ->setCcOwner($data->getCcOwner())
            ->setCcLast4(substr($data->getCcNumber(), -4))
            ->setCcNumber($data->getCcNumber())
            ->setCcCid($data->getCcCid())
            ->setCcExpMonth($data->getCcExpMonth())
            ->setCcExpYear($data->getCcExpYear())
            ->setCcSsIssue($data->getCcSsIssue())
            ->setCcSsStartMonth($data->getCcSsStartMonth())
            ->setCcSsStartYear($data->getCcSsStartYear())
            ->setToken($data->getToken())
            ->setAdditionalData($data->getBpToken());
        return $this;
    }

    public function assignBluePayToken($token)
    {
    $info = $this->getInfoInstance();
    $info->setAdditionalData($token);
    }

    public function prepareSave()
    {
        $info = $this->getInfoInstance();
        if ($this->_canSaveCc) {
            $info->setCcNumberEnc($info->encrypt('xxxx-'.$info->getCcLast4()));
        }
        if ($info->getAdditionalData()) {
            $info->setAdditionalData($info->getAdditionalData());
        }
        $info->setCcNumber(null)
            ->setCcCid(null);
        return $this;
    }
    
    public function hasVerificationBackend()
    {
        $configData = $this->getConfigData('useccv_backend');
<<<<<<< HEAD
        if ($configData === null) {
=======
        if (is_null($configData)) {
>>>>>>> origin/master
            return true;
        }
        return (bool) $configData;
    }

    public function saveCustomerPaymentInfo($result)
    {
        $info = $this->getInfoInstance();
        if ($info->getSavePaymentInfo() != '1') {
<<<<<<< HEAD
            return;
=======
return;
>>>>>>> origin/master
        }

        $customerId = $this->checkoutSession->getQuote()->getCustomerId();
        $customer = $this->customerRegistry->retrieve($customerId);
        $customerData = $customer->getDataModel();
<<<<<<< HEAD
        $paymentAcctString = $customerData->getCustomAttribute('bluepay_stored_accts') ?
            $customerData->getCustomAttribute('bluepay_stored_accts')->getValue() : '';
=======
        $paymentAcctString = $customerData->getCustomAttribute('bluepay_stored_accts') ? $customerData->getCustomAttribute('bluepay_stored_accts')->getValue() : '';
>>>>>>> origin/master
        $oldToken = $info->getToken();
        $newToken = $result->getRrno();
        $newCardType = $result->getCardType();
        $newPaymentAccount = $result->getPaymentAccountMask();
        $newCcExpMonth = substr($result->getCcExpires(), 0, 2);
        $newCcExpYear = substr($result->getCcExpires(), 2, 2);

        // This is a brand new payment account
        if ($info->getToken() == '') {
            $paymentAcctString = $info->getPaymentType() == 'ACH' ?
                $paymentAcctString . $newPaymentAccount . ' - eCheck,' . $newToken . '|' :
<<<<<<< HEAD
                $paymentAcctString . $newPaymentAccount . ' - ' .$newCardType .
                ' [' . $newCcExpMonth . '/' . $newCcExpYear .
                '],' . $newToken . '|';
=======
                $paymentAcctString . $newPaymentAccount . ' - ' .$newCardType . ' [' . $newCcExpMonth . '/' . $newCcExpYear .
            '],' . $newToken . '|';
>>>>>>> origin/master
        // update an existing payment account
        } else {
            $paymentAccts = explode('|', $paymentAcctString);
            foreach ($paymentAccts as $paymentAcct) {
                if (strlen($paymentAcct) < 2) {
                    continue;
                }
                $paymentAccount = explode(',', $paymentAcct);
                if (strpos($paymentAcct, $oldToken) !== false) {
                    $oldPaymentString = $paymentAccount[0];
                    $oldPaymentAccount = explode('-', $oldPaymentString)[0];
                    // gather new ACH info to update payment info in db
                    if ($info->getPaymentType() == 'ACH') {
<<<<<<< HEAD
                        $newPaymentString = str_replace(
                            trim($oldPaymentAccount),
                            $newPaymentAccount,
                            $oldPaymentString
                        );
=======
                        $newPaymentString = str_replace(trim($oldPaymentAccount), $newPaymentAccount, $oldPaymentString);
>>>>>>> origin/master
                    // gather new CC info to update payment info in db
                    } else {
                        $oldExpMonth = substr(explode('[', ($oldPaymentString))[1], 0, 2);
                        $oldExpYear = substr(explode('[', ($oldPaymentString))[1], 3, 2);
                        $oldCardType = explode('[', (explode('-', $oldPaymentString)[1]))[0];
                        $newPaymentString = str_replace($oldExpMonth, $newCcExpMonth, $oldPaymentString);
                        $newPaymentString = str_replace($oldExpYear, $newCcExpYear, $newPaymentString);
<<<<<<< HEAD
                        $newPaymentString = str_replace(
                            trim($oldPaymentAccount),
                            $newPaymentAccount,
                            $newPaymentString
                        );
=======
                        $newPaymentString = str_replace(trim($oldPaymentAccount), $newPaymentAccount, $newPaymentString);
>>>>>>> origin/master
                        $newPaymentString = str_replace(trim($oldCardType), $newCardType, $newPaymentString);
                    }
                    $paymentAcctString = str_replace($oldPaymentString, $newPaymentString, $paymentAcctString);
                    $paymentAcctString = str_replace($oldToken, $newToken, $paymentAcctString);
                }
            }
        }
        $customerData->setCustomAttribute('bluepay_stored_accts', $paymentAcctString);
        $customer->updateData($customerData);
        $customer->save();
<<<<<<< HEAD
=======
        return;
>>>>>>> origin/master
    }
}
