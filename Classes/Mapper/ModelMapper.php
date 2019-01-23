<?php

/**
 * Map general Models.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Mapper;

use HDNET\Autoloader\MapperInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Map general Models.
 */
class ModelMapper implements MapperInterface
{
    /**
     * Check if the current mapper can handle the given type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function canHandleType($type)
    {
        $type = \trim($type, '\\');
        if (!\class_exists($type)) {
            return false;
        }
        $abstractEntity = \trim(AbstractEntity::class, '\\');
        try {
            if ($type === $abstractEntity) {
                return true;
            }
            $reflection = new \ReflectionClass($type);
            while ($reflection = $reflection->getParentClass()) {
                if ($abstractEntity === \trim($reflection->getName(), '\\')) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Get the TCA configuration for the current type.
     *
     * @param string $fieldName
     * @param bool   $overWriteLabel
     *
     * @return array
     */
    public function getTcaConfiguration($fieldName, $overWriteLabel = false)
    {
        $baseConfig = [
            'type' => 'user',
            'userFunc' => 'HDNET\\Autoloader\\UserFunctions\\Tca->modelInfoField',
        ];

        return [
            'exclude' => 1,
            'label' => $overWriteLabel ? $overWriteLabel : $fieldName,
            'config' => $baseConfig,
        ];
    }

    /**
     * Get the database definition for the current mapper.
     *
     * @return string
     */
    public function getDatabaseDefinition()
    {
        return 'int(11) DEFAULT \'0\' NOT NULL';
    }
}
