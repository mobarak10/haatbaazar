<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * change language
     * @param $locale
     */
    public function switchLanguage()
    {
        $current_lang = \App::currentLocale();
        if ($current_lang == 'bn') {
            session(['APP_LOCALE' => 'en']);
        }else{
            session(['APP_LOCALE' => 'bn']);
        }
        return redirect()->back();
    }
}
