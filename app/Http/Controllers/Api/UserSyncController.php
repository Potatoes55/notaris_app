<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscriptions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserSyncController extends Controller
{
    public function index()
    {
        try {
            $users = User::orderBy('created_at', 'desc')->get();

            if ($users->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No users found',
                    'data' => [],
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'User data successfully retrieved',
                'data' => $users,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // public function store(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         // Validasi bagian user
    //         $validatedUser = validator($request->input('user'), [
    //             'notaris_id' => 'nullable',
    //             'email' => 'required|email|unique:users,email',
    //             'username' => 'required|string|max:255|unique:users,username',
    //             'password' => 'required|string|min:6',
    //             'phone' => 'required|string',
    //             'address' => 'required|string',
    //             'status' => 'required|string',
    //         ])->validate();

    //         // Validasi bagian subscription
    //         $validatedSubscription = validator($request->input('user.subscription'), [
    //             'plan_id' => 'required',
    //             'start_date' => 'required|date',
    //             'end_date' => 'required|date',
    //             'status' => 'required|string',
    //         ])->validate();

    //         // Simpan user
    //         $user = User::create([
    //             'notaris_id' => $validatedUser['notaris_id'],
    //             'email' => $validatedUser['email'],
    //             'username' => $validatedUser['username'],
    //             'password' => bcrypt($validatedUser['password']),
    //             'phone' => $validatedUser['phone'],
    //             'address' => $validatedUser['address'],
    //             'status' => $validatedUser['status'],
    //         ]);
    //         $subscription = Subscriptions::updateOrCreate(
    //             ['user_id' => $user->id], // kondisi
    //             [
    //                 'plan_id' => $validatedSubscription['plan_id'],
    //                 'start_date' => $validatedSubscription['start_date'],
    //                 'end_date' => $validatedSubscription['end_date'],
    //                 'status' => $validatedSubscription['status'],
    //             ]
    //         );
    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'User dan subscription berhasil disync',
    //             'data' => [
    //                 'user' => $user,
    //                 'subscription' => $subscription
    //             ]
    //         ], 201);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi error.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function store(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         // Ambil payload
    //         $payloadUser = $request->input('user');
    //         $payloadSub = $payloadUser['subscription'] ?? [];

    //         // Validasi tanpa unique (karena sync bisa update)
    //         $validatedUser = validator($payloadUser, [
    //             'notaris_id' => 'nullable',
    //             'email' => 'required|email',
    //             'username' => 'required|string|max:255',
    //             'password' => 'nullable|string|min:6',
    //             'phone' => 'required|string',
    //             'address' => 'required|string',
    //             'status' => 'required|string',
    //         ],

    //             )->validate();

    //         // Cari user lama (berdasarkan email / username)
    //         $user = User::where('email', $validatedUser['email'])
    //             ->orWhere('username', $validatedUser['username'])
    //             ->first();

    //         if (!$user) {
    //             // BUAT user baru
    //             if (!empty($validatedUser['password'])) {
    //                 $validatedUser['password'] = $validatedUser['password'];
    //             }

    //             $user = User::create($validatedUser);
    //         } else {
    //             // UPDATE user lama
    //             if (!empty($validatedUser['password'])) {
    //                 $validatedUser['password'] = $validatedUser['password'];
    //             } else {
    //                 unset($validatedUser['password']);
    //             }

    //             $user->update($validatedUser);
    //         }

    //         // Validasi subscription
    //         $validatedSubscription = validator($payloadSub, [
    //             'plan_id' => 'required',
    //             'start_date' => 'required|date',
    //             'end_date' => 'required|date',
    //             'status' => 'required|string',
    //         ])->validate();

    //         // Sync subscription (update or create)
    //         $subscription = Subscriptions::updateOrCreate(
    //             ['user_id' => $user->id],
    //             $validatedSubscription
    //         );

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Sync user & subscription berhasil',
    //             'data' => [
    //                 'user' => $user,
    //                 'subscription' => $subscription,
    //             ]
    //         ], 200);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi error.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function store(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         // Ambil payload utama
    //         $payloadUser = $request->input('user');
    //         $payloadSub  = $payloadUser['subscription'] ?? [];

    //         $validatedUser = validator($payloadUser, [
    //             'notaris_id' => 'nullable',
    //             'email'      => 'required|email',
    //             'username'   => 'required|string|max:255',
    //             'password'   => 'nullable|string|min:6',
    //             'phone'      => 'required|string',
    //             'address'    => 'required|string',
    //             'status'     => 'required|string',
    //         ])->validate();

    //         $user = User::where('email', $validatedUser['email'])
    //             ->orWhere('username', $validatedUser['username'])
    //             ->first();

    //         if (!empty($validatedUser['password'])) {
    //             $validatedUser['password'] = $validatedUser['password'];
    //         } else {
    //             unset($validatedUser['password']);
    //         }

    //         if ($user) {
    //             $user->update($validatedUser);
    //         } else {
    //             $user = User::create($validatedUser);
    //         }

    //         $validatedSub = validator($payloadSub, [
    //             'plan_id'     => 'required',
    //             'start_date'  => 'required|date',
    //             'end_date'    => 'required|date',
    //             'payment_date' => 'nullable',
    //             'status'      => 'required|string',
    //         ])->validate();

    //         $latestSub = Subscriptions::where('user_id', $user->id)
    //             ->orderBy('end_date', 'desc')
    //             ->first();

    //         if ($latestSub) {
    //             // Hanya update jika end_date lebih baru
    //             if ($validatedSub['end_date'] > $latestSub->end_date) {
    //                 $latestSub->update($validatedSub);
    //             }
    //             $subscription = $latestSub;
    //         } else {
    //             // Jika belum ada subscription sama sekali → buat
    //             $subscription = Subscriptions::create(array_merge(
    //                 $validatedSub,
    //                 ['user_id' => $user->id]
    //             ));
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Sync user & subscription berhasil',
    //             'data' => [
    //                 'user'         => $user,
    //                 'subscription' => $subscription
    //             ]
    //         ], 200);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi error.',
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            /*
            | AMBIL PAYLOAD
            */
            $payloadUser = $request->input('user');
            $payloadSub = $payloadUser['subscription'] ?? [];

            /*
            | VALIDASI USER
            */
            $validatedUser = validator($payloadUser, [
                'notaris_id' => 'nullable',
                'email' => 'required|email',
                'username' => 'required|string|max:255',
                'subscription_id' => 'required',
                'access_token' => 'required|string|max:25',
                'password' => 'nullable|string|min:6',
                'phone' => 'required|string',
                'address' => 'required|string',
                'signup_at' => 'nullable',
                'active_at' => 'nullable',
                'status' => 'required',
            ])->validate();

            /*
            | CREATE / UPDATE USER
            */
            $user = User::where('email', $validatedUser['email'])
                ->orWhere('username', $validatedUser['username'])
                ->first();

            if (! empty($validatedUser['password'])) {
                $validatedUser['password'] = $validatedUser['password'];
            } else {
                unset($validatedUser['password']);
            }

            $user = $user ? tap($user)->update($validatedUser) : User::create($validatedUser);

            /*
            | VALIDASI SUBSCRIPTION
            */
            $validatedSub = validator($payloadSub, [
                'id' => 'nullable|integer',
                'plan_id' => 'nullable',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'payment_date' => 'nullable',
                'status' => 'required',
            ])->validate();

            /*
            | CASE 1: Jika ada subscription id → HARUS dibuat baru
            | Tapi cek dulu apakah id itu sudah ada (duplicate)
            */
            // cek jika payload mengandung subscription menimpa atau create baru
            if (! empty($validatedSub['id'])) {

                $exists = Subscriptions::where('user_id', $user->id)
                    ->where('id', $validatedSub['id'])
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Subscription ID sudah dipakai oleh user ini.',
                    ], 400);
                }

                // Buat baru karena aman (ID belum pernah ada)
                $subscription = Subscriptions::create(array_merge(
                    $validatedSub,
                    ['user_id' => $user->id]
                ));

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Subscription baru berhasil ditambahkan (ID custom).',
                    'data' => [
                        'user' => $user,
                        'subscription' => $subscription,
                    ],
                ], 200);
            }

            /*
            | CASE 2: TIDAK ADA subscription.id
            | → Ikuti rule: Update terbaru atau buat baru jika belum ada.
            */
            $latestSub = Subscriptions::where('user_id', $user->id)
                ->orderBy('end_date', 'desc')
                ->first();

            if ($latestSub) {
                if ($validatedSub['end_date'] > $latestSub->end_date) {
                    $latestSub->update($validatedSub);
                }

                $subscription = $latestSub;
            } else {
                // User belum punya subscription → buat baru
                $subscription = Subscriptions::create(array_merge(
                    $validatedSub,
                    ['user_id' => $user->id]
                ));
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sync user & subscription berhasil.',
                'data' => [
                    'user' => $user,
                    'subscription' => $subscription,
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi error.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
