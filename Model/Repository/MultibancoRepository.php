<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ifthenpay\Payment\Model\Repository;

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

    public function save(Multibanco $multibanco)
    {
        try {
            $this->multibancoResource->save($multibanco);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getByReference(string $reference)
    {
        $model = $this->multibancoFactory->create();
        $this->multibancoResource->load($model, $reference, 'reference');

        return $model;
    }

    public function getById(string $id)
    {
        $model = $this->multibancoFactory->create();
        $this->multibancoResource->load($model, $id);

        return $model;
    }

    public function getByOrderId(string $orderId)
    {
        $model = $this->multibancoFactory->create();
        $this->multibancoResource->load($model, $orderId, 'order_id');
        return $model;
    }
}
