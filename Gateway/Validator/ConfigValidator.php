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

namespace Ifthenpay\Payment\Gateway\Validator;

use Ifthenpay\Payment\Logger\IfthenpayLogger;
use Ifthenpay\Payment\Helper\Factory\DataFactory;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class ConfigValidator extends AbstractValidator
{
    private $configData;
    private $logger;

	public function __construct(ResultInterfaceFactory $resultFactory, DataFactory $dataFactory, IfthenpayLogger $logger)
	{
        parent::__construct($resultFactory);
        $this->configData = $dataFactory->setType('ccard')->build()->getConfig();
        $this->logger = $logger;
    }


    public function validate(array $validationSubject)
    {
        $isValid = true;
        $ccardKey = $this->configData['ccardKey'];

        if (!$ccardKey || $ccardKey === 'Choose Account') {
            $this->logger->debug('ccard key is not set', ['configData' => $this->configData]);
            $isValid = false;
        }

        return $this->createResult($isValid);
    }
}
