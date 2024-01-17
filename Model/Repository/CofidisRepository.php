<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ifthenpay\Payment\Model\Repository;

use Ifthenpay\Payment\Api\CofidisRepositoryInterface;
use Ifthenpay\Payment\Model\CofidisFactory;
use Ifthenpay\Payment\Model\ResourceModel\Cofidis as CofidisResource;
use Ifthenpay\Payment\Model\Cofidis;

class CofidisRepository implements CofidisRepositoryInterface
{
    private $cofidisFactory;
    private $cofidisResource;

    public function __construct(
        CofidisFactory $cofidisFactory,
        CofidisResource $cofidisResource
    ) {
        $this->cofidisFactory = $cofidisFactory;
        $this->cofidisResource = $cofidisResource;
    }

    public function save(Cofidis $cofidis)
    {
        try {
            $this->cofidisResource->save($cofidis);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getByHash(string $hash)
    {
        $model = $this->cofidisFactory->create();
        $this->cofidisResource->load($model, $hash, 'hash');

        return $model;
    }


    public function getByTransactionId(string $transactionId)
    {
        $model = $this->cofidisFactory->create();
        $this->cofidisResource->load($model, $transactionId, 'transaction_id');

        return $model;
    }

    public function getById(string $id)
    {
        $model = $this->cofidisFactory->create();
        $this->cofidisResource->load($model, $id);

        return $model;
    }

    public function getByOrderId(string $orderId)
    {
        $model = $this->cofidisFactory->create();
        $this->cofidisResource->load($model, $orderId, 'order_id');
        return $model;
    }
}
