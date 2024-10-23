<?php

/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Ifthenpay\Payment\Lib\Factory\ConfigFactory;
use Ifthenpay\Payment\Lib\Services\UpdatesService;
use Ifthenpay\Payment\Logger\Logger;
use Magento\Framework\Module\ModuleListInterface;

class AvailableUpdate extends Field
{
    /**
     * Template path
     *
     * @var string
     */
    public $_template = 'Ifthenpay_Payment::system/config/form/availableUpdate.phtml';
    protected $configFactory;
    protected $logger;
    public $data;
    protected $paymentMethod;
    private $updatesService;
    protected $moduleList;
    public function __construct(
        Context $context,
        ConfigFactory $configFactory,
        Logger $logger,
        UpdatesService $updatesService,
        ModuleListInterface $moduleList,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configFactory = $configFactory;
        $this->logger = $logger;
        $this->updatesService = $updatesService;
        $this->moduleList = $moduleList;
    }

    /**
     * Render fieldset html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        try {
            $updatesJson = $this->updatesService->getUpgradeJsonFile();

            $latestVersion = $updatesJson['version'] ?? '';
            $githubLink = $updatesJson['view'] ?? '';
            $moduleVersion = $this->moduleList->getOne('Ifthenpay_Payment')['setup_version'] ?? '';

            $instructionHeaderTranslation_1 = __('To upgrade');
            $instructionHeaderTranslation_2 = __(', run the following commands:');

            $defaultUpgradeCommandsHtml = <<<HTML
                <div class="instructions">
                    <h2><b>{$instructionHeaderTranslation_1}</b>{$instructionHeaderTranslation_2}</h2>
                    <pre><code>
                    composer update ifthenpay/magento2
                    php bin/magento setup:upgrade
                    php bin/magento setup:di:compile
                    php bin/magento cache:clean
                    </code></pre>
                </div>
            HTML;

            if (version_compare($latestVersion, $moduleVersion, '>')) {

                $this->data['latest_version'] = $latestVersion;
                $this->data['github_link'] = $githubLink;
                $this->data['commands_html'] = $defaultUpgradeCommandsHtml;

                return $this->_decorateRowHtml($element, "<td colspan='5'>" . $this->toHtml() . '</td>');
            }
        } catch (\Throwable $th) {
            $this->logger->error('config/upgrade/info', [
                'error' => $th,
            ]);
        }
        return $this->_decorateRowHtml($element, '');
    }
}
