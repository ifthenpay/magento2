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
use Magento\Payment\Gateway\Http\ConverterInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Ifthenpay\Payment\Lib\HttpClient;

/**
 * Class Soap
 * @package Magento\Payment\Gateway\Http\Client
 * @api
 */
class IfthenpaygatewayAuthorizationClient implements ClientInterface
{
    const SUCCESS_STATUS = '0';
    const SUCCESS_MESSAGE = 'Success';

    private $httpClient;

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var ConverterInterface | null
     */
    private $converter;



    public function __construct(Logger $logger, HttpClient $httpClient, ?ConverterInterface $converter = null)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->converter = $converter;
    }


    public function placeRequest(TransferInterface $transferObject)
    {
        $url = $transferObject->getUri();
        $payload = $transferObject->getBody();


        $this->httpClient->doPost($url, $payload);

        $responseArray = $this->httpClient->getBodyArray();

        $status = $this->httpClient->getStatus();

        if ($status !== 200 || $responseArray['PinCode'] === '' || $responseArray['RedirectUrl'] === '') {
            throw new \Exception('Error: Ifthenpay Gateway request failed.');
        }

        // add deadline to response to later register in DB
        $responseArray['deadline'] = $payload['expiredate'];

        return $responseArray;
    }
}
