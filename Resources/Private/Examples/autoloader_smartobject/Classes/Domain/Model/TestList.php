<?php
/**
 * TestList Model
 *
 * @category Extension
 * @package  AutoloaderSmartobject
 * @author   Carsten Biebricher
 */

namespace HDNET\AutoloaderSmartobject\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * TestList Model
 * Container for the test-model.
 *
 * @author Carsten Biebricher
 * @db
 */
class TestList extends AbstractEntity {

	/**
	 * Example list
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\HDNET\AutoloaderSmartobject\Domain\Model\Test>
	 * @db
	 */
	protected $list;

	/**
	 * set the list
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $list
	 */
	public function setList($list) {
		$this->list = $list;
	}

	/**
	 * Get the list
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
	 */
	public function getList() {
		return $this->list;
	}
}