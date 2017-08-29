<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class DateTime extends Constraint
{
    public function validate($content): bool
    {
        if (!is_string($content)) {
            return false;
        }

        $date = date_parse($content);

        return $date['error_count'] ? false : true;
    }
}
