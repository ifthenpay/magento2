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
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Magento\Framework\Controller\Result\JsonFactory;


class ResetAccounts extends Action
{
    private $resultJsonFactory;
    private $dataFactory;
    private $gateway;
    private $logger;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        DataFactory $dataFactory,
        Gateway $gateway,
        IfthenpayLogger $logger
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataFactory = $dataFactory;
        $this->gateway = $gateway;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $requestData = $this->getRequest()->getParams();
            $configData = $this->dataFactory->setType($requestData['paymentMethod'])->build();
            $backofficeKey = $configData->getBackofficeKey();

            if (!$backofficeKey) {
                return $this->resultJsonFactory->create()->setData(['error' => __('backofficeKeyRequired')]);
            }
            $this->gateway->authenticate($backofficeKey);
            $configData->saveUserPaymentMethods($this->gateway->getPaymentMethods());
            $configData->saveUserAccount($this->gateway->getAccount());
            $this->logger->debug('ResetAccounts: Reseting accounts with success');
            return $this->resultJsonFactory->create()->setData(['success' => true]);
        } catch (\Throwable $th) {
            $this->logger->debug('ResetAccounts: Error Reseting accounts - ' . $th->getMessage());
            return $this->resultJsonFactory->create()->setData(['error' => true]);
        }
    }
}