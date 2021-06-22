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

use Ifthenpay\Payment\Lib\Callback\CallbackData;
use Ifthenpay\Payment\Lib\Contracts\Callback\CallbackDataInterface;

class CallbackDataMbway extends CallbackData implements CallbackDataInterface
{
    public function getData(array $request): array
    {
        $paymentRepository = $this->repositoryFactory->setType('mbway')->build();
        $data = $paymentRepository->getByIdPedido($request['id_pedido'])->getData();

        if (empty($data)) {
            return $paymentRepository->getByOrderId($request['referencia'])->getData();
        }
        return $data;
    }
}
