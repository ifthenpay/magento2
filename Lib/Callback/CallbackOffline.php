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

use Ifthenpay\Payment\Lib\Callback\CallbackProcess;
use Ifthenpay\Payment\Lib\Contracts\Callback\CallbackProcessInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Framework\App\Response\Http;
use Ifthenpay\Payment\Lib\Callback\CallbackValidate;
use Magento\Framework\UrlInterface;
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


class CallbackOffline extends CallbackProcess implements CallbackProcessInterface
{
    private $callbackValidate;

	public function __construct(
        CallbackDataFactory $callbackDataFactory,
        CallbackValidate $callbackValidate,
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
        parent::__construct(
            $callbackDataFactory,
            $modelFactory,
            $createInvoiceService,
            $dataFactory,
            $repositoryFactory,
            $searchCriteriaBuilder,
            $orderRepository,
            $paymentRepository,
            $resultFactory,
            $urlBuilder,
            $logger
        );
        $this->callbackValidate = $callbackValidate;
	}


    public function process()
    {
        try {
            $this->setPaymentData();

            if (empty($this->paymentData)) {
                $this->executePaymentNotFound();
            } else {
                    $this->setOrder();
                    $orderData = $this->order->getData();
                    $antiPhishingKey = $this->dataFactory->setType($this->request['payment'])->build()->getConfig()['chaveAntiPhishing'];
                    $this->callbackValidate->setHttpRequest($this->request)
                    ->setOrder($orderData)
                    ->setConfigurationChaveAntiPhishing($antiPhishingKey)
                    ->setPaymentDataFromDb($this->paymentData)
                    ->validate();
                    $this->changeIfthenpayPaymentStatus('paid');
                    $this->changeOrderStatus(Order::STATE_PROCESSING);
                    $this->createInvoice(Invoice::CAPTURE_OFFLINE);
                    $this->logger->debug('Callback offline executed with success', [
                        'requestData' => $this->request,
                        'order' => $this->order->getData(),
                        'paymentData' => $this->paymentData,
                        'antiPhishingKey' => $antiPhishingKey
                    ]);
                    return $this->callbackController->getResponse()
                    ->setStatusCode(Http::STATUS_CODE_200)
                    ->setContent('ok');

            }
        } catch (\Throwable $th) {
            $this->logger->debug('Error executing callback offline', [
                'error' => $th,
                'errorMessage' => $th->getMessage(),
                'requestData' => $this->request
            ]);
            return $this->callbackController->getResponse()
                ->setStatusCode(Http::STATUS_CODE_400)
                ->setContent($th->getMessage());
        }
    }
}
