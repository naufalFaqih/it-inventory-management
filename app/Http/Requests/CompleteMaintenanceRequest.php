<?php

namespace App\Http\Requests;

use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAuthorizedStaff() ?? false;
    }

    public function rules(): array
    {
        return [
            'action_taken' => ['required', 'string', 'max:1000'],
            'completed_at' => ['required', 'date'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'condition_after' => ['required', Rule::in([Asset::CONDITION_GOOD, Asset::CONDITION_MINOR_DAMAGE, Asset::CONDITION_MAJOR_DAMAGE])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
