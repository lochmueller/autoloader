<?php
/**
 * Test Model
 *
 * @category   Extension
 * @package    AutoloaderSmartobject
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */

namespace HDNET\AutoloaderSmartobject\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Test Model
 *
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 * @db
 */
class Test extends AbstractEntity {

	/**
	 * A basic field
	 *
	 * @var string
	 * @db
	 */
	protected $fieldName;

	/**
	 * A boolean
	 *
	 * @var bool
	 * @db
	 */
	protected $boolField;

	/**
	 * File example
	 *
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 * @db
	 */
	protected $file;

	/**
	 * Custom (variable that has a custom DB type)
	 *
	 * @var int
	 * @db int(11) DEFAULT '0' NOT NULL
	 */
	protected $customField;

	/**
	 * Set the basic field
	 *
	 * @param string $fieldName
	 */
	public function setFieldName($fieldName) {
		$this->fieldName = $fieldName;
	}

	/**
	 * Get the basic field
	 *
	 * @return string
	 */
	public function getFieldName() {
		return $this->fieldName;
	}

	/**
	 * Boolean
	 *
	 * @param bool $boolField
	 */
	public function setBoolField($boolField) {
		$this->boolField = $boolField;
	}

	/**
	 * Boolean
	 *
	 * @return bool
	 */
	public function getBoolField() {
		return $this->boolField;
	}

	/**
	 * Set custom field
	 *
	 * @param int $customField
	 */
	public function setCustomField($customField) {
		$this->customField = $customField;
	}

	/**
	 * Get custom field
	 *
	 * @return int
	 */
	public function getCustomField() {
		return $this->customField;
	}

	/**
	 * Set file
	 *
	 * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $file
	 */
	public function setFile($file) {
		$this->file = $file;
	}

	/**
	 * Get file
	 *
	 * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */
	public function getFile() {
		return $this->file;
	}
}