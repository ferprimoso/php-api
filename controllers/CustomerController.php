<?php
namespace app\controllers;

use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use app\models\Customer;
use yii\filters\auth\HttpBearerAuth;


class CustomerController extends ActiveController
{
    public $modelClass = 'app\models\Customer';
    
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'authenticator' => [
                'class' => HttpBearerAuth::class,
            ],
            // Other behaviors if needed
        ]);
    }
    

    public function actions()
    {
        $actions = parent::actions();

        $actions['index'] = [
            'class' => 'yii\rest\IndexAction',
            'modelClass' => $this->modelClass,
            'prepareDataProvider' => function () {
                $query = Customer::find();
                
                // Retrieve parameters from query
                $params = \Yii::$app->request->queryParams;
                $limit = isset($params['limit']) ? (int)$params['limit'] : 10;
                $offset = isset($params['offset']) ? (int)$params['offset'] : 0;
                
                // Apply limit and offset
                $query->limit($limit)->offset($offset);

                // Apply sorting if provided
                if (isset($params['sort'])) {
                    $sort = $params['sort'];
                    $query->orderBy($sort);
                } else {
                    // Default sorting if not specified
                    $query->orderBy('id ASC');
                }

                // Apply filtering if provided
                if (isset($params['filter'])) {
                    $columns = array_keys(Customer::getTableSchema()->columns); // Get existing columns from the schema
                    foreach ($params['filter'] as $attribute => $value) {
                        if (in_array($attribute, $columns)) {
                            $query->andWhere(['like', $attribute, $value]);
                        } else {
                            throw new BadRequestHttpException("Invalid column: $attribute");
                        }
                    }
                }

                return new ActiveDataProvider([
                    'query' => $query,
                    'pagination' => false, // Disable Yii2 pagination as we handle it manually
                ]);
            }
        ];

      
        /*  // Define a custom 'create' action
         $actions['create'] = [
            'class' => 'app\actions\CreateCustomerAction',
            'modelClass' => $this->modelClass,
        ];
 */
        unset($actions['create']);
 
        return $actions;
    }

    /**
     * Custom Create Action for Books
     */
    public function actionCreate()
    {
        $model = new $this->modelClass;
        $model->load(\Yii::$app->request->post(), '');

         // Perform custom validation logic if needed
         if (!$model->validate()) {
            // Return the validation errors
            \Yii::$app->response->statusCode = 400;
            return $model->getErrors();
        }
    
        // Custom validation logic
        if (!$model->validateAddress()) {
            throw new BadRequestHttpException('Invalid CEP');
        }
    
        if ($model->save()) {
            \Yii::$app->response->statusCode = 201;
            return $model;
        }
    
        throw new ServerErrorHttpException('Failed to create the Customer');
    }

    
}
