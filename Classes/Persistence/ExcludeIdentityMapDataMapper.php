<?php

/**
 * Exclude the IdentityMap in the regular data map for backend selection
 * We need on object in different languages and the IdentityMap do not respect that!
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Persistence;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * Exclude the IdentityMap in the regular data map for backend selection
 * We need on object in different languages and the IdentityMap do not respect that!
 */
class ExcludeIdentityMapDataMapper extends DataMapper
{
    /**
     * Maps a single row on an object of the given class.
     *
     * @param string $className The name of the target class
     * @param array  $row       A single array with field_name => value pairs
     *
     * @return object An object of the given class
     */
    protected function mapSingleRow(string $className, array $row): AbstractEntity
    {
        /** @var AbstractEntity $object */
        $object = $this->createEmptyObject($className);
        $this->persistenceSession->registerObject($object, $row['uid']);
        $this->thawProperties($object, $row);
        $object->_memorizeCleanState();
        $this->persistenceSession->registerReconstitutedEntity($object);

        return $object;
    }
}
