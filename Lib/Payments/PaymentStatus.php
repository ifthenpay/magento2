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



namespace Ifthenpay\Payment\Lib\Payments;

use Ifthenpay\Payment\Lib\Request\WebService;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;

class PaymentStatus
{
    protected $data;
    protected $webService;
    protected $logger;

    public function __construct(WebService $webService, IfthenpayLogger $logger)
    {
        $this->webService = $webService;
        $this->logger = $logger;
    }


    public function setData(GatewayDataBuilder $data): PaymentStatus
    {
        $this->data = $data;
        $this->logger->info('payment status data set with success', ['data' => $this->data]);
        return $this;
    }
}
