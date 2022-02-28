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
        IfthenpayLogger $logger
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataFactory = $dataFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $requestData = $this->getRequest()->getParams();
            $configData = $this->dataFactory->setType($requestData['paymentMethod'])->build();
            $storeEmail = $configData->getStoreEmail();
            $storeName = $configData->getStorename();
            $userToken = $configData->saveUpdateUserAccountToken();

            $msg = "Associar conta " . $requestData['paymentMethod'] . " ao contrato \n\n";
            $msg .= "backofficeKey: " . $configData->getBackofficeKey() .  "\n\n";
            $msg .= "Email Cliente: " .  $storeEmail . "\n\n";
            $msg .= "Update User Account: " .  $this->storeManager->getStore()->getBaseUrl() . 'ifthenpay/Frontend/UpdateUserAccount?updateUserToken=' . $userToken . '&paymentMethod=' . $requestData['paymentMethod'] . "\n\n";
            $msg .= "Pedido enviado automaticamente pelo sistema Magento da loja [" . $storeName . "]";

            $from = $storeEmail;
            $nameFrom = $storeName;
            $to = "suporte@ifthenpay.com";
            $nameTo = "Ifthenpay";

            $email = new \Zend_Mail();
            $email->setSubject('Adicionar conta ' . $requestData['paymentMethod'] . ' ao contracto.');
            $email->setBodyText($msg);
            $email->setFrom($from, $nameFrom);
            $email->addTo($to, $nameTo);
            $email->send();
            $this->logger->debug('Email add new account sent with success', [
                'paymentMethod' => $requestData['paymentMethod'],
                'storeEmail' => $storeEmail,
                'userToken' => $userToken
            ]);
            return $this->resultJsonFactory->create()->setData(['success' => true]);
        } catch (\Throwable $th) {
            $this->logger->debug('Error sending add new account email',[
                'error' => $th,
                'errorMessage' => $th->getMessage(),
                'paymentMethod' => $requestData['paymentMethod'],
                'storeEmail' => $storeEmail,
                'userToken' => $userToken
            ]);
            return $this->resultJsonFactory->create()->setData(['error' => true]);
        }
    }
}