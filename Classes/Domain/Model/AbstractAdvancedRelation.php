<?php

/**
 * Advanced relation model.
 */
declare(strict_types = 1);

namespace HDNET\Autoloader\Domain\Model;

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
     * @db varchar(60) DEFAULT '' NOT NULL
     */
    protected $tablenames;

    /**
     * Sorting foreign.
     *
     * @db
     */
    protected $sortingForeign;

    /**
     * Ident.
     *
     * @db varchar(30) DEFAULT '' NOT NULL
     */
    protected $ident;
}
