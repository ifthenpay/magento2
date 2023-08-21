<?php
/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Ifthenpay\Payment\Config;


/**
 * Class ConfigVars
 * store all the module static variables
 * @package Ifthenpay\Payment\Config
 */
final class ConfigVars
{

    /* -------------------------------------------------------------------------- */
    /*                              Module Variables                              */
    /* -------------------------------------------------------------------------- */


    public const VENDOR = 'ifthenpay';
    public const MODULE = 'payment';
    public const VENDOR_CC = 'Ifthenpay';
    public const MODULE_CC = 'Payment';
    public const MODULE_NAME = self::VENDOR_CC . '_' . self::MODULE_CC;
    public const VENDOR_PREFIX = 'ifthenpay_';


    /* -------------------------------------------------------------------------- */
    /*                                   Gateway                                  */
    /* -------------------------------------------------------------------------- */

    public const MULTIBANCO = 'multibanco';
    public const MULTIBANCO_DYNAMIC = 'MB';


    public const MBWAY = 'mbway';
    public const PAYSHOP = 'payshop';
    public const CCARD = 'ccard';

    public const PAYMENT_METHODS = [
        self::MULTIBANCO,
        self::MBWAY,
        self::PAYSHOP,
        self::CCARD
    ];

    public const PAYMENT_METHOD_CODES = [
        self::MULTIBANCO_CODE,
        self::MBWAY_CODE,
        self::PAYSHOP_CODE,
        self::CCARD_CODE
    ];

    public const REFUNDABLE_PAYMENT_METHOD_CODES = [
        self::MBWAY_CODE,
        self::CCARD_CODE
    ];


    public const MULTIBANCO_DEADLINE_HOURS = 23;
    public const MULTIBANCO_DEADLINE_MINUTES = 59;
    public const PAYSHOP_DEADLINE_HOURS = 0;
    public const PAYSHOP_DEADLINE_MINUTES = 0;
    public const CCARD_DEADLINE_MINUTES = 30;
    public const MBWAY_DEADLINE_MINUTES = 30;


    /* -------------------------------------------------------------------------- */
    /*                            Database Table Names                            */
    /* -------------------------------------------------------------------------- */

    public const DB_MULTIBANCO_TABLE_NAME = 'ifthenpay_multibanco';
    public const DB_PAYSHOP_TABLE_NAME = 'ifthenpay_payshop';
    public const DB_MBWAY_TABLE_NAME = 'ifthenpay_mbway';
    public const DB_CCARD_TABLE_NAME = 'ifthenpay_ccard';


    /* -------------------------------------------------------------------------- */
    /*                      Ifthenpay database Payment status                     */
    /* -------------------------------------------------------------------------- */

    public const DB_STATUS_PENDING = 'pending';
    public const DB_STATUS_PAID = 'paid';
    public const DB_STATUS_CANCELED = 'canceled';
    public const DB_STATUS_REFUNDED = 'refunded';





    /* -------------------------------------------------------------------------- */
    /*                           Callback Urls subString                          */
    /* -------------------------------------------------------------------------- */

    public const MULTIBANCO_CALLBACK_STRING = 'ifthenpay/Frontend/CallbackCtrl?ec=ma&payment=multibanco&phish_key=[CHAVE_ANTI_PHISHING]&entity=[ENTIDADE]&reference=[REFERENCIA]&amount=[VALOR]';
    public const PAYSHOP_CALLBACK_STRING = 'ifthenpay/Frontend/CallbackCtrl?ec=ma&payment=payshop&phish_key=[CHAVE_ANTI_PHISHING]&reference=[REFERENCIA]&amount=[VALOR]';
    public const MBWAY_CALLBACK_STRING = 'ifthenpay/Frontend/CallbackCtrl?ec=ma&payment=mbway&phish_key=[CHAVE_ANTI_PHISHING]&transaction_id=[ID_TRANSACAO]&amount=[VALOR]';

    /* -------------------------------------------------------------------------- */
    /*                        CCARD gateway "callback" url and status                       */
    /* -------------------------------------------------------------------------- */
    public const CCARD_CALLBACK_STRING = 'ifthenpay/Frontend/CallbackCcardCtrl?order_id=[ORDER_ID]&qn=[QN]';
    public const CCARD_SUCCESS_STATUS = '6dfcbb0428e4f89c';
    public const CCARD_ERROR_STATUS = '101737ba0aa2e7c5';
    public const CCARD_CANCEL_STATUS = 'd4d26126c0f39bf2';



    /* -------------------------------------------------------------------------- */
    /*                               Database Config                              */
    /* -------------------------------------------------------------------------- */

    public const MULTIBANCO_CODE = self::VENDOR . '_' . self::MULTIBANCO;
    public const MBWAY_CODE = self::VENDOR . '_' . self::MBWAY;
    public const PAYSHOP_CODE = self::VENDOR . '_' . self::PAYSHOP;
    public const CCARD_CODE = self::VENDOR . '_' . self::CCARD;
    public const IFTHENPAY_CODE = self::VENDOR;


    public const DB_CONFIG_PREFIX_GENERIC = 'payment/ifthenpay/';
    public const DB_CONFIG_PREFIX = 'payment/ifthenpay_';
    public const DB_CONFIG_PREFIX_MULTIBANCO = self::DB_CONFIG_PREFIX . self::MULTIBANCO . '/';
    public const DB_CONFIG_PREFIX_PAYSHOP = self::DB_CONFIG_PREFIX . self::PAYSHOP . '/';
    public const DB_CONFIG_PREFIX_MBWAY = self::DB_CONFIG_PREFIX . self::MBWAY . '/';
    public const DB_CONFIG_PREFIX_CCARD = self::DB_CONFIG_PREFIX . self::CCARD . '/';

    public const BACKOFFICE_KEY = 'backoffice_key';
    public const USER_PAYMENT_METHODS = 'user_payment_methods';
    public const USER_ACCOUNTS = 'user_accounts';
    public const REQUEST_TOKEN = 'request_token';





    /* -------------------------------------------------------------------------- */
    /*                          Multibanco writable CONFIGURATION                          */
    /* -------------------------------------------------------------------------- */

    public const MULTIBANCO_CALLBACK_URL = 'callback_url';
    public const MULTIBANCO_ANTI_PHISHING_KEY = 'anti_phishing_key';
    public const MULTIBANCO_ACTIVATE_CALLBACK = 'activate_callback';
    public const MULTIBANCO_IS_CALLBACK_ACTIVATED = 'is_callback_activated';



    /* -------------------------------------------------------------------------- */
    /*                          Payshop writable CONFIGURATION                          */
    /* -------------------------------------------------------------------------- */

    public const PAYSHOP_CALLBACK_URL = 'callback_url';
    public const PAYSHOP_ANTI_PHISHING_KEY = 'anti_phishing_key';
    public const PAYSHOP_ACTIVATE_CALLBACK = 'activate_callback';
    public const PAYSHOP_IS_CALLBACK_ACTIVATED = 'is_callback_activated';




    /* -------------------------------------------------------------------------- */
    /*                          Mbway writable CONFIGURATION                          */
    /* -------------------------------------------------------------------------- */

    public const MBWAY_CALLBACK_URL = 'callback_url';
    public const MBWAY_ANTI_PHISHING_KEY = 'anti_phishing_key';
    public const MBWAY_ACTIVATE_CALLBACK = 'activate_callback';
    public const MBWAY_IS_CALLBACK_ACTIVATED = 'is_callback_activated';




    /* -------------------------------------------------------------------------- */
    /*                          Card writable CONFIGURATION                          */
    /* -------------------------------------------------------------------------- */


    public const IS_PAYMENT_METHOD_ACTIVE = 'active';
    public const SHOW_PAYMENT_ICON = 'show_payment_icon';
    public const TITLE = 'title';
    public const MIN_VALUE = 'min_order_total';
    public const MAX_VALUE = 'max_order_total';
    public const ALLOWSPECIFIC = 'allowspecific';
    public const SPECIFICCOUNTRY = 'specificcountry';
    public const SORT_ORDER = 'sort_order';
    public const SHOW_REFUND = 'show_refund';




    /* ----------------------------- Multibanco conf ---------------------------- */

    public const MULTIBANCO_ENTITY = 'entity';
    public const MULTIBANCO_SUB_ENTITY = 'sub_entity';
    public const MULTIBANCO_DEADLINE = 'deadline';
    public const MULTIBANCO_SEND_INVOICE_EMAIL = 'send_invoice_email';



    /* ------------------------------ Payshop conf ------------------------------ */



    public const PAYSHOP_KEY = 'key';
    public const PAYSHOP_DEADLINE = 'deadline';
    public const PAYSHOP_SEND_INVOICE_EMAIL = 'send_invoice_email';


    /* ------------------------------- MB WAY conf ------------------------------ */
    public const MBWAY_KEY = 'key';
    public const MBWAY_SEND_INVOICE_EMAIL = 'send_invoice_email';
    public const MBWAY_SHOW_COUNTDOWN = 'show_countdown';


    /* ------------------------------- CCard conf ------------------------------- */
    public const CCARD_KEY = 'key';
    public const CCARD_SEND_INVOICE_EMAIL = 'send_invoice_email';






    /* -------------------------------------------------------------------------- */
    /*                            magento native config                           */
    /* -------------------------------------------------------------------------- */

    const GENERAL_NAME_PATH = 'trans_email/ident_general/name';
    const GENERAL_EMAIL_PATH = 'trans_email/ident_general/email';
    const SALES_NAME_PATH = 'trans_email/ident_sales/name';
    const SALES_EMAIL_PATH = 'trans_email/ident_sales/email';




    /* -------------------------------------------------------------------------- */
    /*                                Module paths                                */
    /* -------------------------------------------------------------------------- */

    public const PATH_LOG_FILE = '/var/log/ifthenpay.log';

    public const PATH_TEMPLATE_ADMIN_SYSTEM_CONFIG_FORM = self::VENDOR_CC . '_' . self::MODULE_CC . '::system/config/form/';




    /* -------------------------------------------------------------------------- */
    /*                                  API urls                                  */
    /* -------------------------------------------------------------------------- */

    public const API_URL_IFTHENPAY_BASE = 'web/secure/base_url';

    public const API_URL_GET_ACCOUNTS_BY_BACKOFFICE = 'https://www.ifthenpay.com/IfmbWS/ifmbws.asmx/getEntidadeSubentidadeJsonV2';

    public const API_URL_ACTIVATE_CALLBACK = 'https://ifthenpay.com/api/endpoint/callback/activation';

    public const API_URL_MBWAY_SET_REQUEST = 'https://mbway.ifthenpay.com/IfthenPayMBW.asmx/SetPedidoJSON';
    public const API_URL_POST_MBWAY_GET_PAYMENT_STATUS = 'https://www.ifthenpay.com/mbwayws/ifthenpaymbw.asmx/EstadoPedidosJSON';


    public const API_URL_CCARD_SET_REQUEST = 'https://ifthenpay.com/api/creditcard/init/';


    public const API_URL_MULTIBANCO_DYNAMIC_SET_REQUEST = 'https://ifthenpay.com/api/multibanco/reference/init';
    public const API_URL_PAYSHOP_SET_REQUEST = 'https://ifthenpay.com/api/payshop/reference/';

    public const API_URL_IFTHENPAY_POST_REFUND = 'https://ifthenpay.com/api/endpoint/payments/refund';















    /* -------------------------------------------------------------------------- */
    /*                      internal ajax requests urls strings                   */
    /* -------------------------------------------------------------------------- */

    public const AJAX_URL_STR_RESET_BACKOFFICE_KEY = 'ifthenpay/Config/ResetBackofficeKeyCtrl';
    public const AJAX_URL_STR_GET_SUB_ENTITIES = 'ifthenpay/Config/GetSubEntitiesCtrl';
    public const AJAX_URL_STR_GET_MBWAY_PAYMENT_STATUS = 'ifthenpay/Frontend/MbwayCheckAndSetOrderStatusCtrl';
    public const AJAX_URL_STR_GET_MBWAY_RESEND_NOTIFICATION = 'ifthenpay/Frontend/MbwayResendNotificationCtrl';
    public const AJAX_URL_STR_GET_REQUEST_ACCOUNT = 'ifthenpay/Config/RequestAccountCtrl';
    public const AJAX_URL_STR_GET_REFRESH_ACCOUNTS = 'ifthenpay/Config/RefreshUserAccountsinternalyCtrl';
    public const AJAX_URL_STR_GET_REQUEST_REFUND_TOKEN = 'ifthenpay/Config/RequestRefundTokenCtrl';
    public const AJAX_URL_STR_GET_VERIFY_REFUND_TOKEN = 'ifthenpay/Config/VerifyRefundTokenCtrl';







    /* -------------------------------------------------------------------------- */
    /*                                DOM Document                                */
    /* -------------------------------------------------------------------------- */

    public const DOM_RESET_BACKOFFICE_KEY_BTN_ID = 'reset_backoffice_key_btn';

    /* -------------------------------------------------------------------------- */
    /*                                 Asset Paths                                */
    /* -------------------------------------------------------------------------- */

    /* ---------------------- image logs shown in checkout ---------------------- */
    public const ASSET_PATH_CHECKOUT_LOGO_MULTIBANCO = self::MODULE_NAME . '::img/multibanco.png';
    public const ASSET_PATH_CHECKOUT_LOGO_PAYSHOP = self::MODULE_NAME . '::img/payshop.png';
    public const ASSET_PATH_CHECKOUT_LOGO_MBWAY = self::MODULE_NAME . '::img/mbway.png';
    public const ASSET_PATH_CHECKOUT_LOGO_CCARD = self::MODULE_NAME . '::img/ccard.png';


    public const ASSET_PATH_SPINNER = self::MODULE_NAME . '::img/spinner.svg';
    public const ASSET_PATH_CHECKOUT_CONFIRM = self::MODULE_NAME . '::img/success.png';
    public const ASSET_PATH_CHECKOUT_FAIL = self::MODULE_NAME . '::img/fail.png';
    public const ASSET_PATH_CHECKOUT_WARNING = self::MODULE_NAME . '::img/warning.png';
    public const ASSET_PATH_CHECKOUT_MBWAY_ICON_MOBILE = self::MODULE_NAME . '::img/mobilePhone.svg';





    /* -------------------------------------------------------------------------- */
    /*                                  Currency                                  */
    /* -------------------------------------------------------------------------- */
    public const CURRENCY_CODE_EURO = 'EUR';
    public const CURRENCY_SYMBOL_EURO = '€';
    public const ALLOWED_CURRENCY_CODE = self::CURRENCY_CODE_EURO;





}
