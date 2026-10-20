<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAuthorizedStaff() ?? false;
    }

    public function rules(): array
    {
        return [
            'asset_id' => ['required', 'exists:assets,id'],
            'technician_id' => ['required', 'exists:users,id'],
            'issue' => ['required', 'string', 'max:1000'],
            'started_at' => ['required', 'date'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
