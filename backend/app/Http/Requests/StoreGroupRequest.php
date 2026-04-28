<?php

namespace App\Http\Requests;

use App\Enum\GroupType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * StoreGroupRequest
 * 
 * Handles validation for group creation request.
 * Enforces a strict 2-deep hierarchy:
 * - Root level: Must be type 'ORG'.
 * - Child level: Must have a parent 'ORG' and be type 'TEAM'.
 * 
 * @property-read string $name
 * @property-read string|null $parent_id
 * @property-read bool|null $is_private_child
 * @property-read \App\Enum\GroupType $type
 * @property-read string|null $billing_email
 * @property-read \Illuminate\Http\UploadedFile|null $icon
 */
class StoreGroupRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:5', 'max:255'],
            'parent_id' => ['nullable', 'string', 'min:36', 'max:36', Rule::exists('groups', 'id')->where('type', GroupType::ORG)],
            'is_private_child' => [Rule::requiredIf($this->filled('parent_id')), 'boolean'],
            'type' => ['required', $this->filled('parent_id') ? Rule::in([GroupType::TEAM]) : Rule::in([GroupType::ORG])],
            'billing_email' => ['nullable', 'string', 'email', 'max:255'],
            'icon' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:5000'],
        ];
    }
}
