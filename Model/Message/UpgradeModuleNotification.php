<?php

namespace Ifthenpay\Payment\Model\Message;

use Ifthenpay\Payment\Lib\Request\WebService;
use \Magento\Framework\Notification\MessageInterface;
use \Magento\Framework\Module\ModuleListInterface;

class UpgradeModuleNotification implements MessageInterface
{
	private $webService;
    private $moduleList;
    private $moduleVersion;

    const GITHUB_REALEASE_PATH = 'https://github.com/ifthenpay/magento2/releases/tag/';

    public function __construct(WebService $webService, ModuleListInterface $moduleList)
	{
        $this->webService = $webService;
        $this->moduleList = $moduleList;
	}

    private function getModuleVersion(): string
    {
        return $this->moduleList->getOne('Ifthenpay_Payment')['setup_version'];
    }


   public function getIdentity()
   {
       // Retrieve unique message identity
       return 'ifthenpay_module_upgrade';
   }

   public function isDisplayed()
   {
        $response = $this->webService->getRequest('https://ifthenpay.com/modulesUpgrade/magento/upgrade.json')->getResponseJson();
        if (version_compare(str_replace('v', '', $response['version']), $this->getModuleVersion(), '>')) {
            $this->moduleVersion = $response['version'];
            return true;
        }
        return false;

   }

   public function getText()
   {
        $message = __('Ifthenpay Module Version %1 is available. Please Update Ifthenpay Module.',  $this->moduleVersion) . ' ';
        $message .= __('Go to <a href="%1" target="_blank">Github Realease</a> to review.', self::GITHUB_REALEASE_PATH . $this->moduleVersion);
        return $message;
   }

   public function getSeverity()
   {
       // Possible values:
       // SEVERITY_CRITICAL
       // SEVERITY_MAJOR
       // SEVERITY_MINOR
       // SEVERITY_NOTICE
       return self::SEVERITY_NOTICE;
   }
}
