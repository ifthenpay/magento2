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


class IfthenpayCCardConfigProvider extends IfthenpayMasterConfigProvider implements ConfigProviderInterface
{
    const CODE = Gateway::CCARD;

    public function getConfig()
    {
        return [
            'payment' => [
                Gateway::CCARD => [
                    'logoUrl' => $this->assetRepository->getUrlWithParams('Ifthenpay_Payment::svg/ccard.svg', []),
                    'showPaymentIcon' => isset($this->dataFactory->setType(Gateway::CCARD)->build()->getConfig()['showPaymentIcon']) ? $this->dataFactory->setType(Gateway::CCARD)->build()->getConfig()['showPaymentIcon'] : null
                ],
            ]
        ];
    }
}
