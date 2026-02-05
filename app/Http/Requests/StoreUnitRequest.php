<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:units,name',
            'short_name' => 'required|string|max:10|unique:units,short_name',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|boolean'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Unit name is required.',
            'name.unique' => 'Unit name already exists.',
            'short_name.required' => 'Short name is required.',
            'short_name.unique' => 'Short name already exists.',
            'short_name.max' => 'Short name may not be greater than 10 characters.',
            'description.max' => 'Description may not be greater than 1000 characters.',
            'status.required' => 'Status is required.',
        ];
    }
}