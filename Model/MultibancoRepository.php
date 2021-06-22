<?php

namespace Ifthenpay\Payment\Model;

use Ifthenpay\Payment\Api\MultibancoRepositoryInterface;
use Ifthenpay\Payment\Model\MultibancoFactory;
use Ifthenpay\Payment\Model\ResourceModel\Multibanco as MultibancoResource;
use Ifthenpay\Payment\Model\Multibanco;

class MultibancoRepository implements MultibancoRepositoryInterface
{
    private $multibancoFactory;
    private $multibancoResource;

    public function __construct(
        MultibancoFactory $multibancoFactory,
        MultibancoResource $multibancoResource
    ) {
        $this->multibancoFactory = $multibancoFactory;
        $this->multibancoResource = $multibancoResource;
    }

    public function save(Multibanco $mbway)
    {
        try {
            $this->multibancoResource->save($mbway);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getByReferencia(string $referencia)
    {
        $multibancoModel = $this->multibancoFactory->create();
        $this->multibancoResource->load($multibancoModel, $referencia, 'referencia');

        return $multibancoModel;
    }

    public function getById(string $multibancoId)
    {
        $multibancoModel = $this->multibancoFactory->create();
        $this->multibancoResource->load($multibancoModel, $multibancoId);

        return $multibancoModel;
    }
}
