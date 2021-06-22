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

namespace Ifthenpay\Payment\Gateway\Http\Client;

use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Magento\Framework\Message\ManagerInterface;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Ifthenpay\Payment\Lib\Strategy\Payments\IfthenpayPaymentReturn;


class Client implements ClientInterface
{
    private $logger;
    private $ifthenpayPaymentReturn;
    protected $_messageManager;

    public function __construct(
        IfthenpayPaymentReturn $ifthenpayPaymentReturn,
        ManagerInterface $messageManager,
        IfthenpayLogger $logger,
        ConverterInterface $converter = null
    )
    {
        $this->logger = $logger;
        $this->converter = $converter;
        $this->_messageManager = $messageManager;
        $this->ifthenpayPaymentReturn = $ifthenpayPaymentReturn;
    }

    public function placeRequest(TransferInterface $transferObject)
    {
        try {
            $result = $this->ifthenpayPaymentReturn
                ->setOrder($transferObject->getBody()['order'])
                ->setPayment($transferObject->getBody()['payment'])
                ->execute()->getPaymentGatewayResultData();
            $this->logger->debug('CCard Payment: CCard Payment return executed with success');
            return $result;
        } catch (\Exception $th) {
            $this->logger->debug('CCard Payment: Error executing CCard Payment return - ' . $th->getMessage());
            throw $th;
        }
    }
}
