<?php

namespace App\Http\Resources\v1;

use App\Enums\TokenAbility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'success' => true,
            'message' => 'user details',
            'data'    => [
                'user_id'             => $this->id,
                'name'                => $this->name,
                'email'               => $this->email,
                'access_token'        => $this->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.ac_expiration')))->plainTextToken,
                'refresh_token'        => $this->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.rt_expiration')))->plainTextToken,
                'roles'               => $this->roles->pluck('name') ?? [],
                'roles.permissions'   => $this->getPermissionsViaRoles()->pluck('name') ?? [],
                'permissions'         => $this->permissions->pluck('name') ?? [],
            ]

        ];
    }
}
