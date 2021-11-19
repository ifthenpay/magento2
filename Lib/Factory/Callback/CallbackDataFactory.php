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

namespace Ifthenpay\Payment\Lib\Factory\Callback;

use Ifthenpay\Payment\Lib\Factory\Factory;
use Ifthenpay\Payment\Lib\Payments\Gateway;
use Ifthenpay\Payment\Lib\Callback\CallbackDataCCard;
use Ifthenpay\Payment\Lib\Callback\CallbackDataMbway;
use Ifthenpay\Payment\Lib\Factory\Model\ModelFactory;
use Ifthenpay\Payment\Lib\Callback\CallbackDataPayshop;
use Ifthenpay\Payment\Lib\Callback\CallbackDataMultibanco;
use Ifthenpay\Payment\Lib\Factory\Model\RepositoryFactory;
use Ifthenpay\Payment\Lib\Contracts\Callback\CallbackDataInterface;

class CallbackDataFactory extends Factory
{
    private $modelFactory;
    private $repositoryFactory;

	public function __construct(ModelFactory $modelFactory, RepositoryFactory $repositoryFactory)
	{
        $this->modelFactory = $modelFactory;
        $this->repositoryFactory = $repositoryFactory;
	}

    public function build(): CallbackDataInterface
    {
        switch (strtolower($this->type)) {
            case Gateway::MULTIBANCO:
                return new CallbackDataMultibanco($this->modelFactory, $this->repositoryFactory);
            case Gateway::MBWAY:
                return new CallbackDataMbway($this->modelFactory, $this->repositoryFactory);
            case Gateway::PAYSHOP:
                return new CallbackDataPayshop($this->modelFactory, $this->repositoryFactory);
            case Gateway::CCARD:
                return new CallbackDataCCard($this->modelFactory, $this->repositoryFactory);
            default:
                throw new \Exception('Unknown Callback Data Class');
        }
    }
}
