<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateGroupRequest
 * 
 * Handles validation for group metadata updates.
 * Supports partial updates (PATCH) where all fields are optional.
 * 
 * @property-read string|null $name
 * @property-read \Illuminate\Http\UploadedFile|null $icon
 * @property-read string|null $billing_email
 */
class UpdateGroupRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'min:5', 'max:255'],
            'icon' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:5000'],
            'billing_email' => ['nullable', 'string', 'email', 'max:255']
        ];
    }
}
