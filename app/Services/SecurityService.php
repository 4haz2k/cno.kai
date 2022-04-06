<?php
namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class SecurityService
{
    public function encryptData($value): string
    {
        return Hash::make($value."$".env("SECRET_DOCUMENT_CODE"));
    }

    public function checkHash($value, $hash): bool
    {
        if(Hash::check($value."$".env("SECRET_DOCUMENT_CODE"), $hash)){
            return true;
        }
        else{
            return false;
        }
    }
}
