<?php

declare(strict_types=1);

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
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if (isset($values['excludes']) && \is_array($values['excludes'])) {
            $this->excludes = $values['excludes'];
        } elseif (isset($values['value']) && \is_array($values['value'])) {
            $this->excludes = $values['value'];
        }
    }
}
