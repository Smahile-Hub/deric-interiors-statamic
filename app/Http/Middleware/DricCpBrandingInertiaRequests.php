<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Statamic\Http\Middleware\CP\HandleInertiaRequests;

class DricCpBrandingInertiaRequests extends HandleInertiaRequests
{
    public function share(Request $request): array
    {
        $shared = parent::share($request);

        $shared['_statamic'] = [
            ...($shared['_statamic'] ?? []),
            ...$this->brandProps(),
        ];

        return $shared;
    }

    private function brandProps(): array
    {
        $name = config('statamic.cp.custom_cms_name', config('app.name', 'Dric Interior'));
        $light = config('statamic.cp.custom_logo_url', '/golden-dric-logo.png');
        $dark = config('statamic.cp.custom_dark_logo_url', $light);

        return [
            'version' => $name,
            'cmsName' => $name,
            'logos' => [
                'text' => config('statamic.cp.custom_logo_text', $name),
                'siteName' => $name,
                'light' => ['nav' => $light, 'outside' => $light],
                'dark' => ['nav' => $dark, 'outside' => $dark],
            ],
        ];
    }
}
