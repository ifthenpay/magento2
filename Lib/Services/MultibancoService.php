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

class MultibancoService
{
    protected $model;
    protected $repository;

    public function __construct(
        ModelFactory $modelFactory,
        RepositoryFactory $repositoryFactory
    ) {
        $this->model = $modelFactory->createModel(ConfigVars::MULTIBANCO);
        $this->repository = $repositoryFactory->createRepository(ConfigVars::MULTIBANCO);
    }

    public function getById($id)
    {
        return $this->repository->getById($id)->getData();
    }

    public function getByOrderId($orderId)
    {
        return $this->repository->getByOrderId($orderId)->getData();
    }



    public function getByReference($reference)
    {
        return $this->repository->getByReference($reference)->getData();
    }


    public function getPaymentByRequestData($requestData)
    {
        $data = $this->repository->getByReference($requestData[ConfigVars::CB_REFERENCE])->getData();

        if (!empty($data)) {
            return $data;
        }

        return $this->repository->getByOrderId($requestData[ConfigVars::CB_ORDER_ID])->getData();
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
