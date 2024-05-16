<?php

declare(strict_types=1);

namespace Ifthenpay\Payment\Lib\Utility;

use Magento\Framework\Locale\Resolver;

class Locale
{
    private Resolver $localeResolver;

    public function __construct(
        Resolver $localeResolver
    ) {
        $this->localeResolver = $localeResolver;
    }

    public function getCurrentLocale()
    {
        $currentLocaleCode = $this->localeResolver->getLocale(); // fr_CA
        $languageCode = strstr($currentLocaleCode, '_', true);
        return $languageCode;
    }
}
