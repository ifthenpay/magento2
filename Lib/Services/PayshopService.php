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

class PayshopService
{
    protected $model;
    protected $repository;

    public function __construct(
        ModelFactory $modelFactory,
        RepositoryFactory $repositoryFactory
    ) {
        $this->model = $modelFactory->createModel(ConfigVars::PAYSHOP);
        $this->repository = $repositoryFactory->createRepository(ConfigVars::PAYSHOP);
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
        if (!$requestData['reference']) {
            return [];
        }
        return $this->repository->getByReference($requestData['reference'])->getData();
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
