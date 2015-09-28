<?php
/**
 * Test service
 *
 * @author  Tim Lochmüller
 */

namespace HDNET\AutoloaderSoap\Service\Soap;

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Test service
 */
class TestService implements SingletonInterface
{

    /**
     * Test function
     *
     * @param string $test
     *
     * @return int
     */
    public function testFunction($test)
    {
        return 1;
    }

}
