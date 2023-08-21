<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Ifthenpay\Payment\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Locale\Resolver;

use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Gateway\Config\MbwayConfig;


class MbwayConfigProvider implements ConfigProviderInterface
{
    const CODE = ConfigVars::MBWAY_CODE;
    // const COUNTRY_CODES_PATH = __DIR__ . '/CountryCodes.json';
    const COUNTRY_CODES_PATH = __DIR__ . '/../../Lib/Utility/countryCodes.json';

    // /home/docker/magento245/src/app/code/Ifthenpay/Payment/Lib/Utility/countryCodes.json

    protected $assetRepository;
    protected $config;

    public function __construct(
        MbwayConfig $config,
        Repository $assetRepository,
        Resolver $locale,
    ) {
        $this->config = $config;
        $this->assetRepository = $assetRepository;
        $this->locale = $locale;
    }

    public function getConfig(): array
    {

        $langCode = $this->locale->getLocale();

        return [
            'payment' => [
                'ifthenpay_mbway' => [
                    'logoUrl' => $this->assetRepository->getUrlWithParams(ConfigVars::ASSET_PATH_CHECKOUT_LOGO_MBWAY, []),
                    'mobileIconUrl' => $this->assetRepository->getUrlWithParams(ConfigVars::ASSET_PATH_CHECKOUT_MBWAY_ICON_MOBILE, []),
                    'showPaymentIcon' => $this->config->getShowPaymentIcon(),
                    'title' => $this->config->getTitle(),
                    'countryCodeOptions' => self::generateCountryCodeOptions($langCode),
                ],
            ]
        ];
    }


	/**
	 * generate an array of country code options to use in a select box for mbway smartphone number
	 * @param string $lang
	 * @return array
	 */
	private static function generateCountryCodeOptions(string $lang): array
	{
        switch ($lang) {
            case 'pt_PT':
                $lang = 'PT';
                break;
            case 'pt_BR':
                $lang = 'PT';
                break;
            case 'es_ES':
                $lang = 'ES';
                break;
            case 'fr_FR':
                $lang = 'FR';
                break;
            default:
                $lang = 'EN';
                break;
        }


		// Read JSON file contents
		$jsonData = file_get_contents(self::COUNTRY_CODES_PATH);

		// Parse JSON data into an associative array
		$countryCodes = json_decode($jsonData, true);

		// get correct language key
		$lang = strtoupper($lang);
		$lang = (isset($countryCodes['mobile_prefixes']) && isset($countryCodes['mobile_prefixes'][0]) && isset($countryCodes['mobile_prefixes'][0][$lang])) ? $lang : 'EN';


		$countryCodeOptions = [];
		foreach ($countryCodes['mobile_prefixes'] as $country) {

			if ($country['Ativo'] != 1) {
				continue; // skip this one
			}

			$countryCodeOptions[] = [
				'value' => $country['Indicativo'],
				'name' => $country[$lang] . ' (+' . $country['Indicativo'] . ')'
			];
		}

		return $countryCodeOptions;
	}
}
