<?php
namespace BluePay\Payment\Controller\Customer;

class Request extends \Magento\Framework\App\Action\Action
{

    const CURRENT_VERSION = '1.2.1';
    /** @var  \Magento\Framework\View\Result\Page */
    private $resultPageFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfiguration;

    private $customerSession;

    private $request;

    private $customerRegistry;

    /** @var \Magento\Framework\Controller\Result\JsonFactory */
    private $jsonResultFactory;

    /**      * @param \Magento\Framework\App\Action\Context $context      */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\HTTP\ZendClientFactory $zendClientFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfiguration,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->customerRegistry = $customerRegistry;
        $this->request = $request;
        $this->resultPageFactory = $resultPageFactory;
        $this->zendClientFactory = $zendClientFactory;
        $this->scopeConfiguration = $scopeConfiguration;
        $this->jsonResultFactory = $jsonResultFactory;
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        if ($this->getRequest()->getPost('delete') == '1') {
            $this->deletePaymentInfo();
            $result->setData(
                [
                    'result' => __('3'),
                    'message' => __('Payment account successfully deleted')
                ]
            );
        } else if ($this->getRequest()->getPost('trans_result') == 'APPROVED') {
            $this->saveCustomerPaymentInfo();
            $result->setData(
                [
                    'result' => __($this->getRequest()->getPost('trans_result')),
                    'message' => __($this->getRequest()->getPost('message'))
                ]
            );
        } else {
            $result->setData(
                [
                    'result' => __($this->getRequest()->getPost('trans_result')),
                    'message' => __($this->getRequest()->getPost('message'))
                ]
            );
        }
        $result->setHttpResponseCode(\Magento\Framework\Webapi\Response::HTTP_OK);
        return $result;
    }

    final public function calcTPS($token)
    {
        $hashstr = $this->scopeConfiguration->getValue(
            'payment/bluepay_payment/secret_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) .
            $this->scopeConfiguration->getValue(
                'payment/bluepay_payment/account_id',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ) .
        'AUTH' . '0.00' . $token . $this->scopeConfiguration->getValue(
            'payment/bluepay_payment/trans_mode',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return hash('sha512', $hashstr);
    }

    public function saveCustomerPaymentInfo()
    {
        $customer = $this->customerRegistry->retrieve($this->customerSession->getId());
        if(!$customer){
            error_log('An error occured while saving payment info: customer not logged in.');
            return;
        }
        $customerData = $this->customerSession->getCustomerData();
        $paymentAcctString = $customerData->getCustomAttribute('bluepay_stored_accts') ?
        $customerData->getCustomAttribute('bluepay_stored_accts')->getValue() : '';
        $oldToken = $this->getRequest()->getPost('master_id');
        $newToken = $this->getRequest()->getPost('rrno');
        $newCardType = $this->getRequest()->getPost('cc_type');
        $newCcExpMonth = $this->getRequest()->getPost('cc_expire_mm');
        $newCcExpYear = $this->getRequest()->getPost('cc_expire_yy');
        if ($this->getRequest()->getPost('payment_type') != 'ACH' && (!$newCcExpMonth || !$newCcExpYear)){
            error_log('An error occured while saving payment info: missing expiration date.');
            return;
        }
        $newPaymentAccount = $this->getRequest()->getPost('payment_account_mask');
        // This is a brand new payment account
        if ($oldToken == '') {
            $paymentAcctString = $this->getRequest()->getPost('payment_type') == 'ACH' ?
                $paymentAcctString . $newPaymentAccount . ' - eCheck,' . $newToken . '|' :
                $paymentAcctString . $newPaymentAccount . ' - ' .$newCardType . ' [' .
                $newCcExpMonth . '/' . $newCcExpYear .
            '],' . $newToken . '|';
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
                    if (preg_match("/eCheck/i", $oldPaymentString)) {
                        $newPaymentString = str_replace(
                            trim($oldPaymentAccount),
                            $newPaymentAccount,
                            $oldPaymentString
                        );
                    // gather new CC info to update payment info in db
                    } else {
                        $oldExpMonth = substr(explode('[', ($oldPaymentString))[1], 0, 2);
                        $oldExpYear = substr(explode('[', ($oldPaymentString))[1], 3, 2);
                        $oldCardType = explode('[', (explode('-', $oldPaymentString)[1]))[0];
                        $newPaymentString = str_replace($oldExpMonth, $newCcExpMonth, $oldPaymentString);
                        $newPaymentString = str_replace($oldExpYear, $newCcExpYear, $newPaymentString);
                        $newPaymentString = str_replace(
                            trim($oldPaymentAccount),
                            $newPaymentAccount,
                            $newPaymentString
                        );
                        $newPaymentString = str_replace(trim($oldCardType), $newCardType, $newPaymentString);
                    }
                    $paymentAcctString = str_replace($oldPaymentString, $newPaymentString, $paymentAcctString);
                    $paymentAcctString = str_replace($oldToken, $newToken, $paymentAcctString);
                    break;
                }
            }
        }
        $customerData->setCustomAttribute('bluepay_stored_accts', $paymentAcctString);
        $customer->updateData($customerData);
        $customer->save();
    }

    public function deletePaymentInfo()
    {
        $customer = $this->customerRegistry->retrieve($this->customerSession->getId());
        $customerData = $this->customerSession->getCustomerData();
        $paymentAcctString = $customerData->getCustomAttribute('bluepay_stored_accts') ?
        $customerData->getCustomAttribute('bluepay_stored_accts')->getValue() : '';
        $oldToken = $this->getRequest()->getPost('master_id');
        // update an existing payment account
        $paymentAccts = explode('|', $paymentAcctString);
        foreach ($paymentAccts as $paymentAcct) {
            if (strlen($paymentAcct) < 2) {
                continue;
            }
            $paymentAccount = explode(',', $paymentAcct);
            // Delete old payment account from string value in db
            if (strpos($paymentAcct, $oldToken) !== false) {
                $paymentAcctString = str_replace($paymentAcct . '|', '', $paymentAcctString);
                break;
            }
        }
        $customerData->setCustomAttribute('bluepay_stored_accts', $paymentAcctString);
        $customer->updateData($customerData);
        $customer->save();
    }
}
