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
    protected $messageManager;

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
            $requestData = $this->getRequest()->getParams();
            return $this->callbackStrategy->execute($requestData, $this);
        } catch (\Throwable $th) {
            $this->logger->debug('Error Executing callback', [
                'error' => $th,
                'errorMessage' => $th->getMessage(),
                'requestData' => $requestData
            ]);
            throw $th;
        }
    }
}
