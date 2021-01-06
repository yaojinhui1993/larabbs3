<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Str;
use Gregwar\Captcha\CaptchaBuilder;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Api\CaptchaRequest;

class CaptchasController extends Controller
{
    public function store(CaptchaRequest $request, CaptchaBuilder $captchaBuilder)
    {
        $key = 'captcha_' . Str::random(15);
        $phone = $request->phone;

        $captcha = $captchaBuilder->build();
        $expireAt = now()->addMinutes(2);

        Cache::put($key, [
            'phone' => $phone,
            'code' => $captcha->getPhrase(),
            $expireAt,
        ]);

        $result = [
            'captcha_key' => $key,
            'expired_at' => $expireAt->toDateTimeString(),
            'captcha_image_content' => $captcha->inline(),
        ];

        return response()->json($result)->setStatusCode(201);
    }
}
