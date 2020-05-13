<?php

declare(strict_types = 1);

namespace HDNET\Autoloader\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class SmartExclude
{
    /**
     * @var array
     */
    public $excludes = [];

    /**
     * @param array $values
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if (\is_array($values['excludes'])) {
            $this->excludes = $values['excludes'];
        }
    }
}
