<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Model;

use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;




class ScopeConfigResolver
{


    /**
     * @var RequestHttp
     */
    protected $request;
    public $storeManager;
    public $scope;
    public $scopeCode;
    public $storeId;



    /**
     * StoreConfigResolver constructor.
     *
     * @param RequestHttp           $request         HTTP request
     */
    public function __construct(
        RequestHttp $request,
        StoreManagerInterface $storeManager,
    ) {
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->storeId = $this->storeManager->getStore()->getId();
        $this->initScopeAndScopeCode();
    }

    public function initScopeAndScopeCode()
    {
        $website = $this->request->getParam('website');
        $store = $this->request->getParam('store');

        if ($website) {
            $this->scope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;
            $this->scopeCode = $website;
        } else if ($store) {
            $this->scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $this->scopeCode = $store;
        } else {
            $this->scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $this->scopeCode = 0;
        }
    }
}
