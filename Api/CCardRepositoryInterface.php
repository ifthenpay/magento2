<?php

namespace Ifthenpay\Payment\Api;

use Ifthenpay\Payment\Model\CCard;

interface CCardRepositoryInterface
{
    public function save(CCard $ccard);
    public function getByRequestId(string $idPedido);
    public function getById(string $ccardId);
}
