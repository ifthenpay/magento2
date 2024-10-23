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
use Magento\Framework\Module\ResourceInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Ifthenpay\Payment\Logger\Logger;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;
use Ifthenpay\Payment\Config\ConfigVars;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Message\ManagerInterface;

class RequestGatewayMethodCtrl extends Action
{
    private $resultJsonFactory;
    private $configData;
    private $storeManager;
    private $logger;
    private $transportBuilder;
    protected $scope;
    protected $scopeCode;
    private $moduleResource;
    protected $messageManager;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        IfthenpayConfig $configData,
        StoreManagerInterface $storeManager,
        Logger $logger,
        TransportBuilder $transportBuilder,
        ResourceInterface $moduleResource,
        ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configData = $configData;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->transportBuilder = $transportBuilder;
        $this->moduleResource = $moduleResource;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        try {
            $requestData = $this->getRequest()->getParams();

            // set the scope and scopeCode to the configData object
            if (isset($requestData['scope']) && isset($requestData['scopeCode'])) {
                $this->configData->setScopeAndScopeCode($requestData['scope'], $requestData['scopeCode']);
            }

            $token = $this->configData->getRequestTokenAndSave();
            $from = [
                "name" => $this->configData->getStorename(),
                "email" => $this->configData->getStoreEmail()
            ];
            $to = "suporte@ifthenpay.com";


            // module version
            $version = $this->moduleResource->getDbVersion(ConfigVars::MODULE_NAME);

            $templateVars = [
                "backoffice_key" => $this->configData->getBackofficeKey(),
                "gateway_key" => $requestData['gatewayKey'],
                "store_email" => $from['email'],
                "payment_method" => $requestData['paymentMethod'],
                "ecommerce_platform" => "Magento 2",
                "module_version" => $version,
                "store_name" => $from['name']
            ];


            $this->transportBuilder
                ->setTemplateIdentifier('request_gateway_method')
                ->setTemplateOptions(['area' => 'adminhtml', 'store' => ScopeConfigInterface::SCOPE_TYPE_DEFAULT])
                ->setTemplateVars($templateVars)
                ->setFromByScope($from)
                ->addTo($to);


            $this->transportBuilder->getTransport()->sendMessage();


            $this->logger->debug('Email add new account sent with success', [
                'paymentMethod' => $requestData['paymentMethod'],
                'storeEmail' => $from['email'],
                'userToken' => $token
            ]);

            $this->messageManager->addSuccess(__("Gateway payment method request sent with success."));

            return $this->resultJsonFactory->create()->setData(['success' => true]);
        } catch (\Throwable $th) {
            $this->logger->debug('Error sending add new account email', [
                'error' => $th,
                'errorMessage' => $th->getMessage(),
                'paymentMethod' => 'ifthenpaygateway',
                'storeEmail' => isset($from['email']) ? $from['email'] : 'undefined',
                'userToken' => $token
            ]);
            return $this->resultJsonFactory->create()->setData(['error' => true]);
        }
    }
}
