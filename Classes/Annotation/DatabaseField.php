<?php

declare(strict_types=1);

namespace HDNET\Autoloader\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class DatabaseField
{
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $sql;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if (isset($values['type'])) {
            $this->type = $values['type'];
        } elseif (isset($values['value'])) {
            $this->type = $values['value'];
        }
        if (isset($values['sql'])) {
            $this->sql = $values['sql'];
        }
    }
}
