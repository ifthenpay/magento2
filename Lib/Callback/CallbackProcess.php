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

declare(strict_types=1);

namespace Ifthenpay\Payment\Lib\Callback;

use Magento\Framework\UrlInterface;
use Magento\Framework\App\Response\Http;
use Magento\Sales\Api\Data\OrderInterface;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Factory\Model\ModelFactory;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Ifthenpay\Payment\Model\Service\CreateInvoiceService;
use Ifthenpay\Payment\Lib\Factory\Model\RepositoryFactory;
use Ifthenpay\Payment\Lib\Factory\Callback\CallbackDataFactory;
use Ifthenpay\Payment\Controller\Frontend\Callback as CallbackController;

class CallbackProcess
{
    protected $paymentMethod;
    protected $paymentData;
    protected $order;
    protected $request;
    protected $orderRepository;
    protected $callbackController;
    protected $modelFactory;
    protected $resultFactory;
    protected $searchCriteriaBuilder;
    protected $urlBuilder;
    protected $createInvoiceService;
    protected $dataFactory;
    protected $logger;
    protected $paymentRepository;
    protected $repositoryFactory;

    public function __construct(
        CallbackDataFactory $callbackDataFactory,
        ModelFactory $modelFactory,
        CreateInvoiceService $createInvoiceService,
        DataFactory $dataFactory,
        RepositoryFactory $repositoryFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepositoryInterface $orderRepository,
        OrderPaymentRepositoryInterface $paymentRepository,
        ResultFactory $resultFactory,
        UrlInterface $urlBuilder,
        IfthenpayLogger $logger
    )
	{
        $this->callbackDataFactory = $callbackDataFactory;
        $this->modelFactory = $modelFactory;
        $this->createInvoiceService = $createInvoiceService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository = $orderRepository;
        $this->paymentRepository = $paymentRepository;
        $this->repositoryFactory = $repositoryFactory;
        $this->resultFactory = $resultFactory;
        $this->urlBuilder = $urlBuilder;
        $this->dataFactory = $dataFactory;
        $this->logger = $logger;
	}

    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    protected function setPaymentData(): void
    {
        $this->paymentData = $this->callbackDataFactory->setType($this->request['payment'])
            ->build()
            ->getData($this->request);

    }

    protected function setOrder(): void
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(OrderInterface::INCREMENT_ID, $this->paymentData['order_id'])->create();
        $data = $this->orderRepository->getList($searchCriteria)->getItems();
        $this->order = $data[array_key_first($data)];
    }

    protected function executePaymentNotFound(): CallbackController
    {
        $this->logger->debug('callback payment not found', ['requestData' => $this->request]);
        return $this->callbackController->getResponse()
        ->setStatusCode(Http::STATUS_CODE_404)
        ->setContent('Pagamento não encontrado');
    }

    protected function changeIfthenpayPaymentStatus(string $status): void
    {
        $paymentRepository = $this->repositoryFactory->setType($this->request['payment'])->build();
        $paymentModel = $paymentRepository->getById($this->paymentData['id']);
        $paymentModel->setStatus($status);
        $paymentRepository->save($paymentModel);
    }

    protected function changeOrderStatus(string $status): void
    {
        $this->order->setState($status)
            ->setStatus($this->order->getConfig()->getStateDefaultStatus($status));
        $this->orderRepository->save($this->order);
    }

    protected function createInvoice(string $invoiceCaptureType): void
    {
        $this->createInvoiceService->createInvoice($this->order, $invoiceCaptureType);
    }

    public function setRequest(array $request)
    {
        $this->request = $request;

        return $this;
    }

    public function setCallbackController($callbackController)
    {
        $this->callbackController = $callbackController;

        return $this;
    }
}
