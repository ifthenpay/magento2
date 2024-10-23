<?php

/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Ifthenpay\Payment\Lib\Services;

use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Lib\Factory\ModelFactory;
use Ifthenpay\Payment\Lib\Factory\RepositoryFactory;

class MbwayService
{
    protected $model;
    protected $repository;

    public function __construct(
        ModelFactory $modelFactory,
        RepositoryFactory $repositoryFactory
    ) {
        $this->model = $modelFactory->createModel(ConfigVars::MBWAY);
        $this->repository = $repositoryFactory->createRepository(ConfigVars::MBWAY);
    }

    public function getById($id)
    {
        return $this->repository->getById($id)->getData();
    }

    public function getByOrderId($orderId)
    {
        return $this->repository->getByOrderId($orderId)->getData();
    }

    public function setPaymentStatusByTransactionId($transactionId, $status)
    {
        $data = $this->getByTransactionId($transactionId);
        $data['status'] = $status;
        $this->setData($data);
    }

    public function setPaymentTransactionIdByOrderId($orderId, $transactionId)
    {
        $data = $this->getByOrderId($orderId);
        $data['transaction_id'] = $transactionId;
        $this->setData($data);
    }

    public function getByTransactionId($transactionId)
    {
        return $this->repository->getByTransactionId($transactionId)->getData();
    }

    public function getOrderIdByTransactionId($transactionId)
    {
        return $this->repository->getByTransactionId($transactionId)->getData()['order_id'];
    }


    public function getOrderTotalByOrderId($orderId)
    {
        return $this->repository->getByOrderId($orderId)->getData()['order_total'];
    }


    public function getPaymentByRequestData($requestData)
    {
        $data = $this->repository->getByTransactionId($requestData[ConfigVars::CB_TRANSACTION_ID])->getData();

        if (!empty($data)) {
            return $data;
        }

        return $this->repository->getByOrderId($requestData[ConfigVars::CB_ORDER_ID])->getData();
    }

    public function getPaymentTransactionIdByOrderId($orderId)
    {
        return $this->repository->getByOrderId($orderId)->getData()['transaction_id'];
    }

    public function setData($data)
    {
        $this->model->setData($data);
    }

    public function save()
    {
        $this->repository->save($this->model);
    }
}
