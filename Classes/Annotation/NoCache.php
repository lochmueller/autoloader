<?php

declare(strict_types = 1);

namespace HDNET\Autoloader\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class NoCache
{
    /**
     * @var string
     */
    public $argumentName;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->argumentName = $values['value'];
        } elseif (isset($values['argumentName'])) {
            $this->argumentName = $values['argumentName'];
        }
    }
}
