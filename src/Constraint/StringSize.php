<?php

namespace Linio\Component\Input\Constraint;

class StringSize implements ConstraintInterface
{
    /**
     * @var int
     */
    protected $minSize;

    /**
     * @var int
     */
    protected $maxSize;

    /**
     * @param int $minSize
     * @param int $maxSize
     */
    public function __construct($minSize, $maxSize = PHP_INT_MAX)
    {
        $this->minSize = $minSize;
        $this->maxSize = $maxSize;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($content)
    {
        $size = strlen($content);

        return $size >= $this->minSize && $size <= $this->maxSize;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorMessage()
    {
        return sprintf('Content out of min/max limit sizes [%s, %s]', $this->minSize, $this->maxSize);
    }
}
