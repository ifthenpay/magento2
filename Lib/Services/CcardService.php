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

class CcardService
{
    protected $model;
    protected $repository;

    public function __construct(
        ModelFactory $modelFactory,
        RepositoryFactory $repositoryFactory
    ) {
        $this->model = $modelFactory->createModel(ConfigVars::CCARD);
        $this->repository = $repositoryFactory->createRepository(ConfigVars::CCARD);
    }

    public function getById($id)
    {
        return $this->repository->getById($id)->getData();
    }

    public function getByOrderId($orderId)
    {
        return $this->repository->getByOrderId($orderId)->getData();
    }



    public function getByRequestId($reference)
    {
        return $this->repository->getByRequestId($reference)->getData();
    }


    public function getPaymentByRequestData($requestData)
    {
        $data = $this->repository->getByRequestId($requestData[ConfigVars::CB_TRANSACTION_ID])->getData();

        if (!empty($data)) {
            return $data;
        }

        return $this->repository->getByOrderId($requestData[ConfigVars::CB_ORDER_ID])->getData();
    }

    public function getPaymentTransactionIdByOrderId($orderId)
    {
        return $this->repository->getByOrderId($orderId)->getData()['request_id'];
    }

    public function setData($data)
    {
        foreach ($data as $key => $value) {
            $this->model->setData($key, $value);
        }
        return $this;
    }

    public function setKeyValue($key, $value)
    {
        $this->model->setData($key, $value);
        return $this;
    }


    public function save()
    {
        $this->repository->save($this->model);
    }
}
