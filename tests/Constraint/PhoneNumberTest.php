<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

use PHPUnit\Framework\TestCase;

class PhoneNumberTest extends TestCase
{
    /**
     * @dataProvider phoneNumberProvider
     */
    public function testIsCheckingData(
        string $countryCode,
        bool $allowFixedLine,
        bool $allowMobile,
        bool $allowInternational,
        string $content,
        bool $expectedResult
    )
    {
        $constraint = new PhoneNumber($countryCode, $allowFixedLine, $allowMobile, $allowInternational);
        $this->assertEquals($expectedResult, $constraint->validate($content));
    }

    public function testIsGettingErrorMessage()
    {
        $constraint = new PhoneNumber('mx');
        $this->assertFalse($constraint->validate('123456'));
        $this->assertEquals('[field] Invalid phone number', $constraint->getErrorMessage('field'));
    }

    public function testErrorMessageIsCustomizable()
    {
        $constraint = new PhoneNumber('mx', true, true, true, 'CUSTOM!');
        $this->assertSame('[field] CUSTOM!', $constraint->getErrorMessage('field'));
    }

    public function phoneNumberProvider()
    {
        return [
            ['mx', true, true, true, '55 3603970', false],
            ['mx', true, true, true, '+52 1 99 3603700', false],
            ['mx', true, true, true, '+86 8860 1329', false],
            ['mx', true, true, false, '55 36039700', true],
            ['mx', true, true, false, '+52 1 55 36039700', true],
            ['mx', true, true, true, '+86 755 8860 1329', true],
            ['mx', true, true, false, '+86 755 8860 1329', false],
            ['ar', true, true, true, '11 4381-8383', true],
            ['co', true, true, true, '315-851-6001', true],
            ['cl', true, true, true, '22824 1067', true],
            ['ar', true, true, false, '03541452271', true],
            ['ar', true, false, false, '03541452271', true],
            ['ar', false, true, true, '03541452271', false],
            ['ar', false, true, false, '297154751989', true],
            ['ar', true, false, true, '297154751989', false],
            ['ec', true, false, false, '026041923', true],
            ['ec', false, true, true, '026041923', false],
        ];
    }
}
