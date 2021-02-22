<?php
/**
 * Log Reader plugin for Craft CMS 3.x
 *
 * Control Panel log file reader utility
 *
 * @link      https://github.com/svale
 * @copyright Copyright (c) 2021 Svale Fossåskaret
 */

namespace svale\logreader;

use svale\logreader\services\FileReader as FileReaderService;
use svale\logreader\models\Settings;
use svale\logreader\utilities\Log as LogUtility;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\services\Utilities;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use svale\logreader\models\LogContent;
use yii\base\Event;

/**
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Svale Fossåskaret
 * @package   LogReader
 * @since     1.0.0
 *
 * @property  FileReaderService $fileReader
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class LogReader extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * LogReader::$plugin
     *
     * @var LogReader
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = false;


    /**
     * @var Model|bool|null The model used to store the log file content
     * @see getLogContent()
     */
    private $_contentModel;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * LogReader::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'log-reader/log-view';
            }
        );

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'log-reader/log-view/do-something';
            }
        );

        // Register our utilities
        Event::on(
            Utilities::class,
            Utilities::EVENT_REGISTER_UTILITY_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = LogUtility::class;
            }
        );

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

/**
 * Logging in Craft involves using one of the following methods:
 *
 * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
 * Craft::info(): record a message that conveys some useful information.
 * Craft::warning(): record a warning message that indicates something unexpected has happened.
 * Craft::error(): record a fatal error that should be investigated as soon as possible.
 *
 * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
 *
 * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
 * the category to the method (prefixed with the fully qualified class name) where the constant appears.
 *
 * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
 * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
 *
 * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
 */
        Craft::info(
            Craft::t(
                'log-reader',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createlogContentModel()
    {
        return new LogContent();
    }

    /**
     * @inheritdoc
     */
    public function getLogContent()
    {
        if ($this->_contentModel === null) {
            $this->_contentModel = $this->createlogContentModel() ?: false;
        }

        if ($this->_contentModel !== false) {
            return $this->_contentModel;
        }

        return null;
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        $logFiles = LogReader::$plugin->fileReader->getAllFiles(true);

        return Craft::$app->view->renderTemplate(
            'log-reader/settings',
            [
                'logFiles' => $logFiles,
                'settings' => $this->getSettings()
            ]
        );
    }
}
