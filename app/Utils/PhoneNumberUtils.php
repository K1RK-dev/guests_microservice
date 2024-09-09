<?php

namespace App\Utils;

use libphonenumber\PhoneNumberUtil;

class PhoneNumberUtils
{
    /**
     * @param string $phoneNumber
     * @return string
     */
    public static function parseDialCodeByPhoneNumber($phoneNumber){
        if(!$phoneNumber)
            return "";
        $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $phoneNumberObject = $phoneNumberUtil->parse($phoneNumber);
        $regionCode = $phoneNumberUtil->getRegionCodeForNumber($phoneNumberObject);
        if(!$regionCode)
            return "";
        $dialCode = $phoneNumberUtil->getCountryCodeForRegion($regionCode);
        if(!$dialCode)
            return "";
        return '+' . $dialCode;
    }
}
