<?php

namespace HDNET\Autoloader\Tests\Unit\Utility;

use HDNET\Autoloader\Controller\ContentController;
use HDNET\Autoloader\Loader\AbstractServerLoader;
use HDNET\Autoloader\LoaderInterface;
use HDNET\Autoloader\Tests\Unit\AbstractTest;
use HDNET\Autoloader\Utility\ReflectionUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * ReflectionUtilityTest.
 */
class ReflectionUtilityTest extends AbstractTest
{
    /**
     * @test
     * @dataProvider classHierarchyProvider
     */
    public function testIsClassInOtherClassHierarchy($searchClass, $checkedClass, $result)
    {
        $res = ReflectionUtility::isClassInOtherClassHierarchy($searchClass, $checkedClass);
        $this->assertEquals($result, $res, 'Check ' . $searchClass . ' in ' . $checkedClass);
    }

    /**
     * @test
     */
    public function testIsInstantiable()
    {
        $this->assertEquals(true, ReflectionUtility::isInstantiable(ContentController::class), 'Check ' . ContentController::class);
        $this->assertEquals(false, ReflectionUtility::isInstantiable(AbstractServerLoader::class), 'Check ' . AbstractServerLoader::class);
        $this->assertEquals(false, ReflectionUtility::isInstantiable(LoaderInterface::class), 'Check ' . LoaderInterface::class);
    }

    public function classHierarchyProvider(): array
    {
        return [
            [ContentController::class, ActionController::class, true],
            [ContentController::class, ContentController::class, true],
            [ContentController::class, \Exception::class, false],
        ];
    }
}
