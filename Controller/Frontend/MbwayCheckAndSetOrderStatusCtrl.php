<?php

/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Controller\Frontend;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Gateway\Config\MbwayConfig;
use Ifthenpay\Payment\Lib\Factory\ServiceFactory;
use Ifthenpay\Payment\Lib\HttpClient;
use Ifthenpay\Payment\Logger\Logger;
use Ifthenpay\Payment\Model\ScopeConfigResolver;




class MbwayCheckAndSetOrderStatusCtrl extends Action
{
	private $config;
	private $resultJsonFactory;
	private $logger;
	private $httpClient;
	private $service;
	private $scopeConfigResolver;

	public const PENDING          = '123'; // Transaction pending payment,
	public const PAID             = '000'; // Transaction successfully completed (Payment confirmed),
	public const REJECTED_BY_USER = '020'; // Transaction rejected by the user.
	public const EXPIRED          = '101'; // Transaction expired (the user has 4 minutes to accept the payment in the MB WAY App before expiring).
	public const DECLINED         = '122'; // Transaction declined to the user.


	public function __construct(
		Context $context,
		MbwayConfig $config,
		Logger $logger,
		JsonFactory $resultJsonFactory,
		HttpClient $httpClient,
		ServiceFactory $serviceFactory,
		ScopeConfigResolver $scopeConfigResolver
	) {
		parent::__construct($context);
		$this->config = $config;
		$this->service = $serviceFactory->createService(ConfigVars::MBWAY_CODE);
		$this->httpClient = $httpClient;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->logger = $logger;
		$this->scopeConfigResolver = $scopeConfigResolver;
	}

	public function execute()
	{
		try {
			$requestData = $this->getRequest()->getParams();

			$store = $this->scopeConfigResolver->storeManager->getStore($requestData['storeId']);

			$mbwayKey = $store->getConfig('payment/ifthenpay_mbway/key');

			$transactionId = $requestData['transaction_id'];
			$url = ConfigVars::API_URL_POST_MBWAY_GET_PAYMENT_STATUS;

			$payload = [
				'mbWayKey' => $mbwayKey,
				'requestId' => $transactionId
			];

			$this->httpClient->doGet($url, $payload);
			$responseArray = $this->httpClient->getBodyArray();
			$status = $this->httpClient->getStatus();

			if ($status !== 200) {
				throw new \Exception('Error: MB WAY request failed.');
			}

			$PaymentStatusCode = $responseArray['Status'];

			if ($PaymentStatusCode === self::PAID) {

				return $this->resultJsonFactory->create()->setData(['orderStatus' => 'paid']);
			}
			if ($PaymentStatusCode === self::PENDING) {

				if ($responseArray['Message'] === 'Request not found') { // edgecase transaction not found
					return $this->resultJsonFactory->create()->setData(['orderStatus' => 'error']);
				}

				return $this->resultJsonFactory->create()->setData(['orderStatus' => 'pending']);
			}
			if ($PaymentStatusCode === self::REJECTED_BY_USER) {
				return $this->resultJsonFactory->create()->setData(['orderStatus' => 'refused']);
			}

			if ($PaymentStatusCode === self::EXPIRED) {
				return $this->resultJsonFactory->create()->setData(['orderStatus' => 'expired']);
			}

			return $this->resultJsonFactory->create()->setData(['orderStatus' => 'error']);
		} catch (\Throwable $th) {
			return $this->resultJsonFactory->create()->setData(['orderStatus' => $th->getMessage()]);
		}
	}
}
