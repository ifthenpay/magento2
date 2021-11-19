<?php

namespace Ifthenpay\Payment\Model;

use Ifthenpay\Payment\Api\CCardRepositoryInterface;
use Ifthenpay\Payment\Model\CCardFactory;
use Ifthenpay\Payment\Model\ResourceModel\CCard as CCardResource;
use Ifthenpay\Payment\Model\CCard;

class CCardRepository implements CCardRepositoryInterface
{
    private $ccardFactory;
    private $ccardResource;

    public function __construct(
        CCardFactory $ccardFactory,
        CCardResource $ccardResource
    ) {
        $this->ccardFactory = $ccardFactory;
        $this->ccardResource = $ccardResource;
    }

    public function save(CCard $ccard)
    {
        try {
            $this->ccardResource->save($ccard);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getByRequestId(string $idPedido)
    {
        $ccardModel = $this->ccardFactory->create();
        $this->ccardResource->load($ccardModel, $idPedido, 'requestId');

        return $ccardModel;
    }

    public function getById(string $ccardId)
    {
        $ccardModel = $this->ccardFactory->create();
        $this->ccardResource->load($ccardModel, $ccardId);

        return $ccardModel;
    }

    public function getByOrderId(string $orderId)
    {
        $ccardModel = $this->ccardFactory->create();
        $this->ccardResource->load($ccardModel, $orderId, 'order_id');

        return $ccardModel;
    }
}
