<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAuthorizedStaff() ?? false;
    }

    public function rules(): array
    {
        return [
            'asset_id' => ['required', 'exists:assets,id'],
            'to_location_id' => ['required', 'exists:locations,id'],
            'to_user_id' => ['nullable', 'exists:users,id'],
            'moved_at' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
