<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ifthenpay\Payment\Model\Repository;

use Ifthenpay\Payment\Api\PayshopRepositoryInterface;
use Ifthenpay\Payment\Model\PayshopFactory;
use Ifthenpay\Payment\Model\ResourceModel\Payshop as PayshopResource;
use Ifthenpay\Payment\Model\Payshop;

class PayshopRepository implements PayshopRepositoryInterface
{
    private $payshopFactory;
    private $payshopResource;

    public function __construct(
        PayshopFactory $payshopFactory,
        PayshopResource $payshopResource
    ) {
        $this->payshopFactory = $payshopFactory;
        $this->payshopResource = $payshopResource;
    }

    public function save(Payshop $payshop)
    {
        try {
            $this->payshopResource->save($payshop);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getByReference(string $reference)
    {
        $model = $this->payshopFactory->create();
        $this->payshopResource->load($model, $reference, 'reference');

        return $model;
    }

    public function getById(string $id)
    {
        $model = $this->payshopFactory->create();
        $this->payshopResource->load($model, $id);

        return $model;
    }

    public function getByOrderId(string $orderId)
    {
        $model = $this->payshopFactory->create();
        $this->payshopResource->load($model, $orderId, 'order_id');
        return $model;
    }
}
