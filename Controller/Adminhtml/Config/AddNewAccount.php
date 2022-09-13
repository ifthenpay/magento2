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
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Magento\Store\Model\StoreManagerInterface;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Mail\Template\TransportBuilder;


class AddNewAccount extends Action
{
    private $resultJsonFactory;
    private $dataFactory;
    private $storeManager;
    private $logger;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        DataFactory $dataFactory,
        StoreManagerInterface $storeManager,
        IfthenpayLogger $logger,
        TransportBuilder $transportBuilder
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataFactory = $dataFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->transportBuilder = $transportBuilder;
    }

    public function execute()
    {
        try {
            $requestData = $this->getRequest()->getParams();
            $configData = $this->dataFactory->setType($requestData['paymentMethod'])->build()->setScope($requestData['scope_id'] ?? '');
            $userToken = $configData->saveUpdateUserAccountToken();
            $from = [
                "name" => $configData->getStorename(),
                "email" => $configData->getStoreEmail()
            ];
            $to = "suporte@ifthenpay.com";


            $templateVars = [
                "backofficeKey" => $configData->getBackofficeKey(),
                "customerEmail" => $from['email'],
                "paymentMethod" => ucFirst($requestData['paymentMethod']),
                "ecommercePlatform" => "Magento 2",
                "updateUserAccountUrl" => $this->storeManager->getStore()->getBaseUrl() . 'ifthenpay/Frontend/UpdateUserAccount?updateUserToken=' . $userToken . '&paymentMethod=' . $requestData['paymentMethod'],
                "storeName" => $from['name']
            ];


            $this->transportBuilder
                ->setTemplateIdentifier('add_new_account')
                ->setTemplateOptions(['area' => 'frontend', 'store' => 0])
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($to);


            $this->transportBuilder->getTransport()->sendMessage();


            $this->logger->debug('Email add new account sent with success', [
                'paymentMethod' => $requestData['paymentMethod'],
                'storeEmail' => "test",
                'userToken' => $userToken
            ]);

            return $this->resultJsonFactory->create()->setData(['success' => true]);

        } catch (\Throwable $th) {
            $this->logger->debug('Error sending add new account email', [
                'error' => $th,
                'errorMessage' => $th->getMessage(),
                'paymentMethod' => isset($requestData['paymentMethod']) ? $requestData['paymentMethod'] : 'undefined',
                'storeEmail' => isset($from['email']) ? $from['email'] : 'undefined',
                'userToken' => $userToken
            ]);
            return $this->resultJsonFactory->create()->setData(['error' => true]);
        }
    }
}
