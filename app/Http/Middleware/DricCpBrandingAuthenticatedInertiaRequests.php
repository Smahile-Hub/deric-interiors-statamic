<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Statamic\Http\Middleware\CP\HandleAuthenticatedInertiaRequests;

class DricCpBrandingAuthenticatedInertiaRequests extends HandleAuthenticatedInertiaRequests
{
    public function share(Request $request): array
    {
        $shared = parent::share($request);
        $name = config('statamic.cp.custom_cms_name', config('app.name', 'Dric Interior'));

        $shared['_statamic'] = [
            ...($shared['_statamic'] ?? []),
            'cmsName' => $name,
            'version' => $name,
        ];

        return $shared;
    }
}
