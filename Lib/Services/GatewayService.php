<?php

/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Ifthenpay\Payment\Lib\Services;

use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Lib\HttpClient;


class GatewayService
{
    const MULTIBANCO = ConfigVars::MULTIBANCO;
    const MULTIBANCO_DYNAMIC = ConfigVars::MULTIBANCO_DYNAMIC;
    const MBWAY = ConfigVars::MBWAY;
    const PAYSHOP = ConfigVars::PAYSHOP;
    const CCARD = ConfigVars::CCARD;
    const COFIDIS = ConfigVars::COFIDIS;
    const PIX = ConfigVars::PIX;
    const IFTHENPAYGATEWAY = ConfigVars::IFTHENPAYGATEWAY;


    private $httpClient;
    private $accounts;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * get all payment methods, this includes the payment methods that are not available for the user
     * @return array
     */
    public function getPaymentMethods(): array
    {
        return [
            self::MULTIBANCO,
            self::MBWAY,
            self::PAYSHOP,
            self::CCARD,
            self::COFIDIS,
            self::PIX,
            self::IFTHENPAYGATEWAY
        ];
    }

    /**
     * makes a request to ifthenpay api with backoffice key and sets the accounts with the response
     * @param string $backofficeKey
     * @throws \Exception
     * @return void
     */
    public function setAccountsWithRequest(string $backofficeKey): void
    {

        $this->httpClient->doPost(
            ConfigVars::API_URL_GET_GATEWAYK_KEYS,
            [
                'backofficekey' => $backofficeKey,
            ],
            false
        );
        $gatewayKeys = json_decode($this->httpClient->getBody(), true);

        $this->httpClient->doPost(
            ConfigVars::API_URL_GET_ACCOUNTS_BY_BACKOFFICE,
            [
                'chavebackoffice' => $backofficeKey,
            ],
            false
        );
        $accounts = json_decode($this->httpClient->getBody(), true);

        if (empty($accounts) || (!$accounts[0]['Entidade'] && empty($authenticate[0]['SubEntidade']))) {
            throw new \Exception('Backoffice key is invalid');
        } else {
            $this->accounts = $accounts;
        }

        if (!empty($gatewayKeys)) {
            $this->accounts[] = [
                'Entidade' => 'IFTHENPAYGATEWAY',
                'SubEntidade' => $gatewayKeys
            ];
        }
    }

    public function getAccounts(): array
    {
        return $this->accounts;
    }

    public function setAccounts(array $account)
    {
        $this->accounts = $account;
    }

    public function getUserPaymentMethods(): array
    {
        $userPaymentMethods = [];

        foreach ($this->accounts as $account) {
            if (in_array(strtolower($account['Entidade']), $this->getPaymentMethods())) {
                $userPaymentMethods[] = strtolower($account['Entidade']);
            } elseif (is_numeric($account['Entidade']) || $account['Entidade'] == self::MULTIBANCO_DYNAMIC) {
                $userPaymentMethods[] = self::MULTIBANCO;
            }
        }
        return array_unique($userPaymentMethods);
    }

    public function getSubEntity(string $entity): array
    {
        return array_values(
            array_filter(
                $this->accounts,
                function ($value) use ($entity) {
                    return $value['Entidade'] === $entity;
                }
            )
        );
    }



    public function is_dynamic(array $paymentMethodGroupArray, string $gatewayKey)
    {
        foreach ($paymentMethodGroupArray as $paymentMethodGroup) {
            if (
                isset($paymentMethodGroup['Tipo']) &&
                isset($paymentMethodGroup['GatewayKey']) &&
                $paymentMethodGroup['GatewayKey'] === $gatewayKey &&
                $paymentMethodGroup['Tipo'] === 'Dinâmicas'
            ) {
                return true;
            }
        }
        return false;
    }



    public function getIfthenpayGatewayPaymentMethodsDataByBackofficeKeyAndGatewayKey($backofficeKey, $gatewayKey): array
    {

        $this->httpClient->doGet(
            'https://api.ifthenpay.com/gateway/methods/available',
            [],
            false
        );
        $methods = json_decode($this->httpClient->getBody(), true);
        if (empty($methods)) {
            return [];
        }


        $this->httpClient->doPost(
            'https://ifthenpay.com/IfmbWS/ifthenpaymobile.asmx/GetAccountsByGatewayKey',
            [
                'backofficekey' => $backofficeKey,
                'gatewayKey' => $gatewayKey
            ],
            false
        );
        $accounts = json_decode($this->httpClient->getBody(), true);
        if (empty($accounts)) {
            return [];
        }


        foreach ($methods as &$method) {

            $methodCode = $method['Entity'];
            $filteredAccounts = array_filter($accounts, function ($item) use ($methodCode) {
                return $item['Entidade'] === $methodCode || ($methodCode === 'MB' && is_numeric($item['Entidade']));
            });

            $method['accounts'] = $filteredAccounts;
        }
        unset($method);

        return $methods;
    }



    public function getIthenpaygatewayKeys($accounts)
    {
        foreach (array_column($accounts, 'SubEntidade', 'Entidade') as $key => $value) {
            if ($key === 'IFTHENPAYGATEWAY') {
                return $value;
            }
        }
    }



    public function isGatewayKeyStatic(array $gatewayKeySettings): bool
    {
        return $gatewayKeySettings['Tipo'] === 'Estáticas';
    }
}
