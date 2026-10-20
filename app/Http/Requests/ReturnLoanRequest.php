<?php

namespace App\Http\Requests;

use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReturnLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAuthorizedStaff() ?? false;
    }

    public function rules(): array
    {
        return [
            'returned_at' => ['required', 'date'],
            'return_condition' => ['required', Rule::in([Asset::CONDITION_GOOD, Asset::CONDITION_MINOR_DAMAGE, Asset::CONDITION_MAJOR_DAMAGE])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
