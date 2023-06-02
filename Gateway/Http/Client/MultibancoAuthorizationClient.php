<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Gateway\Http\Client;

use Ifthenpay\Payment\Config\ConfigVars;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Ifthenpay\Payment\Lib\HttpClient;

/**
 * Class Soap
 * @package Magento\Payment\Gateway\Http\Client
 * @api
 */
class MultibancoAuthorizationClient implements ClientInterface
{
    const SUCCESS = '0';

    private $httpClient;

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var ConverterInterface | null
     */
    private $converter;



    public function __construct(Logger $logger, HttpClient $httpClient, ConverterInterface $converter = null)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->converter = $converter;
    }


    public function placeRequest(TransferInterface $transferObject)
    {
        $url = $transferObject->getUri();
        $payload = $transferObject->getBody();

        $entity = $payload['entity'];

        // if is dynamic request
        if ($entity == ConfigVars::MULTIBANCO_DYNAMIC) {

            $newPayload = [
                'mbKey' => $payload['subEntity'],
                "orderId" => $payload['orderId'],
                "amount" => $payload['orderTotal'],
                "description" => 'magento 2 request',
            ];

            if ($payload['deadline'] != '') {
                $newPayload['expiryDays'] = $payload['deadline'];
            }


            $this->httpClient->doPost($url, $newPayload);

            $responseArray = $this->httpClient->getBodyArray();

            $status = $this->httpClient->getStatus();

            if ($status !== 200 || $responseArray['Status'] !== self::SUCCESS) {
                throw new \Exception('Error: Dynamic Multibanco request failed.');
            }

            // set the expiryDays to the response to use to set correct server time
            $responseArray['daysToDeadline'] = $payload['deadline'];

            return $responseArray;
        } else {

            return $payload;
        }
    }
}
