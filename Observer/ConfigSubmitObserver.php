<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Ifthenpay\Payment\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;

use Ifthenpay\Payment\Logger\Logger;
use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Lib\Services\CallbackService;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;



class ConfigSubmitObserver implements ObserverInterface
{
    /**
     * @var Logger
     */
    protected $logger;
    protected $callbackService;
    protected $configData;
    protected $configFactory;


    /**
     * @param Logger $logger
     */
    public function __construct(
        IfthenpayConfig $configData,
        CallbackService $callbackService,
        Logger $logger
    ) {
        $this->configData = $configData;
        $this->callbackService = $callbackService;
        $this->logger = $logger;
    }

    /**
     * activate payment method callbacks if backoffice key is set and sandbox mode is set to false and if payment method is not already active
     * this is a general entry point, since it does not make distinction between payment methods, that is done inside the activateCallback() function
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        try {
            $backofficeKey = $this->configData->getBackofficeKey();

            // if backoffice key is set , get accounts and payment methods from ifthenpay and activate the callback if its not already active
            if ($backofficeKey !== '') {
                $this->callbackService->toggleCallback();
            }

        } catch (\Throwable $th) {
            $this->logger->error('Error activating the callback', [
                'error' => $th,
            ]);

            throw new LocalizedException(__($th->getMessage()));
        }
    }
}
