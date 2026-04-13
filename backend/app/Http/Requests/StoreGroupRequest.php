<?php

namespace App\Http\Requests;

use App\Enum\GroupType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
