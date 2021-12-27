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
     * @var bool
     */
    public $isHeadless = false;

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

        if (isset($values['isHeadless']) && \is_bool($values['isHeadless'])) {
            $this->isHeadless = $values['isHeadless'];
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->tableName;
    }
}
