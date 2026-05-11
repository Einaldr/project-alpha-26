<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncGroupMemberRolesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $groupId = $this->route("group")->id;

        return [
            'roles' => ['required', 'array','min:1'],
            'roles.*' => ['uuid', 'distinct', Rule::exists('group_roles', 'id')->where('group_id', $groupId)],
        ];
    }
}
