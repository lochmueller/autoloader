<?php

/**
 * Simple relation model.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Domain\Model;

use HDNET\Autoloader\Annotation\DatabaseField;
use HDNET\Autoloader\Annotation\SmartExclude;
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
     * @var int
     * @DatabaseField(type="int")
     */
    protected $uidLocal = 0;

    /**
     * @var int
     * @DatabaseField(type="int")
     */
    protected $uidForeign = 0;

    /**
     * Sorting.
     *
     * @var int
     * @DatabaseField(type="int")
     */
    protected $sorting = 0;
}
