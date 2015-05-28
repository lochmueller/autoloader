<?php
/**
 * Simple relation model
 *
 * @category Extension
 * @package  Autoloader
 * @author   Tim Lochmüller
 */

namespace HDNET\Autoloader\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Simple relation model
 *
 * Note copy the SmartExclude to your relation model
 *
 * @smartExclude EnableFields,Language,Workspaces
 */
abstract class AbstractSimpleRelation extends AbstractEntity {

	/**
	 * Local UID
	 *
	 * @var int
	 * @db
	 */
	protected $uidLocal;

	/**
	 * Foreign UID
	 *
	 * @var int
	 * @db
	 */
	protected $uidForeign;

	/**
	 * Sorting
	 *
	 * @var int
	 * @db
	 */
	protected $sorting;

}