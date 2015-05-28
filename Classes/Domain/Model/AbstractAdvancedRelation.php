<?php
/**
 * Advanced relation model
 *
 * @category Extension
 * @package  Autoloader
 * @author   Tim Lochmüller
 */

namespace HDNET\Autoloader\Domain\Model;

/**
 * Advanced relation model
 *
 * Note copy the SmartExclude to your relation model
 *
 * @smartExclude EnableFields,Language,Workspaces
 */
abstract class AbstractAdvancedRelation extends AbstractSimpleRelation {

	/**
	 * Tablesnames
	 *
	 * @var string
	 * @db varchar(60) DEFAULT '' NOT NULL
	 */
	protected $tablenames;

	/**
	 * Sorting foreign
	 *
	 * @var int
	 * @db
	 */
	protected $sortingForeign;

	/**
	 * Ident
	 *
	 * @var string
	 * @db varchar(30) DEFAULT '' NOT NULL
	 */
	protected $ident;

}