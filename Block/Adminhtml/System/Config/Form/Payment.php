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

namespace Ifthenpay\Payment\Block\Adminhtml\System\Config\Form;

use Magento\Config\Model\Config;
use Magento\Backend\Block\Context;
use Magento\Framework\View\Helper\Js;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Magento\Framework\View\Asset\Repository;

class Payment extends Fieldset
{
    private $secureRenderer;
    private $urlBuilder;

    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        Config $config,
        SecureHtmlRenderer $secureRenderer,
        UrlInterface $urlBuilder,
        Repository $assetRepository,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $authSession,
            $jsHelper,
            $data,
            $secureRenderer
        );
        $this->config         = $config;
        $this->secureRenderer = $secureRenderer;
        $this->urlBuilder = $urlBuilder;
        $this->assetRepository = $assetRepository;
    }

    protected function _getFrontendClass($element)
    {
        return parent::_getFrontendClass($element) . ' with-button';
    }

    protected function _getHeaderTitleHtml($element)
    {
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

        $html .= /* @noEscape */ $this->secureRenderer->renderEventListenerAsTag(
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
        $urlChangeEntidade = $this->urlBuilder->getUrl('ifthenpay/Config/ChangeEntidade');
        $urlChooseNewEntidadeSubEntidade = $this->urlBuilder->getUrl('ifthenpay/Config/ChooseNewEntidadeSubEntidade');
        $urlAddNewAccount = $this->urlBuilder->getUrl('ifthenpay/Config/AddNewAccount');
        $urlResetAccounts = $this->urlBuilder->getUrl('ifthenpay/Config/ResetAccounts');

        $script = "require(['jquery', 'prototype'], function(jQuery){
            window.urlChangeEntidade =" . json_encode($urlChangeEntidade) . ";
            window.urlChooseNewEntidadeSubEntidade =" . json_encode($urlChooseNewEntidadeSubEntidade) . ";
            window.urlAddNewAccount =" . json_encode($urlAddNewAccount) . "; 
            window.urlResetAccounts =" . json_encode($urlResetAccounts) . ";
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
