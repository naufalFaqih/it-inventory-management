<?php

namespace App\Http\Requests;

use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAuthorizedStaff() ?? false;
    }

    public function rules(): array
    {
        $assetId = $this->route('asset')?->id ?? $this->route('asset');

        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'serial_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('assets', 'serial_number')->ignore($assetId)->whereNull('deleted_at'),
            ],
            'location_id' => ['required', 'exists:locations,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'condition' => ['required', Rule::in([Asset::CONDITION_GOOD, Asset::CONDITION_MINOR_DAMAGE, Asset::CONDITION_MAJOR_DAMAGE])],
            'status' => ['required', Rule::in([Asset::STATUS_AVAILABLE, Asset::STATUS_ASSIGNED, Asset::STATUS_BORROWED, Asset::STATUS_MAINTENANCE, Asset::STATUS_RETIRED])],
            'purchase_date' => ['nullable', 'date'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
