# Log Reader plugin for Craft CMS 3.x

Control Panel Log File Reader Utility

![Screenshot](resources/img/plugin-logo.svg)

## Requirements

This plugin requires Craft CMS 3.0 or above.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require craft-log-reader/log-reader

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Log Reader.

## Log Reader Overview

The Plugin adds Log File Reader as an option in the Utilities section. The Reader provides a selection of all log files found the default Craft log file diretory.

The Reader is a simple tabular list of lines begining with the standard format of [date][time] (2021-02-03 22:08:55) in the selected log file.


## Configuring Log Reader

Under the plugin settings you can configure which log files taht shoiuld be available to the Reader.

## Using Log Reader

The list can be filter by level of incident (info, warning, error).

Please note that the plugin does not display a full comprehensiv view of the log files. For this use th Yii Debug Tool Bar is much better. But it can provid a quick look and can be particualary useful for custom logfiles.

## Log Reader Roadmap

* Add a configuration setting for elligable incident levels
* Add a configuration setting for lines cut off (e.g. max 300)
* Add sortable columns
* Add option of searching/filtering by dates
* Re-write in Vue
* Release it

Brought to you by [Svale Fossåskaret](https://github.com/svale)
