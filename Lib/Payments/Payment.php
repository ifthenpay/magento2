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
use Ifthenpay\Payment\Lib\Builders\DataBuilder;
use Ifthenpay\Payment\Lib\Contracts\Models\PaymentModelInterface;

class Payment
{

    protected $orderId;
    protected $valor;
    protected $dataBuilder;
    protected $webService;
    protected $logger;

    public function __construct(string $orderId, string $valor, DataBuilder $dataBuilder, WebService $webService, IfthenpayLogger $ifthenpayLogger)
    {
        $this->orderId = $orderId;
        $this->valor = $this->formatNumber(number_format(floatval($valor), 2, '.', ''));
        $this->dataBuilder = $dataBuilder;
        $this->webService = $webService;
        $this->logger = $ifthenpayLogger;
    }

    protected function formatNumber(string $number) : string
    {
        $verifySepDecimal = number_format(99, 2);

        $valorTmp = $number;

        $sepDecimal = substr($verifySepDecimal, 2, 1);

        $hasSepDecimal = true;

        $i = (strlen($valorTmp) -1);

        for ($i; $i!=0; $i-=1) {
            if (substr($valorTmp, $i, 1)=="." || substr($valorTmp, $i, 1)==",") {
                $hasSepDecimal = true;
                $valorTmp = trim(substr($valorTmp, 0, $i))."@".trim(substr($valorTmp, 1+$i));
                break;
            }
        }

        if ($hasSepDecimal!=true) {
            $valorTmp=number_format($valorTmp, 2);

            $i=(strlen($valorTmp)-1);

            for ($i; $i!=1; $i--) {
                if (substr($valorTmp, $i, 1)=="." || substr($valorTmp, $i, 1)==",") {
                    $hasSepDecimal = true;
                    $valorTmp = trim(substr($valorTmp, 0, $i))."@".trim(substr($valorTmp, 1+$i));
                    break;
                }
            }
        }

        for ($i=1; $i!=(strlen($valorTmp)-1); $i++) {
            if (substr($valorTmp, $i, 1)=="." || substr($valorTmp, $i, 1)=="," || substr($valorTmp, $i, 1)==" ") {
                $valorTmp = trim(substr($valorTmp, 0, $i)).trim(substr($valorTmp, 1+$i));
                break;
            }
        }

        if (strlen(strstr($valorTmp, '@'))>0) {
            $valorTmp = trim(substr($valorTmp, 0, strpos($valorTmp, '@'))).trim($sepDecimal).trim(substr($valorTmp, strpos($valorTmp, '@')+1));
        }

        return $valorTmp;
    }

    protected function checkIfPaymentExist(string $orderId, PaymentModelInterface $paymentModel)
    {
        $paymentData = $paymentModel->getByOrderId($orderId);
        return !empty($paymentData) ? $paymentData : false;
    }

    protected function logWebserviceRequestError(string $paymentMethod, \Throwable $error, array $webServiceRequestData): void
    {
        $this->logger->debug('error making ' . $paymentMethod . ' webservice request', [
            'error' => $error,
            'errorMessage' => $error->getMessage(),
            'webserviceDataRequest' => $webServiceRequestData
        ]);
    }
}
