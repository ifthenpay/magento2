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

namespace Ifthenpay\Payment\Lib\Strategy\Callback;

use Ifthenpay\Payment\Lib\Callback\CallbackOnline;
use Ifthenpay\Payment\Lib\Callback\CallbackOffline;


class CallbackStrategy
{
    private $callbackOffline;
    private $callbackOnline;

	public function __construct(CallbackOffline $callbackOffline, CallbackOnline $callbackOnline)
	{
        $this->callbackOffline = $callbackOffline;
        $this->callbackOnline = $callbackOnline;
	}

    public function execute(array $request, $callbackController)
    {
        if ($request['type'] === 'offline') {
            return $this->callbackOffline->setCallbackController($callbackController)->setPaymentMethod($request['payment'])->setRequest($request)->process();
        } else {
            return $this->callbackOnline->setCallbackController($callbackController)->setPaymentMethod($request['payment'])->setRequest($request)->process();
        }
    }
}