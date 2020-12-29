<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Traits\ActiveUserHelper;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use App\Models\Traits\LastActivedAtHelper;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;

class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use MustVerifyEmailTrait;
    use Notifiable {
        notify as protected laravelNotify;
    }

    use HasRoles;

    use ActiveUserHelper;

    use LastActivedAtHelper;



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone', 'email', 'password', 'introduction', 'avatar',
        'weixin_openid', 'weixin_unionid',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'weixin_openid', 'weixin_unionid',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function isAuthorOf($model)
    {
        return $this->id === $model->user_id;
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了
        if ($this->id === Auth::id()) {
            return;
        }

        // 只有数据库类型通知才需要提醒
        if (method_exists($instance, 'toDatabase')) {
            $this->increment('notification_count');
        }

        $this->laravelNotify($instance);
    }

    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }

    public function setPasswordAttribute($value)
    {
        if (strlen($value) !== 60) {
            $value = bcrypt($value);
        }
        return $this->attributes['password']  = $value;
    }

    public function setAvatarAttribute($path)
    {
        if (! Str::startsWith($path, 'http')) {
            $path = config('app.url') . "/uploads/images/avatars/{$path}";
        }

        $this->attributes['avatar'] = $path;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
