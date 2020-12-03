<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{
    public function show(User $user)
    {
        return view('users.show', [
            'user' => $user,
        ]);
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->all();

        if ($request->avatar) {
            if ($avatarResult = (new ImageUploadHandler)->save($request->avatar, 'avatars', $user->id, 416)) {
                $data['avatar'] = $avatarResult['path'];
            }
        }

        $user->update($data);


        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }
}
