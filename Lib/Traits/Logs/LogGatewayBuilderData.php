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

namespace Ifthenpay\Payment\Lib\Traits\Logs;

trait LogGatewayBuilderData
{
    protected function logGatewayBuilderData(): void
    {
        $this->logger->info('gateway builder data set with success', [
                'paymentMethod' => $this->paymentMethod,
                'gatewayBuilder' => $this->gatewayDataBuilder
            ]
        );
    }
}
