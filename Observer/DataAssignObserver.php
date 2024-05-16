<?php

/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Ifthenpay\Payment\Observer;

use Ifthenpay\Payment\Config\ConfigVars;
use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;


class DataAssignObserver extends AbstractDataAssignObserver
{

    public function execute(Observer $observer)
    {
        // check if payment method belongs to ifthenpay module (courtesy of waterstone consulting dev department)
        $paymentMethod = $observer->getEvent()->getData('method');
        if (!in_array($paymentMethod->getCode(), ConfigVars::PAYMENT_METHOD_CODES)) {
            return;
        }

        $data = $this->readDataArgument($observer);
        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);


        if (!empty($additionalData)) {
            $paymentInfo = $this->readPaymentModelArgument($observer);
            $paymentInfo->setAdditionalInformation($additionalData);
        }
    }
}
