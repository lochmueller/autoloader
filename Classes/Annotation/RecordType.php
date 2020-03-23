<?php

declare(strict_types = 1);

namespace HDNET\Autoloader\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class RecordType
{
    /**
     * @var string
     */
    public $recordType;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->recordType = (string)$values['value'];
        }
    }

    public function __toString()
    {
        return (string)$this->recordType;
    }
}
