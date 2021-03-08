<?php
/**
 * Log Reader plugin for Craft CMS 3.x
 *
 * Control Panel log file reader utility
 *
 * @link      https://github.com/svale
 * @copyright Copyright (c) 2021 Svale Fossåskaret
 */

namespace svale\logreader\utilities;

use svale\logreader\LogReader;
use svale\logreader\assetbundles\logreader\LogReaderAsset;
// use svale\logreader\models\LogContent as LogModel;

use Craft;
use craft\base\Utility;

/**
 * Log Reader Utility
 *
 * Utility is the base class for classes representing Control Panel utilities.
 *
 * https://craftcms.com/docs/plugins/utilities
 *
 * @author    Svale Fossåskaret
 * @package   LogReader
 * @since     1.0.0
 */
class Log extends Utility
{
    // Static
    // =========================================================================

    /**
     * Returns the display name of this utility.
     *
     * @return string The display name of this utility.
     */
    public static function displayName(): string
    {
        return Craft::t('log-reader', 'Log File Reader');
    }

    /**
     * Returns the utility’s unique identifier.
     *
     * The ID should be in `kebab-case`, as it will be visible in the URL (`admin/utilities/the-handle`).
     *
     * @return string
     */
    public static function id(): string
    {
        return 'logreader-log';
    }

    /**
     * Returns the path to the utility's SVG icon.
     *
     * @return string|null The path to the utility SVG icon
     */
    public static function iconPath()
    {
        return Craft::getAlias("@svale/logreader/assetbundles/logreader/dist/img/LogReader-icon.svg");

    }

    /**
     * Returns the number that should be shown in the utility’s nav item badge.
     *
     * If `0` is returned, no badge will be shown
     *
     * @return int
     */
    public static function badgeCount(): int
    {
        return 0;
    }

    /**
     * Insert JS to template.
     *
     * @return string
     */
    public static function appendJS(): void
    {
		$url = explode('?log', Craft::$app->request->url)[0];

        $js = <<<JS

const formElement = document.querySelector('#js-logfile-form');
const fileSelector = document.querySelector('#js-logfile-selector');
const levelSelector = document.querySelector('#js-level-selector');

if(fileSelector) {
    fileSelector.addEventListener("change", function(e){
        formElement.submit();
    });
}
if(levelSelector) {
    levelSelector.addEventListener("change", function(e){
        formElement.submit();
    });
}
JS;

		Craft::$app->view->registerJs($js);
    }


    /**
     * Returns the utility's content HTML.
     *
     * @return string
     */
    public static function contentHtml(): string
    {

        $defaultLogfile = LogReader::$plugin->getSettings()->defaultLogfile;
        $current = Craft::$app->request->getParam('log', $defaultLogfile);
        $level = Craft::$app->request->getParam('level', 'all');

        $logFiles = LogReader::$plugin->fileReader->getFiles();
        $logEntries = LogReader::$plugin->fileReader->parse($current, $level);

        LogReader::$plugin->view->registerAssetBundle(LogReaderAsset::class);

        self::appendJS();

        return Craft::$app->getView()->renderTemplate(
            'log-reader/_components/utilities/Log_content',
            [
                'logFiles' => LogReader::$plugin->fileReader->mapFilesToOptions($logFiles),
                'levels' => [
                    ['label' => 'All', 'value' => 'all'],
                    ['label' => 'Info', 'value' => 'info'],
                    ['label' => 'Error', 'value' => 'error']
                ],
                'currentLogFile' => $current,
                'level' => $level,
                'logEntries' => $logEntries,
            ]
        );
    }
}
