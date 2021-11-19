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
use Ifthenpay\Payment\Lib\Builders\DataBuilder;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Lib\Factory\Payment\PaymentFactory;

class Gateway
{
    const MULTIBANCO = 'multibanco';
    const MBWAY = 'mbway';
    const PAYSHOP = 'payshop';
    const CCARD = 'ccard';

    private $webService;
    private $paymentFactory;
    private $account;
    private $paymentMethods = [self::MULTIBANCO, self::MBWAY, self::PAYSHOP, self::CCARD];
    private $paymentMethodsCanCancel = [self::MBWAY, self::CCARD, self::PAYSHOP];

    public function __construct(WebService $webService, PaymentFactory $paymentFactory)
    {
        $this->webService = $webService;
        $this->paymentFactory = $paymentFactory;
    }

    public function getPaymentMethodsType(): array
    {
        return $this->paymentMethods;
    }

    public function getPaymentMethodsCanCancel(): array
    {
        return $this->paymentMethodsCanCancel;
    }

    public function checkIfthenpayPaymentMethod(string $paymentMethod): bool
    {
        if (in_array($paymentMethod, $this->paymentMethods)) {
            return true;
        }
        return false;
    }

    public function authenticate(string $backofficeKey): void
    {
            $authenticate = $this->webService->postRequest(
                'https://www.ifthenpay.com/IfmbWS/ifmbws.asmx/' .
                'getEntidadeSubentidadeJsonV2',
                [
                   'chavebackoffice' => $backofficeKey,
                ]
            )->getResponseJson();

        if (!$authenticate[0]['Entidade'] && empty($authenticate[0]['SubEntidade'])) {
            throw new \Exception('Backoffice key is invalid');
        } else {
            $this->account = $authenticate;
        }
    }

    public function getAccount(): array
    {
        return $this->account;
    }

    public function setAccount(array $account)
    {
        $this->account = $account;
    }

    public function getPaymentMethods(): array
    {
        $userPaymentMethods = [];

        foreach ($this->account as $account) {
            if (in_array(strtolower($account['Entidade']), $this->paymentMethods)) {
                $userPaymentMethods[] = strtolower($account['Entidade']);
            } elseif (is_numeric($account['Entidade'])) {
                $userPaymentMethods[] = $this->paymentMethods[0];
            }
        }
        return array_unique($userPaymentMethods);
    }

    public function getSubEntidadeInEntidade(string $entidade): array
    {
        return array_values(array_filter(
            $this->account,
            function ($value) use ($entidade) {
                return $value['Entidade'] === $entidade;
            }
        ));
    }

    public function getEntidadeSubEntidade(string $paymentMethod): array
    {
        $list = null;
        if ($paymentMethod === self::MULTIBANCO) {
            $list = array_filter(
                array_column($this->account, 'Entidade'),
                function ($value) {
                    return is_numeric($value);
                }
            );
        } else {
            $list = [];
            foreach (array_column($this->account, 'SubEntidade', 'Entidade') as $key => $value) {
                if ($key === strtoupper($paymentMethod)) {
                    $list[] = $value;
                }
            }
        }
        return $list;
    }

    public function execute(string $paymentMethod, GatewayDataBuilder $data, string $orderId, string $valor): DataBuilder
    {
        $paymentMethod = $this->paymentFactory
            ->setType($paymentMethod)
            ->setData($data)
            ->setOrderId($orderId)
            ->setValor($valor)
            ->build();
        return $paymentMethod->buy();
    }
}
