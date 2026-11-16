<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

// Valida CPF (11 dígitos) ou CNPJ (14 dígitos) pelos dígitos verificadores.
// Espera o valor só com números; $tipo restringe a 'F' (CPF) ou 'J' (CNPJ).
class CpfCnpj implements ValidationRule
{
    public function __construct(private ?string $tipo = null)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $doc = preg_replace('/\D/', '', (string) $value);

        if ($this->tipo === 'F' && !self::cpfValido($doc)) {
            $fail('O CPF informado é inválido.');
        } elseif ($this->tipo === 'J' && !self::cnpjValido($doc)) {
            $fail('O CNPJ informado é inválido.');
        } elseif ($this->tipo === null && !self::cpfValido($doc) && !self::cnpjValido($doc)) {
            $fail('O CPF/CNPJ informado é inválido.');
        }
    }

    public static function cpfValido(string $cpf): bool
    {
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += (int) $cpf[$i] * (($t + 1) - $i);
            }
            $digito = ((10 * $soma) % 11) % 10;
            if ((int) $cpf[$t] !== $digito) {
                return false;
            }
        }

        return true;
    }

    public static function cnpjValido(string $cnpj): bool
    {
        if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $pesos = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        foreach ([12, 13] as $t) {
            $soma = 0;
            $p = array_slice($pesos, 13 - $t);
            for ($i = 0; $i < $t; $i++) {
                $soma += (int) $cnpj[$i] * $p[$i];
            }
            $resto = $soma % 11;
            $digito = $resto < 2 ? 0 : 11 - $resto;
            if ((int) $cnpj[$t] !== $digito) {
                return false;
            }
        }

        return true;
    }
}
