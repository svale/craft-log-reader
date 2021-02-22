<?php
/**
 * Log Reader plugin for Craft CMS 3.x
 *
 * Control Panel log file reader utility
 *
 * @link      https://github.com/svale
 * @copyright Copyright (c) 2021 Svale Fossåskaret
 */

namespace svale\logreader\models;

use svale\logreader\LogReader;

use Craft;
use craft\base\Model;

/**
 * Log Model
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Svale Fossåskaret
 * @package   LogReader
 * @since     1.0.0
 */
class LogContent extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some model attribute
     *
     * @var string
     */
    public $date;
    public $time;
    public $level;
    public $context;
    public $description;

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['date', 'time', 'level', 'context', 'description'], 'string'],
        ];
    }
}
