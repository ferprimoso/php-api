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
            ['cpf', 'validateCpf'], // Custom validation function
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

    public function validateCpf($attribute, $params)
    {
        $cpf = preg_replace('/\D/', '', $this->cpf); // Remove non-numeric characters

        if (strlen($cpf) != 11 || preg_match('/^(\d)\1+$/', $cpf)) {
            $this->addError($attribute, 'CPF is invalid.');
            return;
        }

        // Validate CPF digits
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $cpf[$i] * (10 - $i);
        }

        $remainder = $sum % 11;
        $firstDigit = $remainder < 2 ? 0 : 11 - $remainder;

        if ($cpf[9] != $firstDigit) {
            $this->addError($attribute, 'CPF is invalid.');
            return;
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $cpf[$i] * (11 - $i);
        }

        $remainder = $sum % 11;
        $secondDigit = $remainder < 2 ? 0 : 11 - $remainder;

        if ($cpf[10] != $secondDigit) {
            $this->addError($attribute, 'CPF is invalid.');
        }
    }
}
