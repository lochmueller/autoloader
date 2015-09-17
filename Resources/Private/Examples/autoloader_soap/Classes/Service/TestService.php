<?php
/**
 * Test service
 *
 * @author  Tim Lochmüller
 */

namespace HDNET\AutoloaderSoap\Service;

/**
 * Test service
 *
 * @soapServer test
 */
class TestService
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
