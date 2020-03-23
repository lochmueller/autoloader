<?php

declare(strict_types = 1);

namespace HDNET\Autoloader\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class DatabaseKey
{
    /**
     * @var string
     */
    public $key;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->key = $values['value'];
        } elseif (isset($values['argumentName'])) {
            $this->key = $values['argumentName'];
        }
    }

    public function __toString()
    {
        return (string)$this->key;
    }
}
