<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switchLanguage(Request $request, $lang)
    {
        $allowedLocales = ['en', 'gu', 'hi'];

        if (in_array($lang, $allowedLocales)) {
            session(['locale' => $lang]);
            cookie()->queue('panel_locale', $lang, 60 * 24 * 365); // 1 year cookie
            
            $targetTrans = ($lang === 'en') ? '/en/en' : '/en/' . $lang;
            cookie()->queue('googtrans', $targetTrans, 60 * 24 * 365);
        }

        return back();
    }
}
