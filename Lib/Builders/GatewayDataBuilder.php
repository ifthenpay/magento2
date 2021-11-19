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

namespace Ifthenpay\Payment\Lib\Builders;

use Ifthenpay\Payment\Lib\Builders\DataBuilder;
use Ifthenpay\Payment\Lib\Contracts\Builders\GatewayDataBuilderInterface;

class GatewayDataBuilder extends DataBuilder implements GatewayDataBuilderInterface
{
    public function setSubEntidade(string $value): GatewayDataBuilderInterface
    {
        $this->data->subEntidade = $value;
        return $this;
    }

    public function setMbwayKey(string $value): GatewayDataBuilderInterface
    {
        $this->data->mbwayKey = $value;
        return $this;
    }

    public function setPayshopKey(string $value): GatewayDataBuilderInterface
    {
        $this->data->payshopKey = $value;
        return $this;
    }

    public function setCCardKey(string $value): GatewayDataBuilderInterface
    {
        $this->data->ccardKey = $value;
        return $this;
    }
}
