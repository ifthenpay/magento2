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

namespace Ifthenpay\Payment\Model\Ui;

use Ifthenpay\Payment\Lib\Payments\Gateway;
use Magento\Framework\View\Asset\Repository;
use Ifthenpay\Payment\Helper\Factory\DataFactory;

class IfthenpayMasterConfigProvider
{
    protected $assetRepository;
    protected $dataFactory;

    public function __construct(
        Repository $assetRepository,
        DataFactory $dataFactory
    ) {
        $this->assetRepository = $assetRepository;
        $this->dataFactory = $dataFactory;
    }
}
