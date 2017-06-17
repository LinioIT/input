<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class UuidValue extends GuidValue
{
    const ERROR_MESSAGE = 'Invalid UUID format';
}
