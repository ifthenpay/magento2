/**
* Ifthenpay_Payment module dependency
*
* @category    Gateway Payment
* @package     Ifthenpay_Payment
* @author      Ifthenpay
* @copyright   Ifthenpay (http://www.ifthenpay.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
require([
    'jquery',
    'domReady!',
    'mage/translate'
], function($, documentReady,$t){
    var mbwayImg = $('#mbwayLogoUrl');
    var mbwayLogoUrl = $('#mbwayLogoUrl').attr('src');
    mbwayImg.remove();
    $('body').on('change', '#p_method_mbway', function(event) {
        var eventTarget = $(event.target);
        if ($('#ifthenpay_mb_way_control').length <= 0) {
            $(`<div class="field required" id="ifthenpay_mb_way_control">
                <label class="label">
                    <span data-bind="i18n: 'MB WAY Phone'"></span>
                </label>
                <div class="control input-container">
                    <img src="${mbwayLogoUrl}" class="icon" alt="mbway logo">
                    <input name="mbwayPhoneNumber" class="text input-field" type="text" id="ifthenpayMbwayPhone" data-validate='{"required":true, "minlength":9}' placeholder="${$t('mbwayMobilePhone')}" 
                    data-msg-required="${$t('mbwayPhoneRequired')} data-msg-minlength="${$t('mbwayPhoneMinlength')}">
                </div>
            </div>`).insertAfter(eventTarget.next('label'));
        }
        
    });
});
