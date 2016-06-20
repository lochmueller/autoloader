<?php
/**
 * JsonServerTest
 */

namespace HDNET\Autoloader\Tests\Unit\Service;

use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * SoapServerTest
 */
class JsonServerTest extends UnitTestCase
{

    /**
     * @test
     */
    public function foreignFrameworkIsLoaded()
    {
        $this->assertSame(true, class_exists('Zend\\Json\\Server\\Server'));
    }

}
