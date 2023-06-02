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
use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;
use Ifthenpay\Payment\Gateway\Config\MultibancoConfig;





class GetSubEntitiesCtrl extends Action
{
    private $multibancoConfig;
    private $resultJsonFactory;
    private $configData;
    private $logger;

    protected $configFactory;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Logger $logger,
        IfthenpayConfig $configData,
        MultibancoConfig $multibancoConfig
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->configData = $configData;
        $this->multibancoConfig = $multibancoConfig;
    }

    public function execute()
    {
        try {
            $requestData = $this->getRequest()->getParams();

            $this->configData->setScopeAndScopeCode($requestData['scope'], $requestData['scopeCode']);
            $this->multibancoConfig->setScopeAndScopeCode($requestData['scope'], $requestData['scopeCode']);


            $entity = $this->getRequest()->getParam('entity');
            $subEntities = $this->configData->getSubEntities(ConfigVars::MULTIBANCO, $entity);


            $thisEntity = $this->multibancoConfig->getEntity();
            $thisSubEntity = $this->multibancoConfig->getSubEntity();


            // remove subentities in use by other stores (in case of multistore)
            $otherEntitySubEntitiesInUse = $this->multibancoConfig->getOtherEntitiesSubEntityPairsInUse($thisEntity, $thisSubEntity);

            foreach ($otherEntitySubEntitiesInUse as $EntitySubEntityPair) {

                if ($EntitySubEntityPair['entity'] === $entity) {
                    foreach ($subEntities as $key => $item) {
                        if ($item === $EntitySubEntityPair['subEntity']) {
                            unset($subEntities[$key]);
                        }
                    }
                }

            }


            return $this->resultJsonFactory->create()->setData(['success' => true, 'subEntities' => $subEntities]);
        } catch (\Throwable $th) {
            $this->logger->error('Failed to get corresponding Sub Entities.', [
                'error' => $th,
            ]);

            return $this->resultJsonFactory->create()->setData(['error' => true, 'errorMessage' => __('Failed to get corresponding Sub Entities.')]);
        }
    }
}
