<?php

namespace Linio\Component\Input\Transformer;

class DateTimeTransformer implements TransformerInterface
{
    public function transform($value)
    {
        try {
            $date = new \DateTime($value);
        } catch (\Exception $e) {
            return null;
        }

        return $date;
    }
}
