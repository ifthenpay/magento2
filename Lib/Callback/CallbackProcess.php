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
use Ifthenpay\Payment\Lib\Utility\Token;
use \Magento\Framework\App\Response\Http;
use Ifthenpay\Payment\Lib\Utility\Status;
use Magento\Sales\Api\Data\OrderInterface;
use Ifthenpay\Payment\Lib\Utility\TokenExtra;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Ifthenpay\Payment\Lib\Callback\CallbackValidate;
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
    protected $tokenExtra;
    protected $dataFactory;
    protected $logger;
    protected $paymentRepository;
    protected $repositoryFactory;

    public function __construct(
        CallbackDataFactory $callbackDataFactory,
        CallbackValidate $callbackValidate,
        Status $status,
        Token $token,
        TokenExtra $tokenExtra,
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
        $this->callbackValidate = $callbackValidate;
        $this->status = $status;
        $this->token = $token;
        $this->tokenExtra = $tokenExtra;
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
