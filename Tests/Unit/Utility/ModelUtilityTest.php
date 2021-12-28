<?php

namespace HDNET\Autoloader\Tests\Unit\Utility;

use HDNET\Autoloader\Utility\ModelUtility;
use HDNET\Autoloader\Tests\Unit\AbstractTest;

/**
 * ModelUtilityTest.
 */
class ModelUtilityTest extends AbstractTest
{
    /**
     * @test
     */
    public function getTableNameByModelName(): void
    {
        $input = [
            'TEST\\Name\\Domain\\Model\\Name',
            'TEST\\Name\\Domain\\Model\\Content\\ContentName',
            'TYPO3\\CMS\\Name\\Domain\\Model\\Name',
            'TEST\\Name\\Domain\\Model\\Name\\Deeper\\Deeper',
        ];
        $output = [
            'tx_name_domain_model_name',
            'tx_name_domain_model_content_contentname',
            'tx_name_domain_model_name',
            'tx_name_domain_model_name_deeper_deeper',
        ];

        foreach ($input as $key => $value) {
            $result = ModelUtility::getTableNameByModelName($value);
            $this->assertEquals($output[$key], $result);
        }
    }
}
