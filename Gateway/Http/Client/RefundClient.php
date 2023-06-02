<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Ifthenpay\Payment\Lib\HttpClient;
use Ifthenpay\Payment\Logger\Logger;

/**
 * Class Soap
 * @package Magento\Payment\Gateway\Http\Client
 * @api
 */
class RefundClient implements ClientInterface
{
    const SUCCESS = '000';

    private $httpClient;

    private $logger;




    public function __construct(Logger $logger, HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }


    public function placeRequest(TransferInterface $transferObject)
    {
        $url = $transferObject->getUri();
        $payload = $transferObject->getBody();
        $this->httpClient->doPost($url, $payload);
        $responseArray = $this->httpClient->getBodyArray();
        $status = $this->httpClient->getStatus();

        if ($status !== 200) {
            $this->logger->error('gateway/response/refundClient: refund request failed', [
                'transferObject' => $transferObject
            ]);

            throw new \Exception(__('Error: Refund request failed.'));
        }

        return $responseArray;

    }


}
