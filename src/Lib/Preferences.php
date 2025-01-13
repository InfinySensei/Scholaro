<?php

namespace App\Sae\Lib;

use App\Sae\Modele\HTTP\Cookie;

class Preferences
{
    public static function enregistrer($cleChoix, array $choix) : void
    {
        Cookie::enregistrer($cleChoix, $choix);
    }

    public static function lire($cleChoix) : array
    {
        return Cookie::lire($cleChoix);
    }

    public static function existe($cleChoix) : bool
    {
        return Cookie::contient($cleChoix);
    }

    public static function supprimer($cleChoix) : void
    {
        Cookie::supprimer($cleChoix);
    }
}