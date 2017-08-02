<?php
namespace BluePay\Payment\Controller\Customer;

use Magento\Framework\App\Action;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;

class Storedacct extends \Magento\Framework\App\Action\Action
{

    const CURRENT_VERSION = '1.5.5.0';
    /** @var  \Magento\Framework\View\Result\Page */
    private $resultPageFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfiguration;

    private $request;

    private $response;

    private $url;

    private $customerSession;

    private $customerRegistry;

    /**      * @param \Magento\Framework\App\Action\Context $context      */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\HTTP\ZendClientFactory $zendClientFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfiguration
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->customerRegistry = $customerRegistry;
        $this->request = $request;
        $this->response = $response;
        $this->url = $context->getUrl();
        $this->resultPageFactory = $resultPageFactory;
        $this->zendClientFactory = $zendClientFactory;
        $this->scopeConfiguration = $scopeConfiguration;
    }

    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            $this->customerSession->setAfterAuthUrl($this->url->getCurrentUrl());
            $this->customerSession->authenticate();
        }
        $resultPage = $this->resultPageFactory->create();
        $messageBlock = $resultPage->getLayout()->createBlock(
            'Magento\Framework\View\Element\Messages',
            'result'
        );
        $messageBlock = $resultPage->getLayout()->getBlock('result');
        if ($messageBlock) {
            $messageBlock->getMessageCollection()->clear();
        } else {
            $messageBlock = $resultPage->getLayout()->createBlock(
                'Magento\Framework\View\Element\Messages',
                'result'
            );
        }
        $requestParams = $this->getRequest()->getParams();
        if (!isset($requestParams['result']) || !isset($requestParams['message'])) {
            return $resultPage;
        } else if ($this->getRequest()->getParams()['result'] == "3") {
            $messageBlock->addSuccess('Payment account successfully deleted.');
        } elseif ($this->getRequest()->getParams()['result'] == "APPROVED") {
            $messageBlock->addSuccess('Payment account successfully saved.');
        } else {
$messageBlock->addError('An error occurred when saving the payment account. Reason: ' .
            $this->getRequest()->getParams()['message']);
        }
        $resultPage->getLayout()->setChild(
            'result_message',
            $messageBlock->getNameInLayout(),
            'result_alias'
        );
        return $resultPage;
    }
}
