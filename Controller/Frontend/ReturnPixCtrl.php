<?php

/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Controller\Frontend;

use Ifthenpay\Payment\Config\ConfigVars;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use Magento\Sales\Model\Order;
use Ifthenpay\Payment\Lib\Services\PixService;
use Magento\Sales\Api\OrderRepositoryInterface;
use Ifthenpay\Payment\Logger\Logger;
use Magento\Framework\Controller\ResultFactory;


class ReturnPixCtrl extends Action
{

    protected $_orderFactory;
    protected $_order;
    protected $_builderInterface;
    protected $pixService;
    protected $_orderRepository;
    private $logger;


    /**
     * Ipn constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Reader $reader
     * @param ScopeConfigInterface $scopeConfig
     * @param BuilderInterface $builderInterface
     * @param Order $orderFactory
     */

    public function __construct(
        Context $context,
        BuilderInterface $builderInterface,
        Order $orderFactory,
        OrderRepositoryInterface $orderRepository,
        Logger $logger,
        PixService $pixService,
    ) {
        parent::__construct($context);
        $this->_orderFactory = $orderFactory;
        $this->_builderInterface = $builderInterface;
        $this->_orderRepository = $orderRepository;
        $this->logger = $logger;
        $this->pixService = $pixService;
    }



    public function execute()
    {

        // the return controler does only set the order as pending. payment verification is done through callback
        try {
            $requestData = $this->getRequest()->getParams();
            $storedPaymentData = $this->pixService->getPaymentByRequestData($requestData);

            $orderId = $storedPaymentData['order_id'];
            $this->_order = $this->_orderFactory->loadByIncrementId($orderId);

            if ($storedPaymentData['status'] === ConfigVars::DB_STATUS_PENDING) {

                    $this->messageManager->addSuccessMessage(__('Payment concluded with success, awaiting verification.'));
            }
        } catch (\Throwable $th) {
            $this->logger->error('Error ifthenpay return ctrl: ', [
                'error' => $th->getMessage(),
            ]);
            $this->messageManager->addErrorMessage(__('An error ocurred while redirecting to store, please contact the store admin.'));
        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('checkout/onepage/success');
    }
}
