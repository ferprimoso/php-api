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
use Faker\Factory as FakerFactory;
use app\models\Book;

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

        if ($user->save()) {
            echo "User created successfully.\n";
        } else {
            echo "Failed to create user.\n";
            print_r($user->errors);
        }
    }

    public function actionSeedBooks($count = 100)
    {
        $faker = FakerFactory::create();

        for ($i = 0; $i < $count; $i++) {
            $book = new Book();
            $book->isbn = $faker->isbn13;
            $book->title = $faker->sentence(3);
            $book->author = $faker->name;
            $book->price = $faker->randomFloat(2, 5, 100);
            $book->stock = $faker->numberBetween(1, 100);

            if (!$book->save()) {
                echo "Failed to save book: " . implode(", ", $book->errors) . "\n";
            }
        }

        echo "$count books have been seeded.\n";
    }
}
