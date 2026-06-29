<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSession;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /** Session lifetime in hours. */
    protected int $ttlHours = 168; // 7 days

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = AdminUser::where('username', $data['username'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password_hash)) {
            return response()->json(['message' => 'Неверный логин или пароль.'], 401);
        }

        $token     = Str::random(64);
        $expiresAt = Carbon::now()->addHours($this->ttlHours);

        $session = AdminSession::create([
            'user_id'    => $user->id,
            'token'      => $token,
            'expires_at' => $expiresAt,
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'token'      => $token,
            'expires_at' => $expiresAt->toIso8601String(),
            'user'       => [
                'id'       => $user->id,
                'username' => $user->username,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $session = $request->attributes->get('admin_session');
        if ($session) {
            $session->delete();
        }
        return response()->json(['message' => 'Вы вышли из системы.']);
    }

    public function me(Request $request)
    {
        $user = $request->attributes->get('admin_user');
        return response()->json([
            'id'       => $user->id,
            'username' => $user->username,
        ]);
    }

    /** Admin users management */
    public function listUsers()
    {
        return response()->json(AdminUser::orderBy('id')->get());
    }

    public function createUser(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|max:255|unique:admin_users,username',
            'password' => 'required|string|min:6',
        ]);

        $user = AdminUser::create([
            'username'      => $data['username'],
            'password_hash' => Hash::make($data['password']),
        ]);

        return response()->json($user, 201);
    }

    public function updateUser(Request $request, int $id)
    {
        $user = AdminUser::findOrFail($id);

        $data = $request->validate([
            'username' => 'sometimes|string|max:255|unique:admin_users,username,' . $id,
            'password' => 'sometimes|string|min:6',
        ]);

        if (isset($data['username'])) {
            $user->username = $data['username'];
        }
        if (isset($data['password'])) {
            $user->password_hash = Hash::make($data['password']);
        }
        $user->save();

        return response()->json($user);
    }

    public function deleteUser(int $id)
    {
        $user = AdminUser::findOrFail($id);
        $user->delete(); // sessions cascade at DB level
        return response()->json(['message' => 'Пользователь удалён.']);
    }
}
