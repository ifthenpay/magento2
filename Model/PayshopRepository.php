<?php

namespace Ifthenpay\Payment\Model;

use Ifthenpay\Payment\Api\PayshopRepositoryInterface;
use Ifthenpay\Payment\Model\PayshopFactory;
use Ifthenpay\Payment\Model\ResourceModel\Payshop as PayshopResource;
use Ifthenpay\Payment\Model\Payshop;

class PayshopRepository implements PayshopRepositoryInterface
{
    private $payshopFactory;
    private $payshopResource;

    public function __construct(
        PayshopFactory $payshopFactory,
        PayshopResource $payshopResource
    ) {
        $this->payshopFactory = $payshopFactory;
        $this->payshopResource = $payshopResource;
    }

    public function save(Payshop $payshop)
    {
        try {
            $this->payshopResource->save($payshop);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getByIdPedido(string $idPedido)
    {
        $payshopModel = $this->payshopFactory->create();
        $this->payshopResource->load($payshopModel, $idPedido, 'id_transacao');

        return $payshopModel;
    }

    public function getById(string $payshopId)
    {
        $payshopModel = $this->payshopFactory->create();
        $this->payshopResource->load($payshopModel, $payshopId);

        return $payshopModel;
    }

    public function getByOrderId(string $orderId)
    {
        $payshopModel = $this->payshopFactory->create();
        $this->payshopResource->load($payshopModel, $orderId, 'order_id');
        return $payshopModel;
    }
}
