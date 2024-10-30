<?php

class CMW_Validate {

    public static function requiredVal($val)
    {
        return !empty($val);
    }

    public static function MaxLength($val, $length)
    {

        if (is_string($val)) {
            return strlen($val) <= $length;
        }
        else {
            return false;
        }
    }

    public static function isNumeric($val)
    {
        return is_numeric($val);
    }

    public static function isPhone($val)
    {
        $val = str_replace([' ', '(', ')', '-'], '', $val);

        if (!is_numeric($val)) return false;

        if (!preg_match('/^0/', $val)) return false;

        if(strlen($val) > 11 || strlen($val) < 8) return false;

        return true;
    }

    public static function isMobile($val)
    {
        $val = str_replace([' ', '(', ')', '-'], '', $val);

        if (!is_numeric($val)) return false;

        if (!preg_match('/^0/', $val)) return false;

        if(strlen($val) != 11) return false;

        return true;
    }

    public static function checkDateOfBirth($val)
    {
        return (strtotime($val) < strtotime(date('d.m.Y')));
    }

    public static function checkDateOfBirthNoMoreThat18Years($val)
    {
        return (strtotime($val) > strtotime("-18 years", time()));
    }

}
