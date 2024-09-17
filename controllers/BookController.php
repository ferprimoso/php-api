<?php
namespace app\controllers;

use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use app\models\Book;
use yii\filters\auth\HttpBearerAuth;

class BookController extends ActiveController
{
    public $modelClass = 'app\models\Book';
    
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
                $query = Book::find();
                $params = \Yii::$app->request->queryParams;
                $limit = isset($params['limit']) ? (int)$params['limit'] : 10;
                $offset = isset($params['offset']) ? (int)$params['offset'] : 0;
                
                // Apply limit and offset
                $query->limit($limit)->offset($offset);

                // Apply sorting if provided
                if (isset($params['sort'])) {
                    $sort = $params['sort'];
                    $columns = array_keys(Book::getTableSchema()->columns); // Get existing columns from the schema

                    // Validate each sort column
                    foreach (explode(',', $sort) as $sortPart) {
                        $sortPart = trim($sortPart);
                        $direction = SORT_ASC;
                        // Use ':' to separate column and direction
                        if (strpos($sortPart, ':') !== false) {
                            list($column, $dir) = explode(':', $sortPart);
                            $column = trim($column);
                            $dir = trim(strtoupper($dir));
                            if ($dir === 'DESC') {
                                $direction = SORT_DESC;
                            } elseif ($dir !== 'ASC') {
                                // Default to ASC if the direction is not recognized
                                $direction = SORT_ASC;
                            }
                        } else {
                            $column = trim($sortPart);
                        }

                        if (in_array($column, $columns)) {
                            $query->addOrderBy([$column => $direction]);
                        } else {
                            throw new BadRequestHttpException("Invalid sort column: $column");
                        }
                    }
                } else {
                    // Default sorting if not specified
                    $query->orderBy('id ASC');
                }


                // Apply filtering if provided
                if (isset($params['filter'])) {
                    $columns = array_keys(Book::getTableSchema()->columns); // Get existing columns from the schema
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

        // Model Validation
        if (!$model->validate()) {
            \Yii::$app->response->statusCode = 400;
            return $model->getErrors();
        }

        // Custom validation logic
        if (!$model->validateIsbn()) {
            throw new BadRequestHttpException('Invalid Isbn');
        }

        if ($model->save()) {
            \Yii::$app->response->statusCode = 201;
            return $model;
        }

        throw new ServerErrorHttpException('Failed to create the book');
    }
}
