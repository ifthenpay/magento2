<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Cron;

use Ifthenpay\Payment\Logger\Logger;
use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Lib\Factory\ServiceFactory;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;



class CancelUnpaidOrders
{

    private $logger;
    private $configFactory;
    private $serviceFactory;
    private $orderCollectionFactory;



    public function __construct(
        Logger $logger,
        ServiceFactory $serviceFactory,
        CollectionFactory $orderCollectionFactory
    ) {
        $this->logger = $logger;
        $this->serviceFactory = $serviceFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    public function execute(): void
    {
        try {

            $this->logger->info('Running Cronjob of cancel Ifthenpay order');

            foreach (ConfigVars::PAYMENT_METHOD_CODES as $paymentMethod) {

                $this->logger->info('Starting cron cancel Ifthenpay ' . $paymentMethod . ' order');
                $this->cancelOrderIfPaymentOverdue($paymentMethod);
                $this->logger->info('Finished Cron cancel Ifthenpay ' . $paymentMethod . ' order');
            }
            $this->logger->info('Finished Cronjob of cancel Ifthenpay order');
        } catch (\Throwable $th) {
            $this->logger->error('Error executing Cronjob cancel Ifthenpay order', [
                'error' => $th,
                'errorMessage' => $th->getMessage()
            ]);
            throw $th;
        }
    }

    /**
     * iterates through orders awaiting payment and cancels them if payment is overdue
     * @param string $paymentMethod
     * @return void
     */
    private function cancelOrderIfPaymentOverdue($paymentMethod): void
    {
        $service = $this->serviceFactory->createService($paymentMethod);
        $ordersAwaitingPayment = $this->getOrderCollectionAwaitingPayment($paymentMethod);

        foreach ($ordersAwaitingPayment as $order) {


            $orderId = $order->getIncrementId();
            $orderStoredData = $service->getByOrderId($orderId);

            $deadline = $this->getDeadlineByPaymentMethod($order, $orderStoredData, $paymentMethod);

            if ($deadline != '' && strtotime($deadline) < strtotime(date('Y-m-d H:i:s'))) {

                $order->registerCancellation(__('Order canceled by cronjob because payment was overdue'));
                $order->save();


                // update ifthenpay table data to canceled
                $orderStoredData['status'] = 'canceled';
                $service->setData($orderStoredData);
                $service->save();


                $this->logger->info('Order canceled by cronjob because payment was overdue', [
                    'orderId' => $orderId,
                    'paymentMethod' => $paymentMethod
                ]);
            }
        }
    }

    /**
     * generates deadline based on payment method
     * - multibanco deadline is always the date in $orderStoredData['deadline'] at 23:59
     * - payshop deadline is always the date in $orderStoredData['deadline'] at 00:00
     * - mbway deadline is always the order creation date + 30 minutes
     * - ccard deadline is always the order creation date + 30 minutes
     * defaults to empty string
     * @param array $orderStoredData
     * @param string $paymentMethod
     * @return string $deadline
     */
    private function getDeadlineByPaymentMethod($order, $orderStoredData, $paymentMethod): string
    {
        if ($paymentMethod === ConfigVars::MULTIBANCO_CODE) {

            $deadline = $orderStoredData['deadline'] ?? '';
            if ($deadline === '') {
                return '';
            }

            $deadline = \DateTime::createFromFormat('d-m-Y', $deadline);
            $deadline->setTime(ConfigVars::MULTIBANCO_DEADLINE_HOURS, ConfigVars::MULTIBANCO_DEADLINE_MINUTES);

            return $deadline->format('Y-m-d H:i:s');
        }

        if ($paymentMethod === ConfigVars::PAYSHOP_CODE) {

            $deadline = $orderStoredData['deadline'] ?? '';
            if ($deadline === '') {
                return '';
            }

            $deadline = \DateTime::createFromFormat('d-m-Y', $deadline);
            $deadline->setTime(ConfigVars::PAYSHOP_DEADLINE_HOURS, ConfigVars::PAYSHOP_DEADLINE_MINUTES);

            return $deadline->format('Y-m-d H:i:s');
        }

        if ($paymentMethod === ConfigVars::MBWAY_CODE) {

            $deadline = $order->getCreatedAt(); // date of order creation

            $deadline = \DateTime::createFromFormat('Y-m-d H:i:s', $deadline);
            $deadline->add(new \DateInterval('PT' . ConfigVars::MBWAY_DEADLINE_MINUTES . 'M'));

            return $deadline->format('Y-m-d H:i:s');
        }

        if ($paymentMethod === ConfigVars::CCARD_CODE) {

            $deadline = $order->getCreatedAt(); // date of order creation

            $deadline = \DateTime::createFromFormat('Y-m-d H:i:s', $deadline);
            $deadline->add(new \DateInterval('PT' . ConfigVars::CCARD_DEADLINE_MINUTES . 'M'));

            return $deadline->format('Y-m-d H:i:s');
        }
        return '';
    }


    /**
     * gets collection of orders awaiting payment
     * - credit card orders are in status 'payment_review'
     * - other payment methods are in status 'pending'
     * @param string $paymentMethod
     * @return $collection
     */
    private function getOrderCollectionAwaitingPayment($paymentMethod)
    {
        // makes distinction between credit card and other payment methods
        $status = $paymentMethod === ConfigVars::CCARD_CODE ? 'payment_review' : 'pending';

        $collection = $this->orderCollectionFactory->create()->addFieldToFilter('status', $status);
        $collection->getSelect()->join(
            ["sop" => "sales_order_payment"],
            'main_table.entity_id = sop.parent_id',
            array('method')
        )->where('sop.method = ?', $paymentMethod);

        return $collection;
    }


}
