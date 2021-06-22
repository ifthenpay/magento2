<?php

namespace Ifthenpay\Payment\Api;

use Ifthenpay\Payment\Model\Payshop;

interface PayshopRepositoryInterface
{
    public function save(Payshop $payshop);
    public function getByIdPedido(string $idPedido);
    public function getById(string $payshopId);
}
