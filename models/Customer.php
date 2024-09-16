<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\services\CepService;


class Customer extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%customer}}';
    }

    public function rules()
    {
        return [
            [['name', 'cpf', 'cep', 'street', 'number', 'city', 'state', 'sex'], 'required'],
            ['additional_information', 'string'], 
            ['cpf', 'match', 'pattern' => '/^\d{11}$/', 'message' => 'CPF must be 11 digits.'],
            ['state', 'string', 'length' => 2],
            ['sex', 'in', 'range' => ['M', 'F']],
        ];
    }

    public function validateAddress()
    {
        $cepService = new CepService();
        $address = $cepService->getAddressByCep($this->cep);

        if ($address) {
            // Validate and set address fields
            $this->street = $address['street'] ?? null;
            $this->city = $address['city'] ?? null;
            $this->state = $address['state'] ?? null;
            return true;
        }

        return false;
    }
}
