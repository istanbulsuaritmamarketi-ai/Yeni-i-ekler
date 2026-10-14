<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = trim($request->input('username'));

        $user = User::where('email', $login)
            ->orWhere('phone', $login)
            ->orWhere('name', $login)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'detail' => 'Kullanıcı adı veya şifre hatalı',
            ], 401);
        }

        $isSuperAdmin = $user->is_admin || in_array($user->role, ['superadmin', 'super_admin']) || $user->email === 'istanbulsuaritmamarketi@gmail.com';
        $role = $isSuperAdmin ? 'super_admin' : ($user->role === 'admin' ? 'admin' : 'staff');

        if ($user->role === 'customer') {
            return response()->json([
                'detail' => 'Bu hesap ile teknik servis paneline giriş yapılamaz.',
            ], 403);
        }

        $token = $user->createToken('mobile-app-token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => [
                'id' => (string) $user->id,
                'username' => $user->email ?? $user->name,
                'name' => $user->name,
                'email' => $user->email ?? '',
                'role' => $role,
                'isApproved' => true,
            ],
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:4',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
        ], [
            'email.unique' => 'Bu e-posta adresi zaten sistemde kayıtlı.',
            'password.min' => 'Şifre en az 4 karakter olmalıdır.',
            'email.email' => 'Lütfen geçerli bir e-posta adresi girin.',
            'username.required' => 'Kullanıcı adı zorunludur.',
            'name.required' => 'Ad soyad zorunludur.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'detail' => $validator->errors()->first(),
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff',
            'is_admin' => false,
        ]);

        return response()->json([
            'message' => 'Kayıt başarılı, yönetici onayı bekleniyor.',
            'detail' => 'PENDING_APPROVAL',
            'user' => [
                'id' => (string) $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'staff',
                'isApproved' => false,
            ],
        ], 201);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $isSuperAdmin = $user->is_admin || in_array($user->role, ['superadmin', 'super_admin']);
        $role = $isSuperAdmin ? 'super_admin' : ($user->role === 'admin' ? 'admin' : 'staff');

        return response()->json([
            'id' => (string) $user->id,
            'username' => $user->email ?? $user->name,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $role,
            'isApproved' => true,
        ]);
    }

    public function usersPending()
    {
        $users = User::where('role', 'staff')
            ->where('is_admin', false)
            ->latest()
            ->get()
            ->map(function ($u) {
                return [
                    'id' => (string) $u->id,
                    'username' => $u->email ?? $u->name,
                    'name' => $u->name,
                    'email' => $u->email,
                    'role' => $u->role,
                    'isApproved' => false,
                    'created_at' => $u->created_at?->toIso8601String(),
                ];
            });

        return response()->json($users);
    }

    public function usersAll()
    {
        $users = User::where('role', '!=', 'customer')
            ->latest()
            ->get()
            ->map(function ($u) {
                $isSuperAdmin = $u->is_admin || in_array($u->role, ['superadmin', 'super_admin']);

                return [
                    'id' => (string) $u->id,
                    'username' => $u->email ?? $u->name,
                    'name' => $u->name,
                    'email' => $u->email,
                    'role' => $isSuperAdmin ? 'super_admin' : ($u->role === 'admin' ? 'admin' : 'staff'),
                    'isApproved' => true,
                    'created_at' => $u->created_at?->toIso8601String(),
                ];
            });

        return response()->json($users);
    }

    public function approveUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->is_admin = ($user->role === 'admin' || $user->role === 'superadmin');
        $user->save();

        return response()->json(['message' => 'Kullanıcı onaylandı']);
    }

    public function rejectUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();

        return response()->json(['message' => 'Kullanıcı reddedildi ve silindi']);
    }

    public function updateRole(Request $request, $userId)
    {
        $role = $request->query('role', $request->input('role'));
        $user = User::findOrFail($userId);

        if (in_array($role, ['staff', 'admin', 'super_admin', 'superadmin'])) {
            $user->role = ($role === 'super_admin') ? 'superadmin' : $role;
            $user->is_admin = in_array($role, ['admin', 'super_admin', 'superadmin']);
            $user->save();
        }

        return response()->json(['message' => 'Rol güncellendi', 'role' => $user->role]);
    }
}
