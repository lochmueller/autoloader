<?php

declare(strict_types=1);

namespace HDNET\Autoloader\Listener;

use HDNET\Autoloader\SmartObjectManager;
use TYPO3\CMS\Core\Database\Event\AlterTableDefinitionStatementsEvent;

class DatabaseListener
{
    public function triggerEvent(AlterTableDefinitionStatementsEvent $event): AlterTableDefinitionStatementsEvent
    {
        $sqlStrings = SmartObjectManager::getSmartObjectRegisterSql();
        $before = $event->getSqlData();
        $event->setSqlData(array_merge($before, $sqlStrings));

        return $event;
    }
}
