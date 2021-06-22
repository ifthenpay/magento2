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

namespace Ifthenpay\Payment\Block;

use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Magento\Framework\View\Element\Template\Context;

class Info extends \Magento\Payment\Block\Info
{
    protected $dataFactory;

    public function __construct(Context $context, DataFactory $dataFactory,array $data = [])
    {
        $this->dataFactory = $dataFactory;
        parent::__construct($context, $data);
    }

    public function getSpecificInformation()
    {
        switch ($this->getMethodCode()) {
            case 'multibanco':
                $informations[__('Entity')->render()] = $this->getInfo()->getAdditionalInformation('entidade');
                $informations[__('Reference')->render()] = $this->getInfo()->getAdditionalInformation('referencia');
                break;
            case 'mbway':
                $informations[__('Request ID')->render()] = $this->getInfo()->getAdditionalInformation('idPedido');
                $informations[__('MB WAY Phone')->render()] = $this->getInfo()->getAdditionalInformation('telemovel');
                break;
            case 'payshop':
                $informations[__('Request ID')->render()] = $this->getInfo()->getAdditionalInformation('idPedido');
                $informations[__('Reference')->render()] = $this->getInfo()->getAdditionalInformation('referencia');
                $informations[__('Validity')->render()] = $this->getInfo()->getAdditionalInformation('validade') !== '' ? (new \DateTime($this->getInfo()->getAdditionalInformation('validade')))->format('d-m-Y') : '';

                break;
            case 'ccard':
                $informations[__('Request ID')->render()] = $this->getInfo()->getAdditionalInformation('idPedido');
                break;
            default:
                break;
        }
        $informations[__('Total to Pay')->render()] = $this->getInfo()->getAdditionalInformation('totalToPay') . $this->dataFactory->setType($this->getMethodCode())->build()->getCurrentCurrencySymbol();
        return (object)$informations;
    }

    public function getMethodCode()
    {
        return $this->getInfo()->getMethodInstance()->getCode();
    }
}
