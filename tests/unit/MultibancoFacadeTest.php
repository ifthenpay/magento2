<?php

use Magento\Payment\Model\Method\Adapter;
use Magento\Payment\Model\Method\Logger;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Ifthenpay\Payment\Model\Ui\MultibancoConfigProvider;
use Ifthenpay\Payment\Model\IfthenpayMultibancoFacade;
use Ifthenpay\Payment\Model\IfthenpayMultibancoConfig;
use Ifthenpay\Payment\Model\IfthenpayMultibancoValueHandlerPool;
use Ifthenpay\Payment\Model\IfthenpayMultibancoValidatorPool;
use PHPUnit\Framework\TestCase;

class IfthenpayMultibancoFacadeTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var IfthenpayMultibancoFacade
     */
    private $multibancoFacade;

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $configProvider = $this->createMock(MultibancoConfigProvider::class);
        $configProvider->method('getCode')->willReturn('ifthenpay_multibanco');

        $logger = $this->createMock(Logger::class);
        $logger->method('debug')->willReturn('');

        $config = $this->objectManager->getObject(IfthenpayMultibancoConfig::class, [
            'methodCode' => 'ifthenpay_multibanco'
        ]);

        $valueHandlerPool = $this->objectManager->getObject(IfthenpayMultibancoValueHandlerPool::class, [
            'handlers' => [
                'default' => 'IfthenpayMultibancoConfigValueHandler',
            ],
        ]);

        $validatorPool = $this->objectManager->getObject(IfthenpayMultibancoValidatorPool::class, [
            'validators' => [
                'country' => 'IfthenpayMultibancoCountryValidator',
            ],
        ]);

        $this->multibancoFacade = $this->objectManager->getObject(IfthenpayMultibancoFacade::class, [
            'logger' => $logger,
            'configProvider' => $configProvider,
            'config' => $config,
            'valueHandlerPool' => $valueHandlerPool,
            'validatorPool' => $validatorPool,
        ]);
    }

    public function testIfthenpayMultibancoFacadeInstantiation()
    {
        $this->assertInstanceOf(Adapter::class, $this->multibancoFacade);
    }
}
