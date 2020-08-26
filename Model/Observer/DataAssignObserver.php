<?php

namespace BluePay\Payment\Model\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Payment\Model\InfoInterface;

class DataAssignObserver extends AbstractDataAssignObserver
{
    const MODEL_CODE = 'payment_model';

    /**
     * Reads payment model argument
     *
     * @param Observer $observer
     * @return InfoInterface
     */
    protected function readPaymentModelArgument(Observer $observer)
    {
        return $this->readArgument($observer, static::MODEL_CODE, InfoInterface::class);
    }
    
    /**
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        $additionalData = new DataObject($additionalData);
        $paymentMethod = $this->readMethodArgument($observer);

        $payment = $observer->getPaymentModel();
        if (!$payment instanceof InfoInterface) {
            $payment = $paymentMethod->getInfoInstance();
        }

        if (!$payment instanceof InfoInterface) {
            throw new LocalizedException(__('Payment model does not provided.'));
        }
        $payment->setPaymentAccountMask($additionalData->getData('payment_account_mask'));
        $payment->setCcLast4(substr($additionalData->getData('cc_number'), -4));
        $payment->setCcNumber($additionalData->getData('cc_number'));
        //$payment->setCcType($additionalData->getData('card_type'));
        $payment->setCcExpMonth($additionalData->getData('cc_exp_month'));
        $payment->setCcExpYear($additionalData->getData('cc_exp_year'));
        $payment->setPaymentType($additionalData->getData('payment_type'));
        $payment->setIframe($additionalData->getData('iframe'));
        $payment->setCcNumber($additionalData->getData('cc_number'));
        $payment->setCcCid($additionalData->getData('cc_cid'));
        $payment->setEcheckRoutingNumber($additionalData->getData('echeck_routing_number'));
        $payment->setEcheckAcctNumber($additionalData->getData('echeck_account_number'));
        $payment->setEcheckAcctType($additionalData->getData('echeck_acct_type'));
        $payment->setToken($additionalData->getData('token'));
        $payment->setTransID($additionalData->getData('trans_id'));
        $payment->setSavePaymentInfo($additionalData->getData('save_payment_info'));
        $payment->setTransactionType($additionalData->getData('trans_type'));
        if ($additionalData->getData('card_type') == "ACH") {
            $payment->setCardType("OT");
        } else if ($additionalData->getData('card_type') != "") {
            $payment->setCardType($additionalData->getData('card_type'));
        } else if ($additionalData->getData('cc_type') != "") {
            $payment->setCardType($additionalData->getData('cc_type'));
        }

        if ($additionalData->getData('iframe') == "1") {
            $payment->setResult($additionalData->getData('result'));
            $payment->setMessage($additionalData->getData('message'));
            $payment->setRrno($additionalData->getData('token'));
            //$payment->setAuthCode($object->getAuthCode());
            //$payment->setAvs($object->getAvs());
            //$payment->setCvv2($object->getCvv2());
        }
        return $payment;
    }
}

