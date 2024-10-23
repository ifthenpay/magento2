<?php

/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Model\Repository;

use Ifthenpay\Payment\Api\IfthenpaygatewayRepositoryInterface;
use Ifthenpay\Payment\Model\IfthenpaygatewayFactory;
use Ifthenpay\Payment\Model\ResourceModel\Ifthenpaygateway as IfthenpaygatewayResource;
use Ifthenpay\Payment\Model\Ifthenpaygateway;

class IfthenpaygatewayRepository implements IfthenpaygatewayRepositoryInterface
{
    private $ifthenpaygatewayFactory;
    private $ifthenpaygatewayResource;

    public function __construct(
        IfthenpaygatewayFactory $ifthenpaygatewayFactory,
        IfthenpaygatewayResource $ifthenpaygatewayResource
    ) {
        $this->ifthenpaygatewayFactory = $ifthenpaygatewayFactory;
        $this->ifthenpaygatewayResource = $ifthenpaygatewayResource;
    }

    public function save(Ifthenpaygateway $ifthenpaygateway)
    {
        try {
            $this->ifthenpaygatewayResource->save($ifthenpaygateway);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getByHash(string $hash)
    {
        $model = $this->ifthenpaygatewayFactory->create();
        $this->ifthenpaygatewayResource->load($model, $hash, 'hash');

        return $model;
    }

    public function getByTransactionId(string $transactionId)
    {
        $model = $this->ifthenpaygatewayFactory->create();
        $this->ifthenpaygatewayResource->load($model, $transactionId, 'transaction_id');

        return $model;
    }

    public function getById(string $id)
    {
        $model = $this->ifthenpaygatewayFactory->create();
        $this->ifthenpaygatewayResource->load($model, $id);

        return $model;
    }

    public function getByOrderId(string $orderId)
    {
        $model = $this->ifthenpaygatewayFactory->create();
        $this->ifthenpaygatewayResource->load($model, $orderId, 'order_id');
        return $model;
    }

    public function getByReference(string $reference)
    {
        //
    }
}
