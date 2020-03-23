<?php

declare(strict_types = 1);

namespace HDNET\Autoloader\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class ParentClass
{
    /**
     * @var string
     */
    public $parentClass;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->parentClass = (string)$values['value'];
        }
    }

    public function __toString()
    {
        return (string)$this->parentClass;
    }
}
