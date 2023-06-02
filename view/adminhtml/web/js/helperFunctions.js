/**
 * used to display a message below the field defined by fieldSelector
 * @param {*} fieldSelector must be a valid jquery selector, for example: "#title", "select[id*="ifthenpay_multibanco_sub_entity"]"
 * @param {*} message
 */
function ifth_displayErrorMessageInField(fieldSelector, message) {
    require(['jquery', 'jquery/ui'], function ($) {

        if ($(fieldSelector)) {
            $(fieldSelector).addClass('mage-error');

            let fieldId = $(fieldSelector).attr('id');

            let messageContainerId = fieldId + '-error';
            let messageContainerHtml = `<label id="${messageContainerId}" class="mage-error" for="${fieldId}"></label>`;
            let messageContainer = $(messageContainerHtml).insertAfter($('#' + fieldId));

            $(messageContainer).append(message);
        }
    });
}

function ifth_clearErrorMessageInField(fieldSelector) {
    require(['jquery', 'jquery/ui'], function ($) {

        field = $(fieldSelector);
        if (field) {
            field.removeClass('mage-error');
        }

        existingErrorMessages = field.siblings();
        if (existingErrorMessages.length > 0) {
            existingErrorMessages.remove();
        }
    });
}
