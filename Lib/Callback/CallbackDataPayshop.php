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

declare(strict_types=1);

namespace Ifthenpay\Payment\Lib\Callback;

use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Lib\Callback\CallbackData;
use Ifthenpay\Payment\Lib\Contracts\Callback\CallbackDataInterface;

class CallbackDataPayshop extends CallbackData implements CallbackDataInterface
{
    public function getData(array $request): array
    {
        return $this->repositoryFactory->setType(Gateway::PAYSHOP)->build()->getByIdPedido($request['id_transacao'])->getData();
    }
}
