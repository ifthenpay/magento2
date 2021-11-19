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
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Magento\Framework\Controller\Result\JsonFactory;


class ChooseNewEntidadeSubEntidade extends Action
{
    private $resultJsonFactory;
    private $dataFactory;
    private $logger;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        DataFactory $dataFactory,
        IfthenpayLogger $logger
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataFactory = $dataFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $requestData = $this->getRequest()->getParams();
            $this->dataFactory->setType($requestData['paymentMethod'])->build()->deleteConfig();
            $this->logger->debug('Choosing New Entidade/SubEntidade with success', [
                'paymentMethod' => $requestData['paymentMethod'],
                'requestData' => $requestData
            ]);
            return $this->resultJsonFactory->create()->setData(['success' => true]);
        } catch (\Throwable $th) {
            $this->logger->debug('Error Choosing New Entidade/SubEntidade', [
                'error' => $th,
                'errorMessage' => $th->getMessage(),
                'paymentMethod' => $requestData['paymentMethod'],
                'requestData' => $requestData
            ]);
            return $this->resultJsonFactory->create()->setData(['error' => __('changeEntidadeSubEntidade')]);
        }
    }
}
