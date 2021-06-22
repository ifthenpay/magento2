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

class SubEntidade extends Entidade
{

    public function toOptionArray(): array
    {
        $options = $this->ifthenpayConfigFormFactory->setType($this->paymentMethod)->build()->getOptions(false);
        if (empty($options)) {
            $options['Choose Entity'] = 'Choose Entity';
        }
        return $options;
    }
}
