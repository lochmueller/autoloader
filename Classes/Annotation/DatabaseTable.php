<?php

declare(strict_types=1);

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
        if (isset($values['tableName']) && \is_string($values['tableName'])) {
            $this->tableName = $values['tableName'];
        } elseif (isset($values['value']) && \is_string($values['value'])) {
            $this->tableName = $values['value'];
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->tableName;
    }
}
