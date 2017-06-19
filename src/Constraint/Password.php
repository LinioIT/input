<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class Password extends Constraint
{
    /**
     * @var int
     */
    protected $minLength;

    /**
     * @var int
     */
    protected $maxLength;

    /**
     * @var bool
     */
    protected $mustHaveUpperCase;

    /**
     * @var bool
     */
    protected $mustHaveLowerCase;

    /**
     * @var bool
     */
    protected $mustHaveDigits;

    /**
     * @var bool
     */
    protected $mustHaveSymbols;

    /**
     * @var bool
     */
    protected $mustHaveDigitsOrSymbols;

    public function __construct(
        int $minLength,
        int $maxLength,
        bool $mustHaveUpperCase,
        bool $mustHaveLowerCase,
        bool $mustHaveDigits,
        bool $mustHaveSymbols,
        bool $mustHaveDigitsOrSymbols,
        string $errorMessage = null)
    {
        $this->setErrorMessage($errorMessage ?? 'Invalid password format');
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
        $this->mustHaveUpperCase = $mustHaveUpperCase;
        $this->mustHaveLowerCase = $mustHaveLowerCase;
        $this->mustHaveDigits = $mustHaveDigits;
        $this->mustHaveSymbols = $mustHaveSymbols;
        $this->mustHaveDigitsOrSymbols = $mustHaveDigitsOrSymbols;
    }

    public function validate($content): bool
    {
        if (strlen($content) < $this->minLength || strlen($content) > $this->maxLength) {
            return false;
        }

        $hasUpperCase = (bool) preg_match('/[A-Z]/', $content);
        $hasLowerCase = (bool) preg_match('/[a-z]/', $content);
        $hasDigits = (bool) preg_match('/[0-9]/', $content);
        $hasSymbols = (bool) preg_match('/[^A-Za-z0-9]/', $content);

        if ($this->mustHaveUpperCase && !$hasUpperCase) {
            return false;
        }

        if ($this->mustHaveLowerCase && !$hasLowerCase) {
            return false;
        }

        if ($this->mustHaveDigits && !$hasDigits) {
            return false;
        }

        if ($this->mustHaveSymbols && !$hasSymbols) {
            return false;
        }

        if ($this->mustHaveDigitsOrSymbols && (!$hasDigits && !$hasSymbols)) {
            return false;
        }

        return true;
    }
}
