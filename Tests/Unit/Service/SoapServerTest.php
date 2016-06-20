<?php
/**
 * SoapServerTest
 */

namespace HDNET\Autoloader\Tests\Unit\Service;

use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * SoapServerTest
 */
class SoapServerTest extends UnitTestCase
{

    /**
     * @test
     */
    public function foreignFrameworkIsLoaded()
    {
        $this->assertSame(true, class_exists('WSDL\\WSDLCreator'));
    }

}
