<?php

declare(strict_types=1);

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
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if (isset($values['config']) && \is_string($values['config'])) {
            $this->config = $values['config'];
        } elseif (isset($values['value']) && \is_string($values['value'])) {
            $this->config = $values['value'];
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->config;
    }
}
