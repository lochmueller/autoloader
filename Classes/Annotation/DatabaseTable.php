<?php

declare(strict_types = 1);

namespace HDNET\Autoloader\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class DatabaseTable
{
    /**
     * @var string
     */
    public $tableName;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if (\is_string($values['value'])) {
            $this->tableName = $values['value'];
        }
    }

    public function __toString()
    {
        return (string)$this->tableName;
    }
}
