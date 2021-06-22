<?php

namespace Ifthenpay\Payment\Api;

use Ifthenpay\Payment\Model\Multibanco;

interface MultibancoRepositoryInterface
{

    public function save(Multibanco $multibanco);
    public function getByReferencia(string $referencia);
    public function getById(string $multibancoId);
}
