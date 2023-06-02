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
    'jquery/ui',
    'mage/translate'
], function ($, documentReady, ui, $t) {

    // run when document is ready
    init();

    function init() {
        copyNoSpaces();
    }

    function copyNoSpaces() {
        let formattedElements = document.getElementsByClassName("copy_no_spaces");

        for (let i = 0; i < formattedElements.length; i++) {
            formattedElements[i].addEventListener('copy', function (event) {
                const selection = document.getSelection();

                const selectionNoSpaces = selection.toString().replace(/\s/g, '');
                event.clipboardData.setData("text/plain", selectionNoSpaces);
                event.preventDefault();
            });
        }
    }
});
