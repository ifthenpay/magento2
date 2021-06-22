<?php
/**
* Ifthenpay_Payment module dependency
*
* @category    Gateway Payment
* @package     Ifthenpay_Payment
* @author      Ifthenpay
* @copyright   Ifthenpay (http://www.ifthenpay.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

namespace Ifthenpay\Payment\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;

class AuthorizationRequest implements BuilderInterface
{

    private $dataFactory;

    public function __construct(
        DataFactory $dataFactory
    )
    {
        $this->dataFactory = $dataFactory;
    }

    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        $payment = $buildSubject['payment'];
        $order = $payment->getOrder();

        return [
            'data_request' => [
                'order' => $order,
                'payment' => $payment->getPayment()
            ]
        ];
    }
}