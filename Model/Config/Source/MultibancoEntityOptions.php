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
use Ifthenpay\Payment\Gateway\Config\MultibancoConfig;
use Magento\Framework\App\ResourceConnection;



class MultibancoEntityOptions implements OptionSourceInterface
{
    private $configData;
    private $multibancoConfig;
    private $readConfig;

    protected $connection;

    public function __construct(
        IfthenpayConfig $configData,
        MultibancoConfig $multibancoConfig,
        ResourceConnection $resourceConnection
    ) {
        $this->configData = $configData;
        $this->multibancoConfig = $multibancoConfig;
        $this->connection = $resourceConnection->getConnection();
    }


    /**
     * Return array of multibanco entities as value-label pairs
     * @return array
     */
    public function toOptionArray()
    {
        $multibancoEntities = $this->configData->getUserPaymentMethodAccounts(ConfigVars::MULTIBANCO);

        $thisEntity = $this->multibancoConfig->getEntity();
        $thisSubEntity = $this->multibancoConfig->getSubEntity();
        $otherEntitiesInUse = $this->multibancoConfig->getOtherEntitiesSubEntityPairsInUse($thisEntity, $thisSubEntity);



        foreach ($otherEntitiesInUse as $otherEntity) {


            // foreach entity
            for ($i = 0; $i < count($multibancoEntities); $i++) {



                if ($otherEntity['entity'] == $multibancoEntities[$i]['Entidade']) {

                    // foreach subentity
                    for ($j = 0; $j < count($multibancoEntities[$i]['SubEntidade']); $j++) {
                        if (
                            isset($multibancoEntities[$i]['SubEntidade'][$j]) &&
                            $otherEntity['subEntity'] == $multibancoEntities[$i]['SubEntidade'][$j]
                        ) {
                            // remove sub entity
                            unset($multibancoEntities[$i]['SubEntidade'][$j]);
                        }
                    }

                    // if entity does not have any subentity remove it
                    if (count($multibancoEntities[$i]['SubEntidade']) <= 0) {
                        unset($multibancoEntities[$i]);
                    }
                }
            }
        }



        $optionArray = [];
        foreach ($multibancoEntities as $key => $value) {

            if (is_array($value) && isset($value['Entidade']) && isset($value['SubEntidade'])) {


                if ($value['Entidade'] == ConfigVars::MULTIBANCO_DYNAMIC) {
                    $optionArray[] = [
                        'value' => $value['Entidade'],
                        'label' => __('Multibanco Dynamic References')
                    ];
                } else {
                    $optionArray[] = [
                        'value' => $value['Entidade'],
                        'label' => $value['Entidade']
                    ];
                }
            }
        }

        if (!empty($optionArray)) {
            array_unshift($optionArray, ['value' => '', 'label' => __('Select a Multibanco Entity')]);
        } else {
            // fallback for when there are no keys and the the request account button is not showing
            array_unshift($optionArray, ['value' => 'nokey', 'label' => __('No Multibanco Entities found')]);
        }

        return $optionArray;
    }

}
