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

use Ifthenpay\Payment\Lib\HttpClient;


class UpdatesService
{

    private $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }


    public function getUpgradeJsonFile(): array
    {
        $this->httpClient->doGet(
            'https://ifthenpay.com/modulesUpgrade/magento/24/upgrade.json',
            [],
            true
        );
        $fileContent = json_decode($this->httpClient->getBody(), true);
        if (empty($fileContent)) {
            return [];
        }

        return $fileContent;
    }
}
