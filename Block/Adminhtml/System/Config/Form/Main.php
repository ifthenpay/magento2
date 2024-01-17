<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Block\Adminhtml\System\Config\Form;

use Magento\Config\Model\Config;
use Magento\Backend\Block\Context;
use Magento\Framework\View\Helper\Js;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Magento\Framework\View\Asset\Repository;
use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Model\ScopeConfigResolver;
use Magento\Framework\Module\ResourceInterface;


class Main extends Fieldset
{
    private $secureRenderer;
    private $urlBuilder;
    private $scopeConfigResolver;
    private $moduleResource;
    private $config;
    private $assetRepository;


    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        Config $config,
        SecureHtmlRenderer $secureRenderer,
        UrlInterface $urlBuilder,
        Repository $assetRepository,
        ScopeConfigResolver $scopeConfigResolver,
        ResourceInterface $moduleResource,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $authSession,
            $jsHelper,
            $data,
            $secureRenderer
        );
        $this->config = $config;
        $this->secureRenderer = $secureRenderer;
        $this->urlBuilder = $urlBuilder;
        $this->assetRepository = $assetRepository;
        $this->scopeConfigResolver = $scopeConfigResolver;
        $this->moduleResource = $moduleResource;
    }

    protected function _getFrontendClass($element)
    {
        return parent::_getFrontendClass($element) . ' with-button';
    }

    protected function _getHeaderTitleHtml($element)
    {
        // set the custom label with ifthenpay's logo
        $moduleVersion = $this->moduleResource->getDbVersion(ConfigVars::MODULE_NAME);


        $slogan = __('Payments by Multibanco, MB WAY, Payshop, Credit Card, and Cofidis Pay');
        $labelHtml = '<div class="ifthenpay-payment-logo"></div><div id="ifthenpay-version" class="ifthenpay-version">V' . $moduleVersion . '</div><div class="ifthenpay-payment-text">' . $slogan . '</div>';
        $element->setLegend($labelHtml);


        $html = '<div class="config-heading" >';
        $htmlId = $element->getHtmlId();
        $html .= '<div class="button-container"><button type="button"' .
            ' disabled="disabled"' .
            ' class="button action-configure' .
            ' disabled' .
            '" id="' . $htmlId . '-head" >' .
            '<span class="state-closed">' . __(
            'Configure'
        ) . '</span><span class="state-opened">' . __(
            'Close'
        ) . '</span></button>';

        $html .= /* @noEscape */$this->secureRenderer->renderEventListenerAsTag(
            'onclick',
            "IfthenpayToggleSolution.call(this, '" . $htmlId . "', '" . $this->getUrl('adminhtml/*/state') .
            "');event.preventDefault();",
            'button#' . $htmlId . '-head'
        );

        $html .= '</div>';
        $html .= '<div class="heading"><strong>' . $element->getLegend() . '</strong>';

        if ($element->getComment()) {
            $html .= '<span class="heading-intro">' . $element->getComment() . '</span>';
        }
        $html .= '<div class="config-alt"></div>';
        $html .= '</div></div>';

        return $html;
    }

    protected function _getHeaderCommentHtml($element)
    {
        return '';
    }

    protected function _isCollapseState($element)
    {
        return false;
    }

    protected function _getExtraJs($element)
    {
        $urlResetBackofficeKey = $this->urlBuilder->getUrl(ConfigVars::AJAX_URL_STR_RESET_BACKOFFICE_KEY);
        $urlGetSubEntities = $this->urlBuilder->getUrl(ConfigVars::AJAX_URL_STR_GET_SUB_ENTITIES);
        $urlRequestAccount = $this->urlBuilder->getUrl(ConfigVars::AJAX_URL_STR_GET_REQUEST_ACCOUNT);
        $urlRefreshAccounts = $this->urlBuilder->getUrl(ConfigVars::AJAX_URL_STR_GET_REFRESH_ACCOUNTS);
        $urlGetMinMax = $this->urlBuilder->getUrl(ConfigVars::AJAX_URL_STR_GET_MIN_MAX);
        $dynamicMultibancoCode = ConfigVars::MULTIBANCO_DYNAMIC;

        $scope = $this->scopeConfigResolver->scope;
        $scopeCode = $this->scopeConfigResolver->scopeCode;
        $storeId = $this->scopeConfigResolver->storeId;



        $script = "require(['jquery', 'prototype'], function(jQuery){
            window.ifthenpay_scope = " . json_encode($scope) . ";
            window.ifthenpay_scopeCode = " . json_encode($scopeCode) . ";
            window.ifthenpay_storeId = " . json_encode($storeId) . ";
            window.ifthenpay_dynamicMultibancoCode = " . json_encode($dynamicMultibancoCode) . ";
            window.ifthenpay_urlRefreshAccounts =" . json_encode($urlRefreshAccounts) . ";
            window.ifthenpay_urlRequestAccount =" . json_encode($urlRequestAccount) . ";
            window.ifthenpay_urlResetBackofficeKey =" . json_encode($urlResetBackofficeKey) . ";
            window.ifthenpay_urlGetSubEntities =" . json_encode($urlGetSubEntities) . ";
            window.ifthenpay_urlGetMinMax =" . json_encode($urlGetMinMax) . ";
            window.ifthenpay_standardErrorMessage =" . json_encode(__('An Error occurred.')) . ";
            window.IfthenpayToggleSolution = function (id, url) {
                var doScroll = false;
                Fieldset.toggleCollapse(id, url);
                if ($(this).hasClassName(\"open\")) {
                    \$$(\".with-button button.button\").each(function(anotherButton) {
                        if (anotherButton != this && $(anotherButton).hasClassName(\"open\")) {
                            $(anotherButton).click();
                            doScroll = true;
                        }
                    }.bind(this));
                }
                if (doScroll) {
                    var pos = Element.cumulativeOffset($(this));
                    window.scrollTo(pos[0], pos[1] - 45);
                }
            }
        });";

        return $this->_jsHelper->getScript($script);
    }

}
