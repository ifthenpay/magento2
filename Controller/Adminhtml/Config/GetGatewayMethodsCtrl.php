<?php

/**
 * @category    Gateway Payment
 * @package     Ifthenpay_Payment
 * @author      Ifthenpay
 * @copyright   Ifthenpay (https://www.ifthenpay.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ifthenpay\Payment\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Ifthenpay\Payment\Logger\Logger;
use Ifthenpay\Payment\Config\ConfigVars;
use Ifthenpay\Payment\Gateway\Config\IfthenpayConfig;
use Ifthenpay\Payment\Gateway\Config\IfthenpaygatewayConfig;
use Ifthenpay\Payment\Lib\Services\GatewayService;


class GetGatewayMethodsCtrl extends Action
{
    private $ifthenpaygatewayConfig;
    private $resultJsonFactory;
    private $configData;
    private $logger;
    private $gatewayService;

    protected $configFactory;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Logger $logger,
        IfthenpayConfig $configData,
        IfthenpaygatewayConfig $ifthenpaygatewayConfig,
        GatewayService $gatewayService
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->configData = $configData;
        $this->ifthenpaygatewayConfig = $ifthenpaygatewayConfig;
        $this->gatewayService = $gatewayService;
    }

    public function execute()
    {
        try {

            $requestData = $this->getRequest()->getParams();
            $this->configData->setScopeAndScopeCode($requestData['scope'], $requestData['scopeCode']);
            $this->ifthenpaygatewayConfig->setScopeAndScopeCode($requestData['scope'], (int)$requestData['scopeCode']);

            // data from request
            $gatewayKey = $requestData['gatewayKey'];
            $inputMethodsName = $requestData['inputMethodsName'];
            $inputMethodsId = $requestData['inputMethodsId'];
            $willClearSelection = isset($requestData['willClearSelection']) ? $requestData['willClearSelection'] : 'false';

            $inputDefaultMethodName = $requestData['inputDefaultMethodName'];
            $inputDefaultMethodId = $requestData['inputDefaultMethodId'];

            // data from db
            $backofficeKey = $this->configData->getBackofficeKey();
            $paymentMethodGroupArray = $this->gatewayService->getIfthenpayGatewayPaymentMethodsDataByBackofficeKeyAndGatewayKey($backofficeKey, $gatewayKey);

            $storedMethods = [];
            $storedDefaultPaymentMethod = '';
            if ($willClearSelection != 'true') {
                $storedMethods = $this->ifthenpaygatewayConfig->getPaymentMethods();
                $storedMethods = $storedMethods != '' ? json_decode($storedMethods, true) : [];
                $storedDefaultPaymentMethod = $this->ifthenpaygatewayConfig->getDefaultPaymentMethod();
            }



            $methodsHtml = $this->generateIfthenpaygatewayPaymentMethodsHtml($paymentMethodGroupArray, $gatewayKey, $inputMethodsName, $inputMethodsId, $storedMethods);



            $defaultMethodHtml = $this->generateIfthenpaygatewayDefaultPaymentMethodSelectionHtml($paymentMethodGroupArray, $storedDefaultPaymentMethod, $inputDefaultMethodName, $inputDefaultMethodId);

            return $this->resultJsonFactory->create()->setData(['success' => true, 'methodsHtml' => $methodsHtml, 'defaultMethodHtml' => $defaultMethodHtml]);
        } catch (\Throwable $th) {
            $this->logger->error('Failed to get corresponding Payment Gateway Methods.', [
                'error' => $th,
            ]);

            return $this->resultJsonFactory->create()->setData(['error' => true, 'errorMessage' => __('Failed to get corresponding Payment Gateway Methods.'), 'methodsHtml' => '', 'defaultMethodHtml' => '']);
        }
    }

    private function generateIfthenpaygatewayDefaultPaymentMethodSelectionHtml($paymentMethodGroupArray, $storedDefaultPaymentMethod, $inputDefaultMethodName, $inputDefaultMethodId)
    {

        $html = '';

        $index = 0;
        $accountOptions = '<option value="' . $index . '">' . __('none') . '</option>';

        foreach ($paymentMethodGroupArray as $paymentMethodGroup) {
            $index++;

            $isDisabled = '';
            if (isset($storedMethods[$paymentMethodGroup['Entity']]['is_active'])) {
                $isDisabled = $storedMethods[$paymentMethodGroup['Entity']]['is_active'] ? '' : 'disabled';
            }
            // disable option if no accounts exist
            if (empty($paymentMethodGroup['accounts'])) {
                $isDisabled = 'disabled';
            }

            $selectedStr = $index == $storedDefaultPaymentMethod ? 'selected' : '';

            $accountOptions .= <<<HTML
			<option value="{$index}" data-method="{$paymentMethodGroup['Entity']}" {$selectedStr} {$isDisabled}>{$paymentMethodGroup['Method']}</option>
			HTML;
        }


        $html = <<<HTML
		<select name="{$inputDefaultMethodName}" id="{$inputDefaultMethodId}" class="form-control">
			{$accountOptions}
		</select>
		HTML;

        return $html;
    }



    public function generateIfthenpaygatewayPaymentMethodsHtml(array $paymentMethodGroupArray, string $gatewayKey, string $inputName, string $inputId, array $storedMethods): string
    {

        if ($gatewayKey == '') {
            return '<p>' . __('Please select a Ifthenpay Gateway key to view this field.') . '</p>';
        }


        $ifthenpaygatewayKeys = $this->configData->getUserPaymentMethodAccounts(ConfigVars::IFTHENPAYGATEWAY);
        $isStaticGatewayKey = !$this->gatewayService->is_dynamic($ifthenpaygatewayKeys, $gatewayKey);

        $html = '';

        $placeHolderAccounts = [];

        foreach ($paymentMethodGroupArray as $paymentMethodGroup) {

            if (! $paymentMethodGroup['IsVisible']) {
                continue;
            }

            $accountOptions = '';
            $account = [];

            $entity = $paymentMethodGroup['Entity']; // unique identifier code like 'MB' or 'MULTIBANCO'
            $imgUrl = $paymentMethodGroup['SmallImageUrl'];

            $index = 0;
            foreach ($paymentMethodGroup['accounts'] as $account) {


                if ($index === 0 && empty($storedMethods)) {
                    if (isset($account['SubEntidade'])) {
                        $placeHolderAccounts[$entity] = [
                            'account' => $account['Conta'],
                            'is_active' => '1',
                            'image_url' => $imgUrl,
                        ];
                    }
                }
                $index++;

                // set selected payment method key
                $selectedStr = '';
                if (isset($storedMethods[$entity]['account'])) {
                    $selectedStr = $account['Conta'] == $storedMethods[$entity]['account'] ? 'selected' : '';
                }


                $accountOptions .= <<<HTML
				<option value="{$account['Conta']}" {$selectedStr}>{$account['Alias']}</option>
				HTML;
            }


            $checkDisabledStr = $accountOptions === '' ? 'disabled' : '';
            $selectDisabledStr = ($accountOptions === '' || $isStaticGatewayKey) ? 'disabled' : '';
            $checkedStr = '';


            if ($accountOptions !== '') {
                // show method account select

                $selectOrActivate = <<<HTML
				<select {$selectDisabledStr} name="IFTHENPAY_IFTHENPAYGATEWAY_METHODS[{$paymentMethodGroup['Entity']}][account]" id="{$paymentMethodGroup['Entity']}" class="form-control" data-img_url="{$imgUrl}">
					{$accountOptions}
				</select>
				HTML;

                // if the isActive is saved use it
                $checkedStr = (isset($storedMethods[$entity]['is_active']) && $storedMethods[$entity]['is_active'] == '1') || !$storedMethods ? 'checked' : '';
            } else {

                // show request button
                $requestMethodStr = __('Request Gateway Method') . ' ' . $paymentMethodGroup['Method'];

                $selectOrActivate = <<<HTML
                    <button type="button" title="request payment method" class="action-primary  request_ifthenpaygateway_method" data-method="{$paymentMethodGroup['Entity']}">
                        <span>{$requestMethodStr}</span>
                    </button>
                HTML;
            }

            $html .= <<<HTML
			<div class="method_line" data-method="{$paymentMethodGroup['Entity']}" >

				<div class="method_checkbox">
					<label>
						<input type="checkbox" name="IFTHENPAY_IFTHENPAYGATEWAY_METHODS[{$paymentMethodGroup['Entity']}][is_active]" value="1" {$checkedStr} {$checkDisabledStr} data-method="{$paymentMethodGroup['Entity']}" class="method_checkbox_input"/>
						<img src="{$paymentMethodGroup['ImageUrl']}" alt="{$paymentMethodGroup['Method']}"/>
					</label>
				</div>
				<div class="method_select">
					{$selectOrActivate}
				</div>
			</div>
			HTML;
        }

        // either set stored methods to hidden input or add a set of the first of all methods
        $hiddenInputValue = !empty($storedMethods) ? json_encode($storedMethods) : json_encode($placeHolderAccounts);

        $html .= <<<HTML
            <input type="hidden" name="{$inputName}" id="{$inputId}" value='{$hiddenInputValue}'/>
        HTML;

        return $html;
    }
}
