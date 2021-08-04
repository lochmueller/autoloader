<?php

declare(strict_types=1);

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
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if (\is_string($values['locations'])) {
            $this->locations[] = $values['locations'];
        } elseif (\is_array($values['locations'])) {
            $this->locations = $values['locations'];
        } elseif (\is_string($values['value'])) {
            $this->locations[] = $values['value'];
        } elseif (\is_array($values['value'])) {
            $this->locations = $values['value'];
        }
    }
}
