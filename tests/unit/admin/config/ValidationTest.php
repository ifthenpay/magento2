<?php

namespace Ifthenpay\Payment\Tests\Unit\admin\config;

use Ifthenpay\Payment\Lib\Validation\GatewayValidation;
use Magento\Framework\Exception\LocalizedException;


class ValidationTest extends \PHPUnit\Framework\TestCase
{
    public function testisBackofficeKeyValidFormatWithLength()
    {
        //arrange

        $testBackofficeKey = '1234-1234-1234-12345';

        //act
        try {
            GatewayValidation::isBackofficeKeyValidFormat($testBackofficeKey);

        } catch (\Throwable $e) {
            //assert
            $this->assertInstanceOf(LocalizedException::class, $e);
            $this->assertEquals($e->getMessage(), 'Backoffice Key format is invalid. ex: 1234-1234-1234-1234');
        }
    }

    public function testisBackofficeKeyValidFormatWithAlpha()
    {
        //arrange

        $testBackofficeKey = 'aaaa-aaaa-aaaa-aaaa';

        //act
        try {
            GatewayValidation::isBackofficeKeyValidFormat($testBackofficeKey);

        } catch (\Throwable $e) {
            //assert
            $this->assertInstanceOf(LocalizedException::class, $e);
            $this->assertEquals($e->getMessage(), 'Backoffice Key format is invalid. ex: 1234-1234-1234-1234');
        }
    }


    public function testisBackofficeKeyValidFormatWithEmpty()
    {
        //arrange

        $testBackofficeKey = '';

        //act
        try {
            GatewayValidation::isBackofficeKeyValidFormat($testBackofficeKey);

        } catch (\Throwable $e) {
            //assert
            $this->assertInstanceOf(LocalizedException::class, $e);
            $this->assertEquals($e->getMessage(), 'Backoffice Key format is invalid. ex: 1234-1234-1234-1234');
        }
    }


    public function testisBackofficeKeyValidFormatWithValid()
    {
        //arrange
        $testBackofficeKey = '1234-1234-1234-1234';

        //act
        //assert
        $this->assertNull(GatewayValidation::isBackofficeKeyValidFormat($testBackofficeKey));
    }
}
