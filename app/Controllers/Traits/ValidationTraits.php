<?php

namespace App\Controllers\Traits;
trait ValidationTraits
{
    public function isValid($value): bool
    {
        $invalid=['','null','undefined','NaN','NULL','UNDEFINED','NAN',null];
        return !in_array($value,$invalid,true);
    }
    public function isValidArray(array $value, array $exception): bool
    {
        foreach ($value as $key => $val) {
            if (!in_array($key, $exception, true) && !$this->isValid($val)) {
                return false;
            }
        }
        return true;
    }
    public function isValidDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    public function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
        
    }
}
?>