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

namespace Ifthenpay\Payment\Model\Config\Source;

use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Lib\Factory\Config\IfthenpayConfigFormFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Entidade implements OptionSourceInterface
{
    protected $paymentMethod = Gateway::MULTIBANCO;
    protected $ifthenpayConfigFormFactory;


    public function __construct(IfthenpayConfigFormFactory $ifthenpayConfigFormFactory)
    {
        $this->ifthenpayConfigFormFactory = $ifthenpayConfigFormFactory;
    }

    public function toOptionArray(): array
    {
        return $this->ifthenpayConfigFormFactory->setType($this->paymentMethod)->build()->getOptions();
    }
}
