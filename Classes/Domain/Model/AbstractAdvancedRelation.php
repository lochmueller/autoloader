<?php

/**
 * Advanced relation model.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Domain\Model;

use HDNET\Autoloader\Annotation\DatabaseField;
use HDNET\Autoloader\Annotation\SmartExclude;

/**
 * Advanced relation model.
 *
 * Note copy the SmartExclude to your relation model
 *
 * @SmartExclude(excludes={"EnableFields","Language","Workspaces"})
 */
abstract class AbstractAdvancedRelation extends AbstractSimpleRelation
{
    /**
     * Tablesnames.
     *
     * @var string
     * @DatabaseField(type="varchar", sql="varchar(60) DEFAULT '' NOT NULL")
     */
    protected $tablenames = '';

    /**
     * Sorting foreign.
     *
     * @var int
     * @DatabaseField(type="int")
     */
    protected $sortingForeign = 0;

    /**
     * Ident.
     *
     * @var string
     * @DatabaseField(type="varchar", sql="varchar(30) DEFAULT '0' NOT NULL")
     */
    protected $ident = '';
}
