<?php

/**
 * Tca UserFunctions.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\UserFunctions;

/**
 * Tca UserFunctions.
 */
class Tca
{
    /**
     * Generate the help message for array fields.
     *
     * @param mixed[] $configuration
     */
    public function arrayInfoField(array $configuration, object $formEngine): string
    {
        return $this->generateGenericRelationMessage($configuration);
    }

    /**
     * Generate the help message for object storage fields.
     *
     * @param mixed[] $configuration
     */
    public function objectStorageInfoField(array $configuration, object $formEngine): string
    {
        return $this->generateGenericRelationMessage($configuration);
    }

    /**
     * Generate the help message for model fields.
     *
     * @param mixed[] $configuration
     */
    public function modelInfoField(array $configuration, object $formEngine): string
    {
        return $this->generateGenericRelationMessage($configuration);
    }

    /**
     * Get a generic text for an info box.
     *
     * @param mixed[] $configuration
     */
    protected function generateGenericRelationMessage(array $configuration): string
    {
        $infoField = '<strong>Please configure your TCA for this field.</strong><br/>';
        $infoField .= 'You see this message because you have NOT configured the TCA.';
        $infoField .= '<ul><li>table: <em>' . $configuration['table'] . '</em></li>';
        $infoField .= '<li>field: <em>' . $configuration['field'] . '</em></li>';
        $infoField .= '<li>config-file';
        $infoField .= '<ul><li>own table: <em>Configuration/TCA/' . $configuration['table'] . '.php</em></li>';
        $infoField .= '<li>foreign table: <em>Configuration/TCA/Overrides/' . $configuration['table'] . '.php</em></li></ul>';
        $infoField .= '</li></ul>';
        $infoField .= 'Common foreign tables are <em>tt_content</em>, <em>tt_address</em>, &hellip;.<br/><br/>';
        $infoField .= 'Information about proper TCA configuration as ';
        $infoField .= '<a href="https://docs.typo3.org/typo3cms/TCAReference/ColumnsConfig/Type/Group.html" target="_blank">group</a>, ';
        $infoField .= '<a href="https://docs.typo3.org/typo3cms/TCAReference/ColumnsConfig/Type/Inline.html" target="_blank">inline</a> or ';
        $infoField .= '<a href="https://docs.typo3.org/typo3cms/TCAReference/ColumnsConfig/Type/Select.html" target="_blank">select</a>';
        $infoField .= '-type can be found in the TCA-documentation.<br/>';

        return $this->wrapInInfoBox($infoField);
    }

    /**
     * Wrap the given content in a info box for the backend.
     */
    protected function wrapInInfoBox(string $content): string
    {
        return '<div style="padding: 5px; border: 2px solid red;">' . $content . '</div>';
    }
}
