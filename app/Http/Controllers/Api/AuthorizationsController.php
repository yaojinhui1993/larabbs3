<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Overtrue\Socialite\AccessToken;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Overtrue\LaravelSocialite\Socialite;
use Illuminate\Auth\AuthenticationException;
use App\Http\Requests\Api\AuthorizationRequest;
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

        $token = auth('api')->login('user');

        return $this->respondWithToken($token)->setStatusCode(201);
    }

    public function store(AuthorizationRequest $request)
    {
        $username = $request->username;

        $credentials = [];

        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $username;
        } else {
            $credentials['phone'] = $username;
        }

        $credentials['password'] = $request->password;

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            throw new AuthenticationException(trans('auth.failed'));
        }

        return $this->respondWithToken($token)->setStatusCode(201);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    public function update()
    {
        $token = auth('api')->refresh();

        return $this->respondWithToken($token);
    }

    public function destroy()
    {
        auth('api')->logout();

        return response(null, 204);
    }
}
