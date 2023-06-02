<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Ifthenpay\Payment\Lib\Utility;

use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;


class Cookie
{

    private $customCookieManager;
    private $customCookieMetadataFactory;

    public function __construct(
        CookieManagerInterface $customCookieManager,
        CookieMetadataFactory $customCookieMetadataFactory
    ) {

        $this->customCookieManager = $customCookieManager;
        $this->customCookieMetadataFactory = $customCookieMetadataFactory;
    }

    public function setCookie($name, $value, $durationSeconds)
    {
        $customCookieMetadata = $this->customCookieMetadataFactory->createPublicCookieMetadata();
        $customCookieMetadata->setDuration($durationSeconds);
        $customCookieMetadata->setPath('/');
        $customCookieMetadata->setHttpOnly(false);

        return $this->customCookieManager->setPublicCookie(
            $name,
            $value,
            $customCookieMetadata
        );
    }


    public function getCookie($name)
    {
        return $this->customCookieManager->getCookie(
            $name
        );
    }

}
