<?php
/**
 * Mapper for variables types to TCA and DB information
 *
 * @category   Extension
 * @package    Autoloader
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */

namespace HDNET\Autoloader;

use HDNET\Autoloader\Utility\ExtendedUtility;

/**
 * Mapper for variables types to TCA and DB information
 *
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */
class Mapper implements SingletonInterface {

	/**
	 * Get the TCA configuration for the current type
	 *
	 * @param string $type
	 * @param string $fieldName
	 * @param bool   $overWriteLabel
	 *
	 * @return array
	 */
	public function getTcaConfiguration($type, $fieldName, $overWriteLabel = FALSE) {
		try {
			$mapper = $this->getMapperByType($type);
		} catch (Exception $exception) {
			// always return a valid mapper
			$mapper = $this->getMapperByType('String');
		}
		return $mapper->getTcaConfiguration($fieldName, $overWriteLabel);
	}

	/**
	 * Get the database definition for the current mapper
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	public function getDatabaseDefinition($type) {
		$mapper = $this->getMapperByType($type);
		return $mapper->getDatabaseDefinition();
	}

	/**
	 * Get a valid mapper for the given type
	 *
	 * @param string $type
	 *
	 * @return MapperInterface
	 * @throws \Exception
	 */
	protected function getMapperByType($type) {
		$mappers = $this->getMappers();
		foreach ($mappers as $mapper) {
			/** @var $mapper MapperInterface */
			if ($mapper->canHandleType($type)) {
				return $mapper;
			}
		}
		throw new Exception('No valid mapper for the given type found: ' . $type, 12371263136);
	}

	/**
	 * Get all mappers
	 *
	 * @return array
	 */
	protected function getMappers() {
		$mapper = array(
			'Boolean',
			'Float',
			'DateTime',
			'FileReference',
			'FileReferenceObjectStorage',
			'ObjectStorage',
			'Int',
			'String',
			'Model',
		);
		foreach ($mapper as $key => $value) {
			$mapper[$key] = ExtendedUtility::create('HDNET\\Autoloader\\Mapper\\' . $value);
		}
		return $mapper;
	}

} 