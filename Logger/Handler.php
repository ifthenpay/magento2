<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ifthenpay\Payment\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;
use Ifthenpay\Payment\Config\ConfigVars;

/**
 * Class Handler
 * Responsible for setting the log file path and log level, this is required to be able to use the Magento 2 logger
 * and store the logs in the desired file path
 * @package Ifthenpay\Payment\Logger
 */
class Handler extends Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;


    /**
     * File name
     * @var string
     */
    protected $fileName = ConfigVars::PATH_LOG_FILE;
}
