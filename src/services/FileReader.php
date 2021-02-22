<?php
/**
 * Log Reader plugin for Craft CMS 3.x
 *
 * Control Panel log file reader utility
 *
 * @link      https://github.com/svale
 * @copyright Copyright (c) 2021 Svale Fossåskaret
 */

namespace svale\logreader\services;

use svale\logreader\LogReader;
use svale\logreader\models\LogContent as LogModel;

use Craft;
use craft\base\Component;
use craft\helpers\App;

/**
 * FileReader Service
 *
 * @author    Svale Fossåskaret
 * @package   LogReader
 * @since     1.0.0
 */
class FileReader extends Component
{

    // Properties
    // =========================================================================

    public $logFileLevel = 'all';

    // Public Methods
    // =========================================================================

    private function setLevel($level)
    {
        $this->logFileLevel = trim($level);
    }

    /**
     * From any other plugin file, call it like this:
     *     LogReader::$plugin->fileReader->getAllFiles()
     *
     * @return mixed
     */
    public function mapFilesToOptions($files = []) {
        return array_map(
            function($item) {
                return ['label' => $item, 'value' => $item];
            },$files
        );
    }

    /**
     * From any other plugin file, call it like this:
     *     LogReader::$plugin->fileReader->getAllFiles()
     *
     * @return mixed
     */
    public function getFiles() {

        $settings = LogReader::$plugin->getSettings();

        $displayFiles = $settings->filesToDisplay;

        $files = $this->getAllFiles();

        if ($displayFiles === '*' || $displayFiles === 1 || !is_array($displayFiles)) {
            return $files;
        }

        if (!$displayFiles) {
            return [];
        }

        // not really needed currently..
        $selectedFiles = array_intersect($displayFiles, $files);

        return $selectedFiles;

    }

    /**
     * From any other plugin file, call it like this:
     *     LogReader::$plugin->fileReader->getAllFiles()
     *
     * @return mixed
     */
    public function getAllFiles($mapped = false) {
        $logPath = Craft::$app->path->getLogPath();

        if (!@file_exists($logPath)) {
            return 'Log files directory not found';
        }

        $logFiles = array_values(array_filter(
            scandir($logPath),
            function ($var) {
                return $var[0] != '.' && strpos($var, '.log');
            }
        ));

        if (!count($logFiles)) {
            return 'No log files found';
        }

        if ($mapped) {
            return  $this->mapFilesToOptions($logFiles);
        }

        return $logFiles;
    }

    /**
     * From any other plugin file, call it like this:
     *     LogReader::$plugin->fileReader->parse()
     *
     * @return mixed
     */
    public function parse($fileName, $level = 'all')
    {

		App::maxPowerCaptain();

        $this->setLevel($level);

        $logPath = Craft::$app->path->getLogPath();

        // @todo: move to settings
        // =>  $levelsToExclude = LogReader::$plugin->getSettings()->levelsToExclude;
        $excludeLevels = ['profile end', 'profile begin', 'trace'];

        // $lines = [];
        $output = [];

        // matches the parts of a typical log line
        // eg: 2021-02-01 11:24:59 [-][-][-][info][modules\winorgdonationformmodule\WinorgDonationFormModule::init] Winorg Donation Form module loaded
            // [date][time][level][context][description]
		$pattern = '/^(\d{4}(?:-\d{2}){2}) ((?:\d{2}:){2}\d{2}) (?:(?:\[[^\]]+\]){3})\[([^\]]+)\]\[([^\]]+)\] (.+)/i';

		if (@file_exists($logPath)) {
		    $logFile = $logPath . '/' . $fileName;

			if (@file_exists($logFile)) {

				$fh = fopen($logFile,'r') or error_log("Can't open file");

                $i = 1;

                while (! feof($fh)) {
                    // read each line and trim off leading/trailing whitespace
                    if ($s = trim(fgets($fh,16384))) {
                        // match the line to the pattern
                        if (preg_match($pattern,$s,$matches)) {

                            // put each part of the match in an appropriately-named variable
                            list($whole_match,$date,$time,$level,$context,$description) = $matches;

                            // drop string of 'whole_match' and only keep the parts
                            // array_shift($matches);
                            // var_dump($this->logFileLevel); die();

                            // exclude some levels
                            if(in_array($level, $excludeLevels)) {
                                continue;
                            }

                            // filter by level
                            if($this->logFileLevel !== 'all') {
                                if($level === $this->logFileLevel) {
                                    $output[] = [
                                        'date' => $date,
                                        'time' => $time,
                                        'level' => $level,
                                        // 'context' => $context,
                                        'description' => $description,
                                    ];
                                }
                            } else {
                                $output[] = [
                                    'date' => $date,
                                    'time' => $time,
                                    'level' => $level,
                                    // 'context' => $context,
                                    'description' => $description,
                                ];
                            }

                            // LogReader::$plugin->getLogContent()->setAttributes($output);
                        } else {
                            // complain if the line didn't match the pattern
                            error_log("Can't parse line $i: $s");
                        }
                    }
                    $i++;
                }

                fclose($fh) or die($php_errormsg);
			}
        }
        // var_dump($lines);die();

        // reverse to get latest at top
        $output = array_reverse($output);

        return $output;
	}
}
