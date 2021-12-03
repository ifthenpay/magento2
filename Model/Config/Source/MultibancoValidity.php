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

use Magento\Framework\Data\OptionSourceInterface;

class MultibancoValidity implements OptionSourceInterface
{

    public function toOptionArray(): array
    {
        $options = [];
        $options['Choose Deadline'] = 'Choose Deadline';
        for ($i=0; $i < 32; $i++) {
            $options[$i] = $i;
        }
        foreach ([45, 60, 90, 120] as $value) {
            $options[$value] = $value;
        }
        return $options;
    }
}
