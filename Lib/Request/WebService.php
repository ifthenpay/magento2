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

namespace Ifthenpay\Payment\Lib\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class WebService
{
    private $client;
    private $response;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getResponseJson(): array
    {
        return json_decode(json_encode(json_decode((string) $this->response->getBody())), true);
    }

    public function postRequest(string $url, array $data, bool $jsonContentType = false): self
    {
        try {
            $this->response = $this->client->post(
                $url,
                $jsonContentType ? ['json' => $data] :
                ['form_params' => $data]
            );
            return $this;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getRequest(string $url, array $data = []): self
    {
        try {
            $this->response = $this->client->get($url, ['query' => $data]);
            return $this;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}