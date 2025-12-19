<?php

namespace App\Http\Controllers;

use Common\Core\BaseController;
use Common\Pages\CustomPage;
use Illuminate\Support\Facades\Auth;

class PolicyPageController extends BaseController
{
    public function privacyPolicy()
    {
        $page = CustomPage::where('slug', 'privacy-policy')->first();
        
        if (!$page) {
            abort(404, 'Privacy Policy page not found');
        }
        
        // For logged-in users, use React app layout (same as contact us page)
        if (Auth::check()) {
            return $this->renderClientOrApi([
                'pageName' => 'custom-page',
                'data' => [
                    'page' => $page,
                    'loader' => 'customPage',
                ],
            ]);
        }
        
        // For guests, use Blade template (marketing site)
        return view('marketing.custom-page', ['page' => $page]);
    }

    public function termsAndConditions()
    {
        $page = CustomPage::where('slug', 'terms-and-conditions')
            ->orWhere('slug', 'terms-of-service')
            ->first();
        
        if (!$page) {
            abort(404, 'Terms & Conditions page not found');
        }
        
        // For logged-in users, use React app layout (same as contact us page)
        if (Auth::check()) {
            return $this->renderClientOrApi([
                'pageName' => 'custom-page',
                'data' => [
                    'page' => $page,
                    'loader' => 'customPage',
                ],
            ]);
        }
        
        // For guests, use Blade template (marketing site)
        return view('marketing.custom-page', ['page' => $page]);
    }
}

