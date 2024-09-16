<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\services\IsbnService;


class Book extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%book}}';
    }

    public function rules()
    {
        return [
            [['isbn', 'title', 'author', 'price', 'stock'], 'required'],
            [['isbn'], 'string', 'max' => 13],
            [['title', 'author'], 'string', 'max' => 255],
            [['price'], 'number'],
            [['stock'], 'integer'],
            [['isbn'], 'unique'],
        ];
    }

    public function validateIsbn()
    {
        $isbnService = new IsbnService();
        $isbn = $isbnService->getBookByIsbn($this->isbn);

        if ($isbn) {
            $this->title = $isbn['title'] ?? null;
            $this->author = $isbn['authors'][0] ?? null;
            return true;
        }

        return false;
    }
}
