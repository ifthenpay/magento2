<?php

namespace Ifthenpay\Payment\Api;

use Ifthenpay\Payment\Model\Mbway;

interface MbwayRepositoryInterface
{
    public function save(Mbway $mbway);
    public function getByIdPedido(string $idPedido);
    public function getById(string $mbwayId);
    public function getByOrderId(string $orderId);
}
