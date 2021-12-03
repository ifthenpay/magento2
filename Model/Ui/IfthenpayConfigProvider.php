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

declare(strict_types=1);

namespace Ifthenpay\Payment\Model\Ui;

use Ifthenpay\Payment\Lib\Payments\Gateway;
use Magento\Checkout\Model\ConfigProviderInterface;
use Ifthenpay\Payment\Model\Ui\IfthenpayMasterConfigProvider;

class IfthenpayConfigProvider extends IfthenpayMasterConfigProvider implements ConfigProviderInterface
{
    public function getConfig(): array
    {
        return [
            'payment' => [
                Gateway::MULTIBANCO => [
                    'logoUrl' => $this->assetRepository->getUrlWithParams('Ifthenpay_Payment::svg/multibanco.svg', []),
                    'showPaymentIcon' => isset($this->dataFactory->setType(Gateway::MULTIBANCO)->build()->getConfig()['showPaymentIcon']) ? $this->dataFactory->setType(Gateway::MULTIBANCO)->build()->getConfig()['showPaymentIcon'] : null
                ],
                Gateway::MBWAY => [
                    'logoUrl' => $this->assetRepository->getUrlWithParams('Ifthenpay_Payment::svg/mbway.svg', []),
                    'showPaymentIcon' => isset($this->dataFactory->setType(Gateway::MBWAY)->build()->getConfig()['showPaymentIcon']) ? $this->dataFactory->setType(Gateway::MBWAY)->build()->getConfig()['showPaymentIcon'] : null
                ],
                Gateway::PAYSHOP => [
                    'logoUrl' => $this->assetRepository->getUrlWithParams('Ifthenpay_Payment::svg/payshop.svg', []),
                    'showPaymentIcon' => isset($this->dataFactory->setType(Gateway::PAYSHOP)->build()->getConfig()['showPaymentIcon']) ? $this->dataFactory->setType(Gateway::PAYSHOP)->build()->getConfig()['showPaymentIcon'] : null
                ],
            ]
        ];
    }
}
