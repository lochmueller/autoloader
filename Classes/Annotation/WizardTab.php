<?php

declare(strict_types = 1);

namespace HDNET\Autoloader\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class WizardTab
{
    /**
     * @var string
     */
    public $config;

    /**
     * @param array $values
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if (\is_string($values['config'])) {
            $this->config = $values['config'];
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->config;
    }
}
