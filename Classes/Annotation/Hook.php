<?php

declare(strict_types = 1);

namespace HDNET\Autoloader\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class Hook
{
    /**
     * @var array
     */
    public $locations = [];

    /**
     * @param array $values
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if (\is_string($values['locations'])) {
            $this->locations[] = $values['locations'];
        } elseif (\is_array($values['locations'])) {
            $this->locations = $values['locations'];
        }
    }
}
