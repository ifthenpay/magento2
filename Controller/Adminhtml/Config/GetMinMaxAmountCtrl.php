<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Ifthenpay\Payment\Logger\Logger;
use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;
use Ifthenpay\Payment\Gateway\Config\CofidisConfig;
use Ifthenpay\Payment\Lib\HttpClient;






class GetMinMaxAmountCtrl extends Action
{
	private $cofidisConfig;
	private $resultJsonFactory;
	private $configData;
	private $logger;
	private $httpClient;


	protected $configFactory;

	public function __construct(
		Context $context,
		JsonFactory $resultJsonFactory,
		Logger $logger,
		IfthenpayConfig $configData,
		CofidisConfig $cofidisConfig,
		HttpClient $httpClient
	) {
		parent::__construct($context);
		$this->resultJsonFactory = $resultJsonFactory;
		$this->logger = $logger;
		$this->configData = $configData;
		$this->cofidisConfig = $cofidisConfig;
		$this->httpClient = $httpClient;
	}

	public function execute()
	{
		try {
			$requestData = $this->getRequest()->getParams();

			$this->configData->setScopeAndScopeCode($requestData['scope'], $requestData['scopeCode']);
			$this->cofidisConfig->setScopeAndScopeCode($requestData['scope'], $requestData['scopeCode']);


			$cofidisKey = $this->getRequest()->getParam('cofidis_key');

			$url = ConfigVars::API_URL_COFIDIS_GET_MAX_MIN_AMOUNT . '/' . $cofidisKey;

			$this->httpClient->doGet($url, []);
			$responseArray = $this->httpClient->getBodyArray();
			$status = $this->httpClient->getStatus();

			if ($status !== 200 || !(isset($responseArray['message']) && $responseArray['message'] == 'success')) {
				throw new \Exception('Error: Min Max request failed.');
			}

			$min = $responseArray['limits']['minAmount'];
			$max = $responseArray['limits']['maxAmount'];


			return $this->resultJsonFactory->create()->setData(['success' => true, 'min' => $min, 'max' => $max]);
		} catch (\Throwable $th) {
			$this->logger->error('Failed to get corresponding Min Max.', [
				'error' => $th,
			]);

			return $this->resultJsonFactory->create()->setData(['error' => true, 'errorMessage' => __('Failed to get corresponding Min Max.')]);
		}
	}
}
