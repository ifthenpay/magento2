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

namespace Ifthenpay\Payment\Lib\Callback;

use Ifthenpay\Payment\Lib\Request\WebService;
use Ifthenpay\Payment\Lib\Builders\GatewayDataBuilder;
use Ifthenpay\Payment\Lib\Payments\Gateway;

class Callback
{

    private $activateEndpoint = 'https://ifthenpay.com/api/endpoint/callback/activation';
    private $webService;
    private $urlCallback;
    private $chaveAntiPhishing;
    private $backofficeKey;
    private $entidade;
    private $subEntidade;
    private $activatedFor = false;

    private $urlCallbackParameters = [
        Gateway::MULTIBANCO => '?type=offline&payment={paymentMethod}&chave=[CHAVE_ANTI_PHISHING]&entidade=[ENTIDADE]&referencia=[REFERENCIA]&valor=[VALOR]',
        Gateway::MBWAY => '?type=offline&payment={paymentMethod}&chave=[CHAVE_ANTI_PHISHING]&referencia=[REFERENCIA]&id_pedido=[ID_TRANSACAO]&valor=[VALOR]&estado=[ESTADO]',
        Gateway::PAYSHOP => '?type=offline&payment={paymentMethod}&chave=[CHAVE_ANTI_PHISHING]&id_cliente=[ID_CLIENTE]&id_transacao=[ID_TRANSACAO]&referencia=[REFERENCIA]&valor=[VALOR]&estado=[ESTADO]'
    ];

    public function __construct(GatewayDataBuilder $data, WebService $webService)
    {
        $this->webService = $webService;
        $this->backofficeKey = $data->getData()->backofficeKey;
        $this->entidade = $data->getData()->entidade;
        $this->subEntidade = $data->getData()->subEntidade;
    }

    private function createAntiPhishing(): void
    {
        $this->chaveAntiPhishing = md5((string) rand());
    }

    private function createUrlCallback(string $paymentType, string $moduleLink): void
    {
        $this->urlCallback = $moduleLink . str_replace('{paymentMethod}', $paymentType, $this->urlCallbackParameters[$paymentType]);
    }

    private function activateCallback(): void
    {
        $request = $this->webService->postRequest(
            $this->activateEndpoint,
            [
                'chave' => $this->backofficeKey,
                'entidade' => $this->entidade,
                'subentidade' => $this->subEntidade,
                'apKey' => $this->chaveAntiPhishing,
                'urlCb' => $this->urlCallback,
            ],
            true
        );

        $response = $request->getResponse();
        if (!$response->getStatusCode() === 200 && !$response->getReasonPhrase()) {
            throw new \Exception("Error Activating Callback");
        }
        $this->activatedFor = true;
    }

    public function make(string $paymentType, string $moduleLink, bool $activateCallback = false): void
    {
        $this->createAntiPhishing();
        $this->createUrlCallback($paymentType, $moduleLink);
        if ($activateCallback) {
            $this->activateCallback();
        }
    }

    public function getUrlCallback(): string
    {
        return $this->urlCallback;
    }

    public function getChaveAntiPhishing(): string
    {
        return $this->chaveAntiPhishing;
    }

    public function getActivatedFor()
    {
        return $this->activatedFor;
    }
}
