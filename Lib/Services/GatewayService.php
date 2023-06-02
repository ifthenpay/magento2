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
    const MBWAY = ConfigVars::MBWAY;
    const PAYSHOP = ConfigVars::PAYSHOP;
    const CCARD = ConfigVars::CCARD;

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
            self::CCARD
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
            } elseif (is_numeric($account['Entidade'])) {
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

}
