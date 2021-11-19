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

namespace Ifthenpay\Payment\Lib\Factory\Payment;

use Ifthenpay\Payment\Lib\Factory\Factory;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Lib\Request\WebService;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Ifthenpay\Payment\Lib\Payments\CCardPaymentStatus;
use Ifthenpay\Payment\Lib\Payments\MbWayPaymentStatus;
use Ifthenpay\Payment\Lib\Payments\PayshopPaymentStatus;
use Ifthenpay\Payment\Lib\Payments\MultibancoPaymentStatus;
use Ifthenpay\Payment\Lib\Contracts\Payments\PaymentStatusInterface;


class PaymentStatusFactory extends Factory
{
    private $logger;
    private $webService;

    public function __construct(
        WebService $webService,
        IfthenpayLogger $logger
    )
	{
        $this->webService = $webService;
        $this->logger = $logger;
    }

    public function build(): PaymentStatusInterface {
        switch ($this->type) {
           case Gateway::MULTIBANCO:
                return new MultibancoPaymentStatus(
                    $this->webService,
                    $this->logger
                );
            case Gateway::MBWAY:
                return new MbWayPaymentStatus(
                    $this->webService,
                    $this->logger
                );
            case Gateway::PAYSHOP:
                return new PayshopPaymentStatus(
                    $this->webService,
                    $this->logger
                );
            case Gateway::CCARD:
                return new CCardPaymentStatus(
                    $this->webService,
                    $this->logger
                );
            default:
                throw new \Exception('Unknown Payment Status Class');
        }
    }
}
