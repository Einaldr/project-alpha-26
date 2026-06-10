<?php

namespace App\Http\Requests;

use App\Enum\GitAuthType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
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
            // Project validation
            'name' => ['required', 'string', 'max:64'],
            'description' => ['nullable', 'string'],
            'git_url' => ['required', 'string'],
            'default_branch' => ['nullable', 'string'],
            'background_image' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:5000'],
            
            // Nested Secrets validation
            'auth_type' => ['nullable', Rule::enum(GitAuthType::class)],
            'access_token' => [
                'nullable', 
                'string', 
                'max:255', 
                'required_if:auth_type,https' // Only required if authentication type is HTTPS
            ],
        ];
    }
}
