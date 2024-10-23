<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Api;

use Ifthenpay\Payment\Model\Ifthenpaygateway;

interface IfthenpaygatewayRepositoryInterface
{

    public function save(Ifthenpaygateway $ifthenpaygateway);
    public function getByReference(string $reference);
    public function getById(string $id);
    public function getByOrderId(string $orderId);
}
