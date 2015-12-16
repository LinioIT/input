<?php
declare(strict_types=1);

namespace Linio\Component\Input\Transformer;

class DateTimeTransformer implements TransformerInterface
{
    public function transform($value)
    {
        try {
            $date = new \DateTime($value);
        } catch (\Exception $e) {
            return;
        }

        return $date;
    }
}
