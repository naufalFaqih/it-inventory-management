<?php

namespace App\Http\Requests;

use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAuthorizedStaff() ?? false;
    }

    public function rules(): array
    {
        return [
            'asset_id' => ['required', 'exists:assets,id'],
            'borrower_id' => ['required', 'exists:users,id'],
            'borrowed_at' => ['required', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:borrowed_at'],
            'checkout_condition' => ['required', Rule::in([Asset::CONDITION_GOOD, Asset::CONDITION_MINOR_DAMAGE, Asset::CONDITION_MAJOR_DAMAGE])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
