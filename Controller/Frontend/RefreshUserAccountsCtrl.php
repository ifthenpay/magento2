<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Controller\Frontend;

use Ifthenpay\Payment\Lib\Services\GatewayService;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Action\Context;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;
use Ifthenpay\Payment\Logger\Logger;



class RefreshUserAccountsCtrl extends Action
{

    private $configData;
    private $logger;
    protected $gatewayService;


    public function __construct(
        Context $context,
        IfthenpayConfig $configData,
        GatewayService $gatewayService,
        Logger $logger,
    ) {
        parent::__construct($context);
        $this->configData = $configData;
        $this->logger = $logger;
        $this->gatewayService = $gatewayService;
    }

    public function execute()
    {
        try {
            $requestData = $this->getRequest()->getParams();

            $this->configData->setScopeAndScopeCode($requestData['scope'], $requestData['scopeCode']);


            // validate request token
            $storedToken = $this->configData->getRequestToken();
            if (!isset($requestData['requestToken']) || $requestData['requestToken'] !== $storedToken) {
                $this->logger->debug('User account token is invalid', [
                    'requestData' => $requestData,
                    'userAccountToken' => $storedToken
                ]);
                return $this->getResponse()
                    ->setStatusCode(Http::STATUS_CODE_400)
                    ->setContent('token is invalid');
            }

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


            $this->logger->debug('User account updated with success', [
                'requestData' => $requestData,
                'backofficeKey' => $backofficeKey,
                'userPaymentMethods' => $userPaymentMethods,
                'userAccount' => $userAccounts
            ]);
            return $this->getResponse()
                ->setStatusCode(Http::STATUS_CODE_200)
                ->setContent('User Account updated with success!');

        } catch (\Throwable $th) {
            $this->logger->debug('Error updating user account', [
                'error' => $th,
                'errorMessage' => $th->getMessage(),
                'requestData' => $requestData,
                'backofficeKey' => $backofficeKey,
            ]);
            return $this->getResponse()
                ->setStatusCode(Http::STATUS_CODE_400)
                ->setContent($th->getMessage());
        }
    }
}
