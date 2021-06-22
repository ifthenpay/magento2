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

namespace Ifthenpay\Payment\Lib\Callback;

use Ifthenpay\Payment\Lib\Factory\Model\ModelFactory;
use Ifthenpay\Payment\Lib\Factory\Model\RepositoryFactory;


class CallbackData
{
    protected $modelFactory;
    protected $repositoryFactory;

	public function __construct(ModelFactory $modelFactory, RepositoryFactory $repositoryFactory)
	{
        $this->modelFactory = $modelFactory;
        $this->repositoryFactory = $repositoryFactory;
	}
}
