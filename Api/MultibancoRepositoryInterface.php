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

namespace Ifthenpay\Payment\Api;

use Ifthenpay\Payment\Model\Multibanco;
use Ifthenpay\Payment\Api\IfthenpayRepositoryInterface;

interface MultibancoRepositoryInterface extends IfthenpayRepositoryInterface
{

    public function save(Multibanco $multibanco);
    public function getByReferencia(string $referencia);
    public function getById(string $multibancoId);
}
