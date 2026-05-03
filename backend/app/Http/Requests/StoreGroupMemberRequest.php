<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * StoreGroupMemberRequest
 * 
 * @property-read string $email
 * @property-read array|null $roles
 */
class StoreGroupMemberRequest extends FormRequest
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
        $groupId = $this->route('group')->id ?? $this->route('group');

        return [
            'email' => ['required', 'email', 'max:255'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['required', 'uuid', 'distinct', Rule::exists('group_roles', 'id')->where('group_id', $groupId)]
        ];
    }
}
