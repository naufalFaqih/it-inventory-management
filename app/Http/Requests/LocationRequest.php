<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $locationId = $this->route('location')?->id ?? $this->route('location');

        return [
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => [
                'nullable',
                'exists:locations,id',
                function ($attribute, $value, $fail) use ($locationId) {
                    if ($locationId && (int) $value === (int) $locationId) {
                        $fail('Lokasi induk (parent) tidak boleh berupa lokasi yang sama.');
                    }
                },
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
