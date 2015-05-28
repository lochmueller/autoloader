<?php
/**
 * Content Model
 *
 * Example for an existing object
 *
 * @category   Extension
 * @package    AutoloaderSmartobject
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */

namespace HDNET\AutoloaderSmartobject\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Content Model
 *
 * Example for an existing object
 *
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 * @db         tt_content
 */
class Content extends AbstractEntity {

	/**
	 * Bodytext
	 *
	 * @var string
	 */
	protected $bodytext;

	/**
	 * Other field (RTE)
	 *
	 * @var string
	 * @enableRichText
	 * @db
	 */
	protected $otherField;

	/**
	 * Foreign model
	 *
	 * @var \HDNET\AutoloaderSmartobject\Domain\Model\Test
	 * @db
	 */
	protected $foreignModelWithoutSpecificDbAnnotation;

	/**
	 * Set bodytext
	 *
	 * @param string $bodytext
	 */
	public function setBodytext($bodytext) {
		$this->bodytext = $bodytext;
	}

	/**
	 * Get bodytext
	 *
	 * @return string
	 */
	public function getBodytext() {
		return $this->bodytext;
	}

	/**
	 * Set other field
	 *
	 * @param string $otherField
	 */
	public function setOtherField($otherField) {
		$this->otherField = $otherField;
	}

	/**
	 * Get other field
	 *
	 * @return string
	 */
	public function getOtherField() {
		return $this->otherField;
	}

	/**
	 * Set the foreign model
	 *
	 * @param \HDNET\AutoloaderSmartobject\Domain\Model\Test $foreignModelWithoutSpecificDbAnnotation
	 */
	public function setForeignModelWithoutSpecificDbAnnotation($foreignModelWithoutSpecificDbAnnotation) {
		$this->foreignModelWithoutSpecificDbAnnotation = $foreignModelWithoutSpecificDbAnnotation;
	}

	/**
	 * get the foreign model
	 *
	 * @return \HDNET\AutoloaderSmartobject\Domain\Model\Test
	 */
	public function getForeignModelWithoutSpecificDbAnnotation() {
		return $this->foreignModelWithoutSpecificDbAnnotation;
	}
} 