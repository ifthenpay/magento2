<?php
/**
* Ifthenpay_Payment module dependency
*
* @category    Gateway Payment
* @package     Ifthenpay_Payment
* @author      Ifthenpay
* @copyright   Ifthenpay (http://www.ifthenpay.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

namespace Ifthenpay\Payment\Controller\Frontend;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Action\Context;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Logger\IfthenpayLogger;


class UpdateUserAccount extends Action
{

    private $dataFactory;
    private $gateway;
    private $logger;

    public function __construct(
        Context $context,
        DataFactory $dataFactory,
        Gateway $gateway,
        IfthenpayLogger $logger
    ) {
        parent::__construct($context);
        $this->dataFactory = $dataFactory;
        $this->gateway = $gateway;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $requestData = $this->getRequest()->getParams();
            $configData = $this->dataFactory->setType($requestData['paymentMethod'])->build();
            $userAccountToken = $configData->getUpdateUserAccountToken();

            if (!isset($requestData['updateUserToken']) || $requestData['updateUserToken'] !== $userAccountToken) {
                $this->logger->debug('User account token is invalid', [
                    'requestData' => $requestData,
                    'userAccountToken' => $userAccountToken                    
                ]);
                return $this->getResponse()
                ->setStatusCode(Http::STATUS_CODE_400)
                ->setContent('token is invalid');
            }

            $backofficeKey = $configData->getBackofficeKey();

            if (!$backofficeKey) {
                $this->logger->debug('User account backofficeKey is required', [
                    'requestData' => $requestData                    
                ]);
                return $this->getResponse()
                ->setStatusCode(Http::STATUS_CODE_200)
                ->setContent('BackofficeKey is required');
            }
            $this->gateway->authenticate($backofficeKey);
            $userPaymentMethods = $this->gateway->getPaymentMethods();
            $userAccount = $this->gateway->getAccount();
            $configData->saveUserPaymentMethods($userPaymentMethods);
            $configData->saveUserAccount($userAccount);
            $configData->deleteUpdateUserAccountToken();
            $this->logger->debug('User account updated with success', [
                'requestData' => $requestData,
                'backofficeKey' => $backofficeKey,
                'userPaymentMethods' => $userPaymentMethods,
                'userAccount' => $userAccount
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