<?php
/**
 * Test Model
 *
 * @category   Extension
 * @package    AutoloaderCsh
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */

namespace HDNET\AutoloaderCsh\Domain\Model;

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
	 * @param bool $boolField
	 */
	public function setBoolField($boolField) {
		$this->boolField = $boolField;
	}

	/**
	 * @return bool
	 */
	public function getBoolField() {
		return $this->boolField;
	}

	/**
	 * @param string $fieldName
	 */
	public function setFieldName($fieldName) {
		$this->fieldName = $fieldName;
	}

	/**
	 * @return string
	 */
	public function getFieldName() {
		return $this->fieldName;
	}



} 