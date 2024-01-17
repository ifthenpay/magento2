<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Ifthenpay\Payment\Lib\Utility;

use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Ifthenpay\Payment\Config\ConfigVars;


class Version
{
    private $moduleResource;
    private $productMetadata;

    public function __construct(
        ResourceInterface $moduleResource,
        ProductMetadataInterface $productMetadata
    ) {
        $this->moduleResource = $moduleResource;
        $this->productMetadata = $productMetadata;
    }

    public function getModuleVersion(): string
    {
        return $this->moduleResource->getDbVersion(ConfigVars::MODULE_NAME);
    }

    public function getMagentoVersion(): string
    {
        return $this->productMetadata->getVersion();
    }

    public function replaceVersionVariables(string $str): string
    {
        $str = str_replace('{ec}', 'ma_' . $this->getMagentoVersion(), $str);
        $str = str_replace('{mv}', $this->getModuleVersion(), $str);

        return $str;
    }
}
