<?php
/**
* Ifthenpay_Payment module dependency
*
* @category    Gateway Payment
* @package     Ifthenpay_Payment
* @author      Ifthenpay
* @copyright   Ifthenpay (http://www.ifthenpay.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

namespace Ifthenpay\Payment\Api;

use Ifthenpay\Payment\Model\CCard;
use Ifthenpay\Payment\Api\IfthenpayRepositoryInterface;

interface CCardRepositoryInterface extends IfthenpayRepositoryInterface
{
    public function save(CCard $ccard);
    public function getByRequestId(string $idPedido);
    public function getById(string $ccardId);
    public function getByOrderId(string $orderId);
}
