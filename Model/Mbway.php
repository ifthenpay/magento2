<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);
namespace Ifthenpay\Payment\Model;

use Magento\Framework\Model\AbstractModel;
use Ifthenpay\Payment\Model\ResourceModel\Mbway as MbwayResource;

class Mbway extends AbstractModel
{

    protected function _construct()
    {
        $this->_init(MbwayResource::class);
    }

}
