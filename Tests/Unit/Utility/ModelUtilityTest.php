<?php

namespace HDNET\Autoloader\Tests\Unit\Utility;

use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * ModelUtilityTest
 */
class ModelUtilityTest extends UnitTestCase
{
    /**
     * @test
     */
    public function getTableNameByModelName()
    {
        $input = [
            'TEST\\Name\\Domain\\Model\\Name',
            'TYPO3\\CMS\\Name\\Domain\\Model\\Name',
            'TEST\\Name\\Domain\\Model\\Name\\Deeper\\Deeper',
        ];
        $output = [
            'tx_name_domain_model_name',
            'tx_name_domain_model_name',
            'tx_name_domain_model_name_deeper_deeper',
        ];

        foreach ($input as $key => $value) {
            $result = \HDNET\Autoloader\Utility\ModelUtility::getTableNameByModelName($value);
            $this->assertEquals($output[$key], $result);
        }
    }
}
