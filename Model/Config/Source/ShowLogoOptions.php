<?php

/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Ifthenpay\Payment\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Ifthenpay\Payment\Lib\Enums\ShowLogoOptionsEnum as LogoEnum;



class ShowLogoOptions implements OptionSourceInterface
{

    public function toOptionArray(): array
    {
        return [
            ['value' => LogoEnum::DEFAULT->value, 'label' => __('Default')],
            ['value' => LogoEnum::TITLE->value, 'label' => __('Title')],
            ['value' => LogoEnum::COMPOSITE->value, 'label' => __('Composite')],
        ];
    }
}
