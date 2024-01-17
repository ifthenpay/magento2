<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ifthenpay\Payment\Model\Repository;

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

    public function getByTransactionId(string $transactionId)
    {
        $model = $this->mbwayFactory->create();
        $this->mbwayResource->load($model, $transactionId, 'transaction_id');

        return $model;
    }

    public function getById(string $id)
    {
        $model = $this->mbwayFactory->create();
        $this->mbwayResource->load($model, $id);

        return $model;
    }

    public function getByOrderId(string $orderId)
    {
        $model = $this->mbwayFactory->create();
        $this->mbwayResource->load($model, $orderId, 'order_id');
        return $model;
    }
}
