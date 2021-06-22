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

namespace Ifthenpay\Payment\Lib\Utility;

class Status {

    private $statusSucess = "6dfcbb0428e4f89c";
    private $statusError = "101737ba0aa2e7c5";
    private $statusCancel = "d4d26126c0f39bf2";

    public function getTokenStatus(string $token): string
    {
        switch ($token) {
            case $this->statusSucess:
                return 'success';
            case $this->statusCancel:
                return 'cancel';
            case $this->statusError:
                return 'error';
            default:
                return '';
        }
    }

    public function getStatusSucess(): string
    {
        return $this->statusSucess;
    }

    public function getStatusError(): string
    {
        return $this->statusError;
    }

    public function getStatusCancel(): string
    {
        return $this->statusCancel;
    }
}