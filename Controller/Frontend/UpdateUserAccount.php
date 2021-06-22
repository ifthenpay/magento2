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
use \Magento\Framework\App\Response\Http;
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

            if (!isset($requestData['updateUserToken']) || $requestData['updateUserToken'] !== $configData->getUpdateUserAccountToken()) {
                return $this->getResponse()
                ->setStatusCode(Http::STATUS_CODE_400)
                ->setContent('token is invalid');
            }

            $backofficeKey = $configData->getBackofficeKey();

            if (!$backofficeKey) {
                return $this->getResponse()
                ->setStatusCode(Http::STATUS_CODE_200)
                ->setContent('BackofficeKey is required');
            }
            $this->gateway->authenticate($backofficeKey);
            $configData->saveUserPaymentMethods($this->gateway->getPaymentMethods());
            $configData->saveUserAccount($this->gateway->getAccount());
            $configData->deleteUpdateUserAccountToken();
            $this->logger->debug('UpdateUserAccount: User account updated with success');
            return $this->getResponse()
                ->setStatusCode(Http::STATUS_CODE_200)
                ->setContent('User Account updated with success!');

        } catch (\Throwable $th) {
            $this->logger->debug('UpdateUserAccount: Error updating user account - ' . $th->getMessage());
            return $this->getResponse()
                ->setStatusCode(Http::STATUS_CODE_400)
                ->setContent($th->getMessage());
        }
    }
}