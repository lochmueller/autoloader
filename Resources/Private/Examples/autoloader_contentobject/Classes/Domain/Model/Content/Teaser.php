<?php
/**
 * Example Teaser Model
 *
 * @category   Extension
 * @package    AutoloaderContentobject
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */

namespace HDNET\AutoloaderContentobject\Domain\Model\Content;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Example Teaser Model
 *
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 * @db         tt_content
 */
class Teaser extends AbstractEntity {

	/**
	 * Bodytext (already exists, so no db annotation)
	 *
	 * @var string
	 */
	protected $bodytext;

	/**
	 * A complete new field incl. the db annotation
	 *
	 * @var string
	 * @db
	 */
	protected $newField;

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
	 * Set new Field
	 *
	 * @param string $newField
	 */
	public function setNewField($newField) {
		$this->newField = $newField;
	}

	/**
	 * Get new Field
	 *
	 * @return string
	 */
	public function getNewField() {
		return $this->newField;
	}

} 