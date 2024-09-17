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
use app\models\Customer;
use app\helpers\CpfHelper;


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

    // to populate books
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

     // Método para popular clientes
     public function actionSeedCustomers($count = 100)
     {
         $faker = FakerFactory::create();
 
         for ($i = 0; $i < $count; $i++) {
             $customer = new Customer();
             $customer->cpf = CpfHelper::generateValidCPF(); // Generate Valid Fake CPF 
             $customer->cep = $faker->numerify('########');
             $customer->name = $faker->name;
             $customer->street = $faker->streetName;
             $customer->number = $faker->buildingNumber;
             $customer->city = $faker->city;
             $customer->state = $faker->stateAbbr;
             $customer->sex = $faker->randomElement(['M', 'F']); // Considerando 'M' para masculino e 'F' para feminino
 
             if (!$customer->save()) {
                 echo "Failed to save customer: " . implode(", ", $customer->errors) . "\n";
             }
         }
 
         echo "$count customers have been seeded.\n";
     }
}
