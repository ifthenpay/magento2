<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ifthenpay\Payment\Model\Repository;

use Ifthenpay\Payment\Api\CcardRepositoryInterface;
use Ifthenpay\Payment\Model\CcardFactory;
use Ifthenpay\Payment\Model\ResourceModel\Ccard as CcardResource;
use Ifthenpay\Payment\Model\Ccard;

class CcardRepository implements CcardRepositoryInterface
{
    private $ccardFactory;
    private $ccardResource;

    public function __construct(
        CcardFactory $ccardFactory,
        CcardResource $ccardResource
    ) {
        $this->ccardFactory = $ccardFactory;
        $this->ccardResource = $ccardResource;
    }

    public function save(Ccard $ccard)
    {
        try {
            $this->ccardResource->save($ccard);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getByTransactionId(string $transactionId)
    {
        $model = $this->ccardFactory->create();
        $this->ccardResource->load($model, $transactionId, 'transaction_id');

        return $model;
    }

    public function getById(string $id)
    {
        $model = $this->ccardFactory->create();
        $this->ccardResource->load($model, $id);

        return $model;
    }

    public function getByOrderId(string $orderId)
    {
        $model = $this->ccardFactory->create();
        $this->ccardResource->load($model, $orderId, 'order_id');
        return $model;
    }

    public function getByRequestId(string $requestId)
    {
        $model = $this->ccardFactory->create();
        $this->ccardResource->load($model, $requestId, 'request_id');
        return $model;
    }



}
