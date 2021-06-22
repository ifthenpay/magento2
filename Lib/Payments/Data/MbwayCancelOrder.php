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

declare(strict_types=1);

namespace Ifthenpay\Payment\Lib\Payments\Data;

use Ifthenpay\Payment\Lib\Request\WebService;

class MbwayCancelOrder
{
	private $ifthenpayController;
    private $webservice;

    public function __construct(Webservice $webservice)
	{
        $this->webservice = $webservice;
	}


    public function cancelOrder(): void
    {
        if ($this->ifthenpayController->config->get('payment_ifthenpay_mbway_activate_cancelMbwayOrder')) {
            $this->ifthenpayController->load->model('extension/payment/ifthenpay');
            $mbwayPendingOrders = $this->ifthenpayController->model_extension_payment_ifthenpay->getAllMbwayPendingOrders();
            if (!empty($mbwayPendingOrders)) {
                $catalogCancelOrderEndpoint = $this->ifthenpayController->config->get('config_secure') ? rtrim(HTTP_CATALOG, '/') : rtrim(HTTPS_CATALOG, '/') . '/index.php?route=extension/payment/ifthenpay/cancelMbwayOrderBackend';
                date_default_timezone_set('Europe/Lisbon');
                foreach ($mbwayPendingOrders as $mbwayOrder) {
                    $minutes_to_add = 30;
                    $time = new \DateTime($mbwayOrder['date_added']);
                    $time->add(new \DateInterval('PT' . $minutes_to_add . 'M'));
                    $today = new \DateTime(date("Y-m-d G:i"));

                    if ($time < $today) {
                        $this->webservice->getRequest(
                            $catalogCancelOrderEndpoint,
                            [
                            'order_id' => $mbwayOrder['order_id'],
                            ],
                            false
                        );
                    }
                }
            }

        }
    }

    public function setIfthenpayController($ifthenpayController)
    {
        $this->ifthenpayController = $ifthenpayController;

        return $this;
    }

    public function setRegistry($registry)
    {
        $this->registry = $registry;

        return $this;
    }
}