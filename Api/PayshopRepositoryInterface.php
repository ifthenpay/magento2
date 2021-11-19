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

use Ifthenpay\Payment\Model\Payshop;
use Ifthenpay\Payment\Api\IfthenpayRepositoryInterface;

interface PayshopRepositoryInterface extends IfthenpayRepositoryInterface
{
    public function save(Payshop $payshop);
    public function getByIdPedido(string $idPedido);
    public function getById(string $payshopId);
}
