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

namespace Ifthenpay\Payment\Lib\Contracts\Builders;

use Ifthenpay\Payment\Lib\Contracts\Builders\DataBuilderInterface;

interface GatewayDataBuilderInterface extends DataBuilderInterface
{
    public function setSubEntidade(string $value): GatewayDataBuilderInterface;
    public function setMbwayKey(string $value): GatewayDataBuilderInterface;
    public function setPayshopKey(string $value): GatewayDataBuilderInterface;
    public function setCCardKey(string $value): GatewayDataBuilderInterface;
}