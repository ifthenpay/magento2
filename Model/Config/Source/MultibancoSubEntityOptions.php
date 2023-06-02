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
use Ifthenpay\Payment\Gateway\Config\MultibancoConfig;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;
use Magento\Framework\App\ResourceConnection;



class MultibancoSubEntityOptions implements OptionSourceInterface
{
    private $multibancoConfig;
    private $configData;
    protected $connection;


    public function __construct(
        MultibancoConfig $multibancoConfig,
        IfthenpayConfig $configData,
        ResourceConnection $resourceConnection

    ) {
        $this->multibancoConfig = $multibancoConfig;
        $this->configData = $configData;
        $this->connection = $resourceConnection->getConnection();
    }


    /**
     * Return array of multibanco sub entities as value-label pairs
     * @return array
     */
    public function toOptionArray()
    {
        $multibancoEntity = $this->multibancoConfig->getEntity();

        if ($multibancoEntity == '') {
            return [];
        }


        $multibancoSubEntity = $this->multibancoConfig->getSubEntity();
        $multibancoSubEntities = $this->configData->getSubEntities(ConfigVars::MULTIBANCO, $multibancoEntity);


        $otherEntitySubEntitiesInUse = $this->multibancoConfig->getOtherEntitiesSubEntityPairsInUse($multibancoEntity, $multibancoSubEntity);

        foreach ($otherEntitySubEntitiesInUse as $EntitySubEntityPair) {

            if ($EntitySubEntityPair['entity'] === $multibancoEntity) {
                foreach ($multibancoSubEntities as $key => $item) {
                    if ($item === $EntitySubEntityPair['subEntity']) {
                        unset($multibancoSubEntities[$key]);
                    }
                }
            }

        }



        $optionArray = [];
        foreach ($multibancoSubEntities as $value) {

            if (!is_array($value) && $value != '') {
                $optionArray[] = [
                    'value' => $value,
                    'label' => $value
                ];
            }
        }

        if ($multibancoEntity === ConfigVars::MULTIBANCO_DYNAMIC && !empty($optionArray)) {
            array_unshift($optionArray, ['value' => '', 'label' => __('Select a Multibanco Key')]);
        } else if ($multibancoEntity !== ConfigVars::MULTIBANCO_DYNAMIC && !empty($optionArray)) {
            array_unshift($optionArray, ['value' => '', 'label' => __('Select a Multibanco Sub Entity')]);
        } else {
            // fallback for when there are no keys and the the request account button is not showing
            array_unshift($optionArray, ['value' => '', 'label' => __('No Multibanco Sub Entities found')]);
        }

        return $optionArray;
    }
}
