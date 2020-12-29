<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Overtrue\Socialite\AccessToken;
use App\Http\Controllers\Controller;
use Overtrue\LaravelSocialite\Socialite;
use Illuminate\Auth\AuthenticationException;
use App\Http\Requests\Api\SocialAuthorizationRequest;

class AuthorizationsController extends Controller
{
    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        $driver = Socialite::driver($type);

        try {
            if ($code = $request->code) {
                $accessToken = $driver->getAccessToken($code);
            } else {
                $tokenData['access_token'] = $request->access_token;

                // 微信需要增加 openid
                if ($type === 'wechat') {
                    $tokenData['openid'] = $request->openid;
                }

                $accessToken = new AccessToken($tokenData);
            }

            $oauthUser = $driver->user($accessToken);
        } catch (\Exception $e) {
            throw new AuthenticationException('参数错误，未获取用户信息。');
        }

        switch ($type) {
            case 'wechat':
                $user = null;

                $unionId = $oauthUser->getOriginal()['unionid'] ?? null;

                if ($unionId) {
                    $user = User::where('weixin_union_id', $unionId)->first();
                } else {
                    $user = User::where('weixin_openid', $oauthUser->getId())->first();
                }

                if (! $user) {
                    $user = User::create([
                        'name' => $oauthUser->getNickName(),
                        'avatar' => $oauthUser->getAvatar(),
                        'weixin_openid' => $oauthUser->getId(),
                        'weixin_unionid' => $unionId,
                    ]);
                }

                break;
        }

        return response()->json(['token' => $user->id]);
    }
}
