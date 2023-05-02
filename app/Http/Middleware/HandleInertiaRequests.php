<?php

namespace App\Http\Middleware;

use App\Helpers\PaginatorHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Inertia\Middleware;
use Illuminate\Support\Facades\Route;

class HandleInertiaRequests extends Middleware
{
    public array $all_permission = [];
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'currentLocal' => \App::currentLocale(),
            'lang' => __('contents'),
            'permissionNames' => auth()->check() ? Auth::user()->getAllPermissions()->pluck('name')->toArray() : '',
            'currentUrl' => Route::currentRouteName(),
            'currentFullUrl' => URL::current(),
            'baseUrl' => url(''),
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                ];
            },
            'config' => config('haatbaazar'),
        ]);
    }
}
