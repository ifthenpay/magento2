<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ifthenpay\Payment\Model\Repository;

use Ifthenpay\Payment\Api\PixRepositoryInterface;
use Ifthenpay\Payment\Model\PixFactory;
use Ifthenpay\Payment\Model\ResourceModel\Pix as PixResource;
use Ifthenpay\Payment\Model\Pix;

class PixRepository implements PixRepositoryInterface
{
    private $pixFactory;
    private $pixResource;

    public function __construct(
        PixFactory $pixFactory,
        PixResource $pixResource
    ) {
        $this->pixFactory = $pixFactory;
        $this->pixResource = $pixResource;
    }

    public function save(Pix $pix)
    {
        try {
            $this->pixResource->save($pix);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getByHash(string $hash)
    {
        $model = $this->pixFactory->create();
        $this->pixResource->load($model, $hash, 'hash');

        return $model;
    }


    public function getByTransactionId(string $transactionId)
    {
        $model = $this->pixFactory->create();
        $this->pixResource->load($model, $transactionId, 'transaction_id');

        return $model;
    }

    public function getById(string $id)
    {
        $model = $this->pixFactory->create();
        $this->pixResource->load($model, $id);

        return $model;
    }

    public function getByOrderId(string $orderId)
    {
        $model = $this->pixFactory->create();
        $this->pixResource->load($model, $orderId, 'order_id');
        return $model;
    }
}
