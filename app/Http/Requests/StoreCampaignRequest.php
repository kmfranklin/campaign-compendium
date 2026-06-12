<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * - For now: always true; policy handles actual permissions.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * - Placeholder: will define real rules later.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'invite_emails' => ['nullable', 'array', 'max:12'],
            'invite_emails.*' => ['bail', 'required', 'email:rfc', 'distinct:ignore_case'],
        ];
    }

    public function messages(): array
    {
        return [
            'invite_emails.max' => 'You can queue up to 12 invite emails when creating a campaign.',
            'invite_emails.*.email' => 'Each queued invite must use a valid email address.',
            'invite_emails.*.distinct' => 'Each invite email only needs to be added once.',
        ];
    }
}
