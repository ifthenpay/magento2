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
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Soap
 * @package Magento\Payment\Gateway\Http\Client
 * @api
 */
class MbwayAuthorizationClient implements ClientInterface
{
	private const SUCCESS         		= '000'; // Request initialized successfully (pending acceptance).
	private const ERROR           		= '999'; // Error initializing the request. You can try again.
	private const INVALID_MOBILE_NUMBER = '113'; // Mobile number provided does not exist.
	private const INCOMPLETE      		= '100'; // The initialization request could not be completed. You can try again.
	private const DECLINED        		= '122'; // Transaction declined by SIBS to the user.
	private const INVALID_ACCOUNT 		= '-1'; // The MB WAY key is invalid.

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


		if ($status !== 200) {
			throw new LocalizedException(__('Error: MB WAY request failed, please try again later or contact support.'));
		}

		if ($responseArray['Status'] !== self::SUCCESS) {
			switch ($responseArray['Status']) {
				case self::INVALID_MOBILE_NUMBER:
					throw new LocalizedException(__('Error: The mobile number provided does not exist, please check and try again.'));
					break;
				case self::INVALID_ACCOUNT:
					throw new LocalizedException(__('Error: MB WAY request failed, please contact support.'));
					break;
				case self::DECLINED:
					throw new LocalizedException(__('Error: MB WAY request declined.'));
					break;
				case self::INCOMPLETE:
				case self::ERROR:
					throw new LocalizedException(__('Error: MB WAY request could not be completed, please try again later or contact support.'));
					break;
				default:
					throw new LocalizedException(__('Error: MB WAY request failed unexpectedly, please try again later or contact support.'));
					break;
			}
		}

		return $responseArray;
	}
}
