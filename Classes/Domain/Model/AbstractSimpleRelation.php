<?php

/**
 * Simple relation model.
 */
declare(strict_types = 1);

namespace HDNET\Autoloader\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Simple relation model.
 *
 * Note copy the SmartExclude to your relation model
 *
 * @smartExclude EnableFields,Language,Workspaces
 */
abstract class AbstractSimpleRelation extends AbstractEntity
{
    /**
     * Local UID.
     *
     * @db
     */
    protected $uidLocal;

    /**
     * Foreign UID.
     *
     * @db
     */
    protected $uidForeign;

    /**
     * Sorting.
     *
     * @db
     */
    protected $sorting;
}
