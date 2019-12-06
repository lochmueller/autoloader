<?php

/**
 * Map property names to DB field values.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Map property names to DB field values.
 */
class NameMapperService
{
    /**
     * Core fields that are stored as camel case in the DB
     * So the field is mapped directly.
     *
     * @var array
     */
    protected $coreCamelCaseField = [
        'tt_content' => [
            'CType',
            'rowDescription',
            'spaceBefore',
            'spaceAfter',
            'colPos',
            'sectionIndex',
            'linkToTop',
        ],
        'sys_template' => [
            'nextLevel',
            'basedOn',
            'includeStaticAfterBasedOn',
        ],
        'sys_domain' => [
            'redirectHttpStatusCode',
            'redirectTo',
            'domainName',
        ],
        'fe_users' => [
            'lockToDomain',
            'TSconfig',
        ],
        'fe_groups' => [
            'lockToDomain',
            'TSconfig',
        ],
        'be_groups' => [
            'groupMods',
            'lockToDomain',
            'TSconfig',
        ],
        'be_users' => [
            'realName',
            'userMods',
            'lockToDomain',
            'disableIPlock',
            'TSconfig',
            'lastlogin',
            'createdByAction',
        ],
        'pages' => [
            'TSconfig',
            'lastUpdated',
            'newUntil',
            'SYS_LASTCHANGED',
            'extendToSubpages',
        ],
        'sys_log' => [
            'NEWid',
            'IP',
        ],
    ];

    /**
     * Get the right DB representation and respect camelCase field of the core.
     */
    public function getDatabaseFieldName(string $tableName, string $propertyName): string
    {
        if (isset($this->coreCamelCaseField[$tableName]) && \in_array($propertyName, $this->coreCamelCaseField[$tableName], true)) {
            return $propertyName;
        }

        return GeneralUtility::camelCaseToLowerCaseUnderscored($propertyName);
    }
}
