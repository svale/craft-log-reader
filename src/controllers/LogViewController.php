<?php
/**
 * Log Reader plugin for Craft CMS 3.x
 *
 * Control Panel log file reader utility
 *
 * @link      https://github.com/svale
 * @copyright Copyright (c) 2021 Svale Fossåskaret
 */

namespace svale\logreader\controllers;
// use svale\logreader\LogReader;

use Craft;
use craft\web\Controller;

use yii\web\Response;

/**
 * LogView Controller
 *
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Svale Fossåskaret
 * @package   LogReader
 * @since     1.0.0
 */
class LogViewController extends Controller
{

    // Protected Properties
    // =========================================================================


    // Public Methods
    // =========================================================================

    /**
     * /actions/log-reader/log-view
     * @return mixed
     */
    public function actionIndex($log = null, $level = 'all'): Response
    {
        if($log === null ) {
            $redirect = 'admin/utilities/logreader-log/?level='.$level;
        }

        $redirect = 'admin/utilities/logreader-log/?log='.$log.'&level='.$level;

        // var_dump($redirect); die();
        return $this->redirect($redirect);
    }


}
