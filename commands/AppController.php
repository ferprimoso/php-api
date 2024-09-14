<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\User;
use yii\base\Exception;


/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppController extends Controller
{
    public function actionAddUser($username, $password, $name)
    {
        $user = new User();
        $user->username = $username;
        $user->setPassword($password); // Hash the password
        $user->name = $name;
        $user->auth_key = Yii::$app->security->generateRandomString(); // Optional: generate an auth key

        if ($user->save()) {
            echo "User created successfully.\n";
        } else {
            echo "Failed to create user.\n";
            print_r($user->errors);
        }
    }
}
