<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Ifthenpay\Payment\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Asset\Repository;

use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Gateway\Config\MultibancoConfig;


class MultibancoConfigProvider implements ConfigProviderInterface
{
    const CODE = ConfigVars::MULTIBANCO_CODE;

    protected $assetRepository;
    protected $config;

    public function __construct(
        MultibancoConfig $config,
        Repository $assetRepository,
    ) {
        $this->config = $config;
        $this->assetRepository = $assetRepository;
    }

    public function getConfig(): array
    {
        return [
            'payment' => [
                'ifthenpay_multibanco' => [
                    'logoUrl' => $this->assetRepository->getUrlWithParams(ConfigVars::ASSET_PATH_CHECKOUT_LOGO_MULTIBANCO, []),
                    'showPaymentIcon' => $this->config->getShowPaymentIcon(),
                    'title' => $this->config->getTitle(),
                ],
            ]
        ];
    }
}
