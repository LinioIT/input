<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;

class PhoneNumber extends Constraint
{
    /**
     * @var string
     */
    protected $countryCode;

    /**
     * @var bool
     */
    protected $allowFixedLine;

    /**
     * @var bool
     */
    protected $allowMobile;

    /**
     * @var bool
     */
    protected $allowInternational;

    public function __construct(
        string $countryCode,
        bool $allowFixedLine = true,
        bool $allowMobile = true,
        bool $allowInternational = true,
        string $errorMessage = null
    )
    {
        $this->countryCode = strtoupper($countryCode);
        $this->allowFixedLine = $allowFixedLine;
        $this->allowMobile = $allowMobile;
        $this->allowInternational = $allowInternational;
        $this->setErrorMessage($errorMessage ?? 'Invalid phone number');
    }

    public function validate($content): bool
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $phoneNumber = $phoneUtil->parse($content, $this->countryCode);
        } catch (NumberParseException $exception) {
            return false;
        }

        if (
            !$phoneUtil->isValidNumber($phoneNumber)
            && !in_array($phoneUtil->getNumberType($phoneNumber), [PhoneNumberType::FIXED_LINE, PhoneNumberType::MOBILE])
        ) {
            return false;
        }

        if (!$this->allowFixedLine && $phoneUtil->getNumberType($phoneNumber) == PhoneNumberType::FIXED_LINE) {
            return false;
        }

        if (!$this->allowMobile && $phoneUtil->getNumberType($phoneNumber) == PhoneNumberType::MOBILE) {
            return false;
        }

        if (!$this->allowInternational && $phoneUtil->getRegionCodeForNumber($phoneNumber) != $this->countryCode) {
            return false;
        }

        return true;
    }
}
