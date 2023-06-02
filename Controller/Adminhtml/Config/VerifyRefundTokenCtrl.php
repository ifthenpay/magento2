<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Ifthenpay\Payment\Logger\Logger;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;
use Ifthenpay\Payment\Lib\Utility\Cookie;
use Ifthenpay\Payment\Lib\Utility\Token;

class VerifyRefundTokenCtrl extends Action
{
    private $resultJsonFactory;
    private $configData;
    private $logger;
    private $cookie;
    private $token;


    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        IfthenpayConfig $configData,
        Logger $logger,
        Cookie $cookie,
        Token $token
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configData = $configData;
        $this->logger = $logger;
        $this->cookie = $cookie;
        $this->token = $token;
    }

    public function execute()
    {
        $requestData = $this->getRequest()->getParams();

        try {
            $inputtedToken = $requestData['token'];

            $tokenCookie = $this->cookie->getCookie('ifthenpayRefundToken');

            if (!password_verify($inputtedToken, $tokenCookie)) {
                return $this->resultJsonFactory->create()->setData(['isSuccess' => false, 'errorMessage' => __('Refund token is not valid.')]);
            }
        } catch (\Throwable $th) {
            $this->logger->error('Failed to verify refund token.', [
                'error' => $th,
            ]);

            return $this->resultJsonFactory->create()->setData(['isSuccess' => false, 'errorMessage' => __('Failed to verify refund token.')]);
        }

        return $this->resultJsonFactory->create()->setData(['success' => true]);


    }
}
