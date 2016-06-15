<?php
/**
 * CamelCase element
 *
 * @category   Extension
 * @package    AutoloaderContentobject
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 */

namespace HDNET\AutoloaderContentobject\Domain\Model\Content;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * CamelCase element
 *
 * @author     Tim Lochmüller <tim@fruit-lab.de>
 * @db         tt_content
 * @wizardTab  common
 */
class ContentElement extends AbstractEntity
{

    /**
     * Bodytext (already exists, so no db annotation)
     *
     * @var string
     */
    protected $bodytext;

    /**
     * Test body with RTE
     *
     * @var string
     * @enableRichText
     */
    protected $newTestBodyWithRte;

    /**
     * Set bodytext
     *
     * @param string $bodytext
     */
    public function setBodytext($bodytext)
    {
        $this->bodytext = $bodytext;
    }

    /**
     * Get bodytext
     *
     * @return string
     */
    public function getBodytext()
    {
        return $this->bodytext;
    }

    /**
     * @return string
     */
    public function getNewTestBodyWithRte()
    {
        return $this->newTestBodyWithRte;
    }

    /**
     * @param string $newTestBodyWithRte
     */
    public function setNewTestBodyWithRte($newTestBodyWithRte)
    {
        $this->newTestBodyWithRte = $newTestBodyWithRte;
    }
}
