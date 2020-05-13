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
        if (isset($values['parentClass'])) {
            $this->parentClass = (string)$values['parentClass'];
        } elseif (isset($values['value'])) {
            $this->parentClass = (string)$values['value'];
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->parentClass;
    }
}
