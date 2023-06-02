<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Ifthenpay\Payment\Model\ResourceModel\Mbway;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Ifthenpay\Payment\Model\Mbway;
use Ifthenpay\Payment\Model\ResourceModel\Mbway as MbwayResource;


class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Mbway::class, MbwayResource::class);
    }
}
