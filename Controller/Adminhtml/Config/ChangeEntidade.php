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
use Ifthenpay\Payment\Helper\Data;
use Magento\Backend\App\Action\Context;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Magento\Framework\Controller\Result\JsonFactory;


class ChangeEntidade extends Action
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
            $this->gateway->setAccount($this->helperData->getUserAccount());
            $requestData = $this->getRequest()->getParams();
            $this->logger->debug('Change Entidade: Change Entidade with success');
            return $this->resultJsonFactory->create()->setData([$this->gateway->getSubEntidadeInEntidade($requestData['entidade'])]);
        } catch (\Throwable $th) {
            $this->logger->debug('Change Entidade: Error Changing Entidade - ' . $th->getMessage());
            return $this->resultJsonFactory->create()->setData(['error' => __('changeEntidadeError')]);
        }
    }
}
