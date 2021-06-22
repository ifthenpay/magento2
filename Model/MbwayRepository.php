<?php

namespace Ifthenpay\Payment\Model;

use Ifthenpay\Payment\Api\MbwayRepositoryInterface;
use Ifthenpay\Payment\Model\MbwayFactory;
use Ifthenpay\Payment\Model\ResourceModel\Mbway as MbwayResource;
use Ifthenpay\Payment\Model\Mbway;

class MbwayRepository implements MbwayRepositoryInterface
{
    private $mbwayFactory;
    private $mbwayResource;

    public function __construct(
        MbwayFactory $mbwayFactory,
        MbwayResource $mbwayResource
    ) {
        $this->mbwayFactory = $mbwayFactory;
        $this->mbwayResource = $mbwayResource;
    }

    public function save(Mbway $mbway)
    {
        try {
            $this->mbwayResource->save($mbway);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getByIdPedido(string $idPedido)
    {
        $mbwayModel = $this->mbwayFactory->create();
        $this->mbwayResource->load($mbwayModel, $idPedido, 'id_transacao');

        return $mbwayModel;
    }

    public function getById(string $mbwayId)
    {
        $mbwayModel = $this->mbwayFactory->create();
        $this->mbwayResource->load($mbwayModel, $mbwayId);

        return $mbwayModel;
    }

    public function getByOrderId(string $orderId)
    {
        $mbwayModel = $this->mbwayFactory->create();
        $this->mbwayResource->load($mbwayModel, $orderId, 'order_id');

        return $mbwayModel;
    }
}
