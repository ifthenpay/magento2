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
use Ifthenpay\Payment\Lib\HttpClient;

class IfthenpaygatewayService
{
    protected $model;
    protected $repository;
    protected $httpClient;

    public function __construct(
        ModelFactory $modelFactory,
        RepositoryFactory $repositoryFactory,
        HttpClient $httpClient
    ) {
        $this->model = $modelFactory->createModel(ConfigVars::IFTHENPAYGATEWAY);
        $this->repository = $repositoryFactory->createRepository(ConfigVars::IFTHENPAYGATEWAY);
        $this->httpClient = $httpClient;
    }

    public function getById($id)
    {
        return $this->repository->getById($id)->getData();
    }

    public function getByOrderId($orderId)
    {
        return $this->repository->getByOrderId($orderId)->getData();
    }



    public function getByTransactionId($transactionId)
    {
        return $this->repository->getByTransactionId($transactionId)->getData();
    }


    public function getPaymentByRequestData($requestData)
    {
        if (isset($requestData[ConfigVars::CB_ORDER_ID]) && $requestData[ConfigVars::CB_ORDER_ID] != '') {
            return $this->repository->getByOrderId($requestData[ConfigVars::CB_ORDER_ID])->getData();
        }

        return [];
    }

    public function getPaymentTransactionIdByOrderId($orderId)
    {
        return $this->repository->getByOrderId($orderId)->getData()['transaction_id'];
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
