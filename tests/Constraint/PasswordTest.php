<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    /**
     * @dataProvider passwordProvider
     */
    public function testIsCheckingPasswordComplexity(
        int $minLength,
        int $maxLength,
        bool $mustHaveUpperCase,
        bool $mustHaveLowerCase,
        bool $mustHaveDigits,
        bool $mustHaveSymbols,
        bool $mustHaveDigitsOrSymbols,
        string $content,
        bool $expectedResult
    )
    {
        $constraint = new Password($minLength, $maxLength, $mustHaveUpperCase, $mustHaveLowerCase, $mustHaveDigits, $mustHaveSymbols, $mustHaveDigitsOrSymbols);
        $this->assertEquals($expectedResult, $constraint->validate($content));
    }

    public function testIsGettingErrorMessage()
    {
        $constraint = new Password(1,2,true, true, true, true, true);
        $this->assertFalse($constraint->validate(''));
        $this->assertEquals('[field] Invalid password format', $constraint->getErrorMessage('field'));
    }

    public function testErrorMessageIsCustomizable()
    {
        $constraint = new Password(1,2,true, true, true, true, true, 'CUSTOM!');
        $this->assertSame('[field] CUSTOM!', $constraint->getErrorMessage('field'));
    }

    public function passwordProvider()
    {
        return [
            [6, 12, false, false, false, false, false, 'TestTest', true],
            [6, 12, false, false, false, false, false, 'Test', false],
            [6, 12, false, false, false, false, false, 'TestTestTestTest', false],
            [6, 12, true, false, false, false, false, 'TestTest', true],
            [6, 12, true, false, false, false, false, 'testtest', false],
            [6, 12, false, true, false, false, false, 'TestTest', true],
            [6, 12, false, true, false, false, false, 'TESTTEST', false],
            [6, 12, false, false, true, false, false, 'TestTest8', true],
            [6, 12, false, false, true, false, false, 'TestTest', false],
            [6, 12, false, false, false, true, false, 'TestTest;', true],
            [6, 12, false, false, false, true, false, 'TestTest', false],
            [6, 12, false, false, false, false, true, 'TestTest;', true],
            [6, 12, false, false, false, false, true, 'TestTest8', true],
            [6, 12, false, false, false, false, true, 'TestTest', false],
            [6, 12, true, true, true, true, true, 'TestTest8;', true],
        ];
    }
}
