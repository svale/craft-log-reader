<?php
/**
 * Log Reader plugin for Craft CMS 3.x
 *
 * Control Panel log file reader utility
 *
 * @link      https://github.com/svale
 * @copyright Copyright (c) 2021 Svale Fossåskaret
 */

namespace svale\logreadertests\unit;

use Codeception\Test\Unit;
use UnitTester;
use Craft;
use svale\logreader\LogReader;

/**
 * ExampleUnitTest
 *
 *
 * @author    Svale Fossåskaret
 * @package   LogReader
 * @since     1.0.0
 */
class ExampleUnitTest extends Unit
{
    // Properties
    // =========================================================================

    /**
     * @var UnitTester
     */
    protected $tester;

    // Public methods
    // =========================================================================

    // Tests
    // =========================================================================

    /**
     *
     */
    public function testPluginInstance()
    {
        $this->assertInstanceOf(
            LogReader::class,
            LogReader::$plugin
        );
    }

    /**
     *
     */
    public function testCraftEdition()
    {
        Craft::$app->setEdition(Craft::Pro);

        $this->assertSame(
            Craft::Pro,
            Craft::$app->getEdition()
        );
    }
}
