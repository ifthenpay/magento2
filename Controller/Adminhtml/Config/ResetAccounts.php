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

namespace Ifthenpay\Payment\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Ifthenpay\Payment\Helper\Data;
use Magento\Framework\Controller\Result\JsonFactory;


class ResetAccounts extends Action
{
    private $resultJsonFactory;
    private $helperData;
    private $gateway;
    private $logger;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Data $helperData,
        Gateway $gateway,
        IfthenpayLogger $logger
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helperData = $helperData;
        $this->gateway = $gateway;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $requestData = $this->getRequest()->getParams();
            $backofficeKey = $this->helperData->getBackofficeKey();

            if (!$backofficeKey) {
                $this->logger->debug('Backoffice key is required for Reseting accounts', [
                    'errorMessage' => __('backofficeKeyRequired'),
                    'backofficeKey' => $backofficeKey,
                    'requestData' => $requestData
                ]);
                return $this->resultJsonFactory->create()->setData(['error' => __('backofficeKeyRequired')]);
            }
            $this->gateway->authenticate($backofficeKey);
            $this->helperData->saveUserPaymentMethods($this->gateway->getPaymentMethods());
            $this->helperData->saveUserAccount($this->gateway->getAccount());
            $this->logger->debug('Reseting accounts with success', [
                'backofficeKey' => $backofficeKey,
                'requestData' => $requestData
            ]);
            return $this->resultJsonFactory->create()->setData(['success' => true]);
        } catch (\Throwable $th) {
            $this->logger->debug('Error Reseting accounts', [
                'error' => $th,
                'errorMessage' => $th->getMessage(),
                'backofficeKey' => $backofficeKey,
                'requestData' => $requestData
            ]);
            return $this->resultJsonFactory->create()->setData(['error' => true]);
        }
    }
}