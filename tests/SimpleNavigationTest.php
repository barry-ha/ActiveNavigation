<?php
/**
 * LOCAL: How to run unit test
 *      > W:
 *      > CD \gwrfinancial\bootstrap\SimpleNavigation\tests
 *      > php phpunit-9.5.9.phar SimpleNavigationTest.php
 */

use PHPUnit\Framework\TestCase;
require('../lib/JVS/SimpleNavigation.php');
//require('../lib/JVS/SimpleNavigation.JVS.php');
echo "---Start test [line " . __LINE__ . "]" . PHP_EOL;

/**
 * SimpleNavigation tests
 *
 * @author Javier Villanueva <info@jvsoftware.com>
 */
class SimpleNavigationTest extends PHPUnit\Framework\TestCase
{
    /**
     * SimpleNavigation instance
     *
     * @var SimpleNavigation
     */
    protected $simpleNavigation;

    public function setUp() : void  // is called before each test runs
    {
        $this->simpleNavigation = new JVS\SimpleNavigation;
    }

    /**
     * Make sure class can be instantiated
     *
     * @return void
     */
    public function testCanInitClass()
    {
        $this->assertInstanceOf('JVS\SimpleNavigation', $this->simpleNavigation);
    }

    /**
     * Make sure it can render navigation without specified links
     *
     * @return void
     */
    public function testCanRenderUnlinkedNavigation()
    {
        $simpleItems = array('Home', 'Blog', 'About');

        $htmlMenu  = '<ul>';
        $htmlMenu .= '<li><a href="#">Home</a></li>';
        $htmlMenu .= '<li><a href="#">Blog</a></li>';
        $htmlMenu .= '<li><a href="#">About</a></li>';
        $htmlMenu .= '</ul>';

        $expectedDom = new \DomDocument;
        $expectedDom->loadHtml($htmlMenu);
        $expectedDom->preservewhitespace = false;

        $actualDom = new \DomDocument();
        $actualDom->loadHtml($this->simpleNavigation->make($simpleItems));
        $actualDom->preservewhitespace = false;

        $this->assertXmlStringEqualsXmlString($expectedDom->saveHTML(), $actualDom->saveHTML());
    }

    /**
     * Make sure it can render navigation with specified links
     *
     * @return void
     */
    public function testCanRenderLinkedMenu()
    {
        $linkedItems = array(
            'Home'  => 'http://home.com',
            'Blog'  => 'http://blog.com',
            'About' => 'http://about.com',
        );

        $htmlMenu  = '<ul>';
        $htmlMenu .= '<li><a href="http://home.com">Home</a></li>';
        $htmlMenu .= '<li><a href="http://blog.com">Blog</a></li>';
        $htmlMenu .= '<li><a href="http://about.com">About</a></li>';
        $htmlMenu .= '</ul>';

        $expectedDom = new \DomDocument;
        $expectedDom->loadHtml($htmlMenu);
        $expectedDom->preservewhitespace = false;

        $actualDom = new \DomDocument();
        $actualDom->loadHtml($this->simpleNavigation->make($linkedItems));
        $actualDom->preservewhitespace = false;

        $this->assertXmlStringEqualsXmlString($expectedDom->saveHTML(), $actualDom->saveHTML());
    }

    /**
     * Make sure it can render multi-level navigation
     * 
     * PHP Fatal error:  
     * Declaration of          PHPUnit_Framework_Comparator_DOMDocument::assertEquals($expected, $actual, $delta = 0, $canonicalize = false, $ignoreCase = false) 
     * must be compatible with PHPUnit_Framework_Comparator_Object     ::assertEquals($expected, $actual, $delta = 0, $canonicalize = false, $ignoreCase = false, array &$processed = Array) 
     * in /home/travis/build/barry-ha/SimpleNavigation/vendor/phpunit/phpunit/PHPUnit/Framework/Comparator/DOMDocument.php on line 114
     *
     * @return void
     */
    public function testCanRenderMultiLevelMenu()
    {
        $multiLevelItems = array(
            'Home'  => 'http://home.com',
            'Blog'  => 'http://blog.com',
            'About' => array(
                'About 1' => 'http://about1.com',
                'About 2' => 'http://about2.com',
            ),
        );

        $htmlMenu  = '<ul>';
        $htmlMenu .= '<li><a href="http://home.com">Home</a></li>';
        $htmlMenu .= '<li><a href="http://blog.com">Blog</a></li>';
        $htmlMenu .= '<li><a href="#">About</a>';
        $htmlMenu .= '<ul>';
        $htmlMenu .= '<li><a href="http://about1.com">About 1</a></li>';
        $htmlMenu .= '<li><a href="http://about2.com">About 2</a></li>';
        $htmlMenu .= '</ul>';     // Note: DOM will auto-close tags
        $htmlMenu .= '</li>';     // this test will pass whether or not 
        $htmlMenu .= '</ul>';     //         these closing lines are here

        $expectedDom = new \DomDocument;
        $expectedDom->loadHtml($htmlMenu);
        $expectedDom->preservewhitespace = false;

        $actualDom = new \DomDocument();
        $actualMenu = $this->simpleNavigation->make($multiLevelItems);
        $actualDom->loadHtml($actualMenu);
        $actualDom->preservewhitespace = false;

        $this->assertXmlStringEqualsXmlString($expectedDom->saveHTML(), $actualDom->saveHTML());

        //$this->assertSame($htmlMenu, $actualMenu);
    }
}
