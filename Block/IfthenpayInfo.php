<?php

/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Block;

use Ifthenpay\Payment\Config\ConfigVars;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Block\Info;

class IfthenpayInfo extends Info
{


    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    public function getSpecificInformation()
    {
        switch ($this->getMethodCode()) {
            case ConfigVars::MULTIBANCO_CODE:

                $ref = $this->getInfo()->getAdditionalInformation('reference');
                // minor fix for users that have updated from the older version of ifthenpay_multibanco module, since that version does not store multibanco reference data (courtesy of waterstone consulting dev department)
                if (!$ref) {
                    return [];
                }
                $formatedReference = substr($ref, 0, 3) . " " . substr($ref, 3, 3) . " " . substr($ref, 6);

                $informations[__('Entity')->render()] = $this->getInfo()->getAdditionalInformation('entity');
                $informations[__('Reference')->render()] = $formatedReference;

                $deadline = $this->getInfo()->getAdditionalInformation('deadline');
                if ($deadline) {
                    $informations[__('Deadline')->render()] = $this->getInfo()->getAdditionalInformation('deadline');
                }
                break;
            case ConfigVars::PAYSHOP_CODE:
                $informations[__('Reference')->render()] = $this->getInfo()->getAdditionalInformation('reference');
                $informations[__('Deadline')->render()] = $this->getInfo()->getAdditionalInformation('deadline');
                break;
            case ConfigVars::MBWAY_CODE:
                $informations[__('Transaction ID')->render()] = $this->getInfo()->getAdditionalInformation('transactionId');
                $informations[__('Phone Number')->render()] = $this->getInfo()->getAdditionalInformation('countryCode') . ' ' . $this->getInfo()->getAdditionalInformation('phoneNumber');
                break;
            case ConfigVars::IFTHENPAYGATEWAY_CODE:
                $informations[__('Payment URL')->render()] = $this->getInfo()->getAdditionalInformation('paymentUrl');
                $informations[__('Deadline')->render()] = $this->getInfo()->getAdditionalInformation('deadline');
                break;
            default:
                break;
        }
        $informations[__('Total to Pay')->render()] = $this->getInfo()->getAdditionalInformation('orderTotal') . $this->getInfo()->getAdditionalInformation('currencySymbol');
        return $informations;
    }

    public function getMethodCode()
    {
        return $this->getInfo()->getMethodInstance()->getCode();
    }
}
