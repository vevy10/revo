<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use App\Notifications\UserInvitationNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;

class InvitationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique(User::class, 'email')],
            'role' => ['required', Rule::exists(Role::class, 'code')],
        ]);

        $plainToken = Str::random(64);

        $invitation = DB::transaction(function () use ($data, $plainToken): Invitation {
            $role = Role::query()->where('code', $data['role'])->firstOrFail();

            $user = User::query()->create([
                'email' => $data['email'],
                'role_id' => $role->id,
            ]);

            return Invitation::query()->create([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'email' => $user->email,
                'token' => hash('sha256', $plainToken),
                'expires_at' => now()->addHours(48),
            ]);
        });

        $invitation->user->notify(new UserInvitationNotification($invitation, $plainToken));

        return response()->json([
            'message' => 'Invitation envoyée.',
            'invitation' => $invitation->load('role'),
        ], Response::HTTP_CREATED);
    }

    public function show(string $token): JsonResponse
    {
        $invitation = $this->findByToken($token, lockForUpdate: true);

        if (! $invitation) {
            return response()->json(['message' => 'Lien d’invitation invalide.'], Response::HTTP_NOT_FOUND);
        }

        if ($invitation->isAccepted()) {
            return response()->json(['message' => 'Cette invitation a déjà été utilisée.'], Response::HTTP_CONFLICT);
        }

        if ($invitation->isExpired()) {
            return response()->json(['message' => 'Cette invitation a expiré.'], Response::HTTP_GONE);
        }

        return response()->json([
            'invitation' => [
                'email' => $invitation->email,
                'role' => $invitation->role->code,
                'expires_at' => $invitation->expires_at,
            ],
        ]);
    }

    public function activate(Request $request, string $token): JsonResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Password::min(10)->mixedCase()->numbers()],
        ]);

        $result = DB::transaction(function () use ($data, $token): array {
            $invitation = $this->findByToken($token, lockForUpdate: true);

            if (! $invitation) {
                return ['status' => Response::HTTP_NOT_FOUND, 'message' => 'Lien d’invitation invalide.'];
            }

            if ($invitation->isAccepted()) {
                return ['status' => Response::HTTP_CONFLICT, 'message' => 'Cette invitation a déjà été utilisée.'];
            }

            if ($invitation->isExpired()) {
                return ['status' => Response::HTTP_GONE, 'message' => 'Cette invitation a expiré.'];
            }

            $invitation->user->forceFill([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'password' => $data['password'],
                'email_verified_at' => now(),
                'activated_at' => now(),
            ])->save();

            $invitation->forceFill([
                'accepted_at' => now(),
            ])->save();

            return ['status' => Response::HTTP_OK, 'user' => $invitation->user->fresh('role')];
        });

        if ($result['status'] !== Response::HTTP_OK) {
            return response()->json(['message' => $result['message']], $result['status']);
        }

        $user = $result['user'];

        return response()->json([
            'message' => 'Compte activé.',
            'token' => $user->createToken('api-token')->plainTextToken,
            'user' => $user,
        ]);
    }

    private function findByToken(string $token, bool $lockForUpdate = false): ?Invitation
    {
        $query = Invitation::query()
            ->with(['role', 'user'])
            ->where('token', hash('sha256', $token));

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        return $query->first();
    }
}
