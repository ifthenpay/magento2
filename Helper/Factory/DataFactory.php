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

namespace Ifthenpay\Payment\Helper\Factory;

use Ifthenpay\Payment\Helper\CCardData;
use Ifthenpay\Payment\Helper\MbwayData;
use Ifthenpay\Payment\Helper\PayshopData;
use Magento\Framework\App\Helper\Context;
use Ifthenpay\Payment\Helper\MultibancoData;
use \Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Ifthenpay\Payment\Helper\Contracts\IfthenpayDataInterface;
use Magento\Framework\Locale\CurrencyInterface;


class DataFactory
{
    private $context;
    private $storeManager;
    private $configWriter;
    private $scopeConfig;
    private $type;
    private $currency;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        WriterInterface $configWriter,
        ScopeConfigInterface $scopeConfig,
        CurrencyInterface $currency
    )
    {
        $this->context = $context;
        $this->storeManager = $storeManager;
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
        $this->currency = $currency;
    }

    public function build(): IfthenpayDataInterface {
        switch ($this->type) {
            case 'multibanco':
                return new MultibancoData(
                    $this->context,
                    $this->storeManager,
                    $this->configWriter,
                    $this->scopeConfig,
                    $this->currency
                );
            case 'mbway':
                return new MbwayData(
                    $this->context,
                    $this->storeManager,
                    $this->configWriter,
                    $this->scopeConfig,
                    $this->currency
                );
            case 'payshop':
                return new PayshopData(
                    $this->context,
                    $this->storeManager,
                    $this->configWriter,
                    $this->scopeConfig,
                    $this->currency
                );
            case 'ccard':
                return new CCardData(
                    $this->context,
                    $this->storeManager,
                    $this->configWriter,
                    $this->scopeConfig,
                    $this->currency
                );
            default:
                throw new \Exception('Unknown Helper Data Class');
        }
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}