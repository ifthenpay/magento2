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
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Ifthenpay\Payment\Logger\Logger;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;
use Ifthenpay\Payment\Lib\Utility\Cookie;
use Ifthenpay\Payment\Lib\Utility\Token;
use Magento\Framework\App\Config\ScopeConfigInterface;

class RequestRefundTokenCtrl extends Action
{
    private $resultJsonFactory;
    private $configData;
    private $storeManager;
    private $logger;
    private $transportBuilder;
    protected $authSession;
    private $cookie;
    private $token;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        IfthenpayConfig $configData,
        StoreManagerInterface $storeManager,
        Logger $logger,
        TransportBuilder $transportBuilder,
        \Magento\Backend\Model\Auth\Session $authSession,
        Cookie $cookie,
        Token $token
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configData = $configData;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->transportBuilder = $transportBuilder;
        $this->authSession = $authSession;
        $this->cookie = $cookie;
        $this->token = $token;
    }

    public function execute()
    {
        try {

            $tokenCookie = $this->cookie->getCookie('ifthenpayRefundToken');

            if ($tokenCookie == null) {
                // generate token
                $token = sprintf('%05d', mt_rand(0, 99999));

                // hash token
                $tokenHash = password_hash($token, PASSWORD_DEFAULT);

                $this->cookie->setCookie('ifthenpayRefundToken', $tokenHash, 30 * 60);


                // send mail with token
                $from = [
                    "name" => 'no-reply',
                    "email" => 'no-reply@mail.com'
                ];

                $toEmail = $this->authSession->getUser()->getEmail();
                $toName = $this->authSession->getUser()->getName();


                $templateVars = [
                    "name" => $toName,
                    "token" => $token,
                    "storeName" => $from['name']
                ];


                $this->transportBuilder
                    ->setTemplateIdentifier('refund_token')
                    ->setTemplateOptions(['area' => 'adminhtml', 'store' => ScopeConfigInterface::SCOPE_TYPE_DEFAULT])
                    ->setTemplateVars($templateVars)
                    ->setFromByScope($from)
                    ->addTo($toEmail);


                $this->transportBuilder->getTransport()->sendMessage();


                $this->logger->info('Email send refund token sent with success', [
                    'storeEmail' => $from['email'],
                    'refundToken' => $token,
                    'toEmail' => $toEmail,
                ]);

            }
            return $this->resultJsonFactory->create()->setData(['isSuccess' => true, 'message' => __('A refund token was sent to your user email account, use it to fill this field and complete the Refund process.')]);

        } catch (\Throwable $th) {
            $this->logger->debug('Error sending refund token email', [
                'error' => $th,
                'errorMessage' => $th->getMessage(),
                'storeEmail' => isset($from['email']) ? $from['email'] : 'undefined',
                'refundToken' => $token
            ]);


            return $this->resultJsonFactory->create()->setData(['isSuccess' => false, 'message' => __('Failed to send refund token.')]);
        }
    }
}
