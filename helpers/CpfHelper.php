<?php

namespace app\helpers;

class CpfHelper
{
    public static function generateValidCPF()
    {
        // Generate the first 9 digits of CPF
        $cpf = '';
        for ($i = 0; $i < 9; $i++) {
            $cpf .= rand(0, 9);
        }

        // Calculate the first check digit
        $sum1 = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum1 += $cpf[$i] * (10 - $i);
        }
        $digit1 = ($sum1 % 11) < 2 ? 0 : 11 - ($sum1 % 11);

        // Calculate the second check digit
        $sum2 = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum2 += $cpf[$i] * (11 - $i);
        }
        $sum2 += $digit1 * 2;
        $digit2 = ($sum2 % 11) < 2 ? 0 : 11 - ($sum2 % 11);

        // Return the complete CPF
        return sprintf('%s%s%s', $cpf, $digit1, $digit2);
    }
}
