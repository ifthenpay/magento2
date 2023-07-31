<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Controller\Adminhtml\Config;


use Ifthenpay\Payment\Lib\Services\GatewayService;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Action\Context;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;
use Ifthenpay\Payment\Logger\Logger;
use Magento\Framework\Controller\Result\JsonFactory;



class RefreshUserAccountsInternalyCtrl extends Action
{

    private $configData;
    private $logger;
    protected $gatewayService;
    private $resultJsonFactory;



    public function __construct(
        Context $context,
        IfthenpayConfig $configData,
        GatewayService $gatewayService,
        Logger $logger,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->configData = $configData;
        $this->logger = $logger;
        $this->gatewayService = $gatewayService;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        try {
            $requestData = $this->getRequest()->getParams();

            $this->configData->setScopeAndScopeCode($requestData['scope'], $requestData['scopeCode']);


            // validate backoffice key
            $backofficeKey = $this->configData->getBackofficeKey();
            if (!$backofficeKey) {
                $this->logger->debug('User account backofficeKey is not present', [
                    'requestData' => $requestData
                ]);
                return $this->getResponse()
                    ->setStatusCode(Http::STATUS_CODE_400)
                    ->setContent('BackofficeKey is required');
            }

            // get acounts for this backoffice key
            $this->gatewayService->setAccountsWithRequest($backofficeKey);
            $userPaymentMethods = $this->gatewayService->getUserPaymentMethods();
            $userAccounts = $this->gatewayService->getAccounts();

            // save accounts attached to this backoffice key in DB config
            $this->configData->saveUserAccounts($userAccounts);
            // save payment method names attached to this backoffice key in DB config
            $this->configData->saveUserPaymentMethods($userPaymentMethods);



            $this->logger->debug('User account updated with success (internal)', [
                'requestData' => $requestData,
                'backofficeKey' => $backofficeKey,
                'userPaymentMethods' => $userPaymentMethods,
                'userAccount' => $userAccounts
            ]);

            $this->messageManager->addSuccessMessage(__('Accounts refreshed with success.'));
            return $this->resultJsonFactory->create()->setData(['success' => true]);

        } catch (\Throwable $th) {
            $this->logger->debug('Error updating user account', [
                'error' => $th,
                'errorMessage' => $th->getMessage(),
                'requestData' => $requestData,
                'backofficeKey' => $backofficeKey,
            ]);
            $this->messageManager->addErrorMessage(__('Failed to refresh accounts.'));
            return $this->resultJsonFactory->create()->setData(['error' => true]);
        }
    }
}