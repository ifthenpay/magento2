<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Ifthenpay\Payment\Model\Config\Source;

use Ifthenpay\Payment\Config\ConfigVars;
use Magento\Framework\Data\OptionSourceInterface;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;
use Ifthenpay\Payment\Gateway\Config\PixConfig;



class PixKeyOptions implements OptionSourceInterface
{
	private $configData;
	private $pixConfig;

	public function __construct(
		IfthenpayConfig $configData,
		PixConfig $pixConfig
	) {
		$this->configData = $configData;
		$this->pixConfig = $pixConfig;
	}


	/**
	 * Return array of order canceling options as value-label pairs
	 * @return array
	 */
	public function toOptionArray()
	{
		$pixKeys = $this->configData->getUserPaymentMethodAccounts(ConfigVars::PIX);

		// remove keys in use by other stores (in case of multistore)
		$thisKey = $this->pixConfig->getKey();
		$otherKeysInUse = $this->pixConfig->getOtherKeysInUse($thisKey);

		// remove this key from list of excludes
		foreach ($otherKeysInUse as $key => $inUse) {
			if ($thisKey === $inUse) {
				unset($otherKeysInUse[$key]);
			}
		}

		$optionArray = [];
		foreach ($pixKeys as $key => $value) {

			if (is_array($value) && isset($value['Entidade']) && isset($value['SubEntidade'])) {

				foreach ($value['SubEntidade'] as $pixKey) {

					// only add option for keys not in the otherKeysInUse
					if (!in_array($pixKey, $otherKeysInUse)) {
						$optionArray[] = [
							'value' => $pixKey,
							'label' => $pixKey
						];
					}
				}
			}
		}

		if (!empty($optionArray)) {
			array_unshift($optionArray, ['value' => '', 'label' => __('Select a Pix key')]);
		} else {
			// fallback for when there are no pix keys and the the request pix account button is not showing
			array_unshift($optionArray, ['value' => 'nokey', 'label' => __('No Pix keys found')]);
		}

		return $optionArray;
	}
}
