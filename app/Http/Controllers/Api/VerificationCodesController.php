<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Str;
use Overtrue\EasySms\EasySms;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\AuthenticationException;
use App\Http\Requests\Api\VerificationCodeRequest;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $captchaData = Cache::get($request->captcha_key);

        if (! $captchaData) {
            abort(403, '图片验证码已失效！');
        }

        if (! hash_equals($captchaData['code'], $request->captcha_code)) {
            Cache::forget($request->captcha_key);
            throw new AuthenticationException('验证码错误');
        }

        $phone = $request->phone;

        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

        try {
            $result = $easySms->send($phone, [
                'template' => config('easysms.gateways.aliyun.templates.register'),
                'data' => [
                    'code' => $code,
                ],
            ]);
        } catch (NoGatewayAvailableException $exception) {
            $message = $exception->getMessage();
            abort(500, $message ?: '短信发送异常');
        } catch (\Exception $e) {
            abort(500, $e->getMessage() ?: '短信发送异常');
        }

        $key = 'verificationCode_' . Str::random(15);
        $expiredAt = now()->addMinutes(5);

        Cache::put($key, [
            'phone' => $phone,
            'code' => $code,
        ], $expiredAt);

        return response()->json([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ]);
    }
}
