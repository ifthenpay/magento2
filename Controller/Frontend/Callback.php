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

namespace Ifthenpay\Payment\Controller\Frontend;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Ifthenpay\Payment\Lib\Strategy\Callback\CallbackStrategy;

class Callback extends Action
{
    protected $callbackStrategy;
    private $logger;

    public function __construct(
        Context $context,
        CallbackStrategy $callbackStrategy,
        IfthenpayLogger $logger
    )
	{
        parent::__construct($context);
        $this->logger = $logger;
        $this->callbackStrategy = $callbackStrategy;
	}



    public function execute()
    {
        try {
            $this->logger->debug('Callback: Callback executed with success');
            return $this->callbackStrategy->execute($this->getRequest()->getParams(), $this);
        } catch (\Throwable $th) {
            $this->logger->debug('Callback: Error Executing callback - ' . $th->getMessage());
            throw $th;
        }
    }
}