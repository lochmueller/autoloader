<?php

/**
 * Advanced relation model.
 */
declare(strict_types = 1);

namespace HDNET\Autoloader\Domain\Model;

use HDNET\Autoloader\Annotation\DatabaseField;
use HDNET\Autoloader\Annotation\SmartExclude;

/**
 * Advanced relation model.
 *
 * Note copy the SmartExclude to your relation model
 *
 * @smartExclude EnableFields,Language,Workspaces
 */
abstract class AbstractAdvancedRelation extends AbstractSimpleRelation
{
    /**
     * Tablesnames.
     *
     * @DatabaseField(type="varchar", sql="varchar(60) DEFAULT '' NOT NULL")
     */
    protected $tablenames;

    /**
     * Sorting foreign.
     *
     * @DatabaseField(type="string")
     */
    protected $sortingForeign;

    /**
     * Ident.
     *
     * @DatabaseField(type="varchar", sql="varchar(30) DEFAULT '0' NOT NULL")
     */
    protected $ident;
}
