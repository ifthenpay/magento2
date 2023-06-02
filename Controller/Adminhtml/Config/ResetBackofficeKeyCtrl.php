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
use Ifthenpay\Payment\Lib\Factory\ConfigFactory;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;



class ResetBackofficeKeyCtrl extends Action
{
    private $resultJsonFactory;
    private $configData;
    private $logger;
    private $configFactory;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        IfthenpayConfig $configData,
        Logger $logger,
        ConfigFactory $configFactory,
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configData = $configData;
        $this->logger = $logger;
        $this->configFactory = $configFactory;
    }

    public function execute()
    {
        try {
            $requestData = $this->getRequest()->getParams();

            $this->deleteAllConfig($requestData['scope'], $requestData['scopeCode']);

            $this->messageManager->addSuccessMessage(__('Backoffice Key reset with success.'));
            return $this->resultJsonFactory->create()->setData(['success' => true]);

        } catch (\Throwable $th) {
            $this->logger->error('Error Reseting backofficeKey', [
                'error' => $th,
            ]);

            $this->messageManager->addErrorMessage(__('Failed to reset Backoffice Key.'));
            return $this->resultJsonFactory->create()->setData(['error' => true]);
        }
    }


    private function deleteAllConfig($scope, $scopeCode)
    {

        // delete config of each payment method
        foreach (ConfigVars::PAYMENT_METHODS as $paymentMethod) {
            $paymentMethodConfig = $this->configFactory->createConfig(ConfigVars::VENDOR_PREFIX . $paymentMethod);

            $paymentMethodConfig->setScopeAndScopeCode($scope, $scopeCode);
            $paymentMethodConfig->deleteAllPaymentMethodConfig();
        }

        // delete general config
        $this->configData->setScopeAndScopeCode($scope, $scopeCode);

        $this->configData->deleteAllGeneralConfig();

    }
}
