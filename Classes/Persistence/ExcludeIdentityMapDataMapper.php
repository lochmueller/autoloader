<?php
/**
 * Exclude the IdentityMap in the regular data map for backend selection
 * We need on object in different languages and the IdentityMap do not respect that!
 *
 * @package Autoloader\Persistence
 * @author  Tim Lochmüller
 */
namespace HDNET\Autoloader\Persistence;

use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * Exclude the IdentityMap in the regular data map for backend selection
 * We need on object in different languages and the IdentityMap do not respect that!
 *
 * @author Tim Lochmüller
 */
class ExcludeIdentityMapDataMapper extends DataMapper {

	/**
	 * Maps a single row on an object of the given class
	 *
	 * @param string $className The name of the target class
	 * @param array  $row       A single array with field_name => value pairs
	 *
	 * @return object An object of the given class
	 */
	protected function mapSingleRow($className, array $row) {
		/** @var \TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface $object */
		$object = $this->createEmptyObject($className);
		$this->identityMap->registerObject($object, $row['uid']);
		$this->thawProperties($object, $row);
		$object->_memorizeCleanState();
		$this->persistenceSession->registerReconstitutedEntity($object);
		return $object;
	}
}
