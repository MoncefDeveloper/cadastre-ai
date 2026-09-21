<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Public lead submission from Framer landing page is unrestricted.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Pre-validation sanitization: Strips executable markup and trims leading/trailing whitespace
     * before inputs enter the validation rules matrix or persist to the database.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'    => strip_tags(trim((string) ($this->name ?? ''))),
            'phone'   => filled($this->phone) ? strip_tags(trim((string) $this->phone)) : null,
            'subject' => strip_tags(trim((string) ($this->subject ?? ''))),
            'message' => strip_tags(trim((string) ($this->message ?? ''))),
        ]);
    }

    /**
     * Validation rules for incoming advisory lead inquiries.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'min:2', 'max:100'],
            'email'        => ['required', 'string', 'email:rfc', 'max:255'], // Trap Guard: Strictly avoids dns check to prevent false 422s from DNS latency
            'phone'        => ['nullable', 'string', 'max:50'],
            'subject'      => ['required', 'string', 'min:3', 'max:255'],
            'message'      => ['required', 'string', 'min:10', 'max:5000'],
            '_cadastre_hp' => ['nullable', 'string', 'max:100'], // Anti-spam honeypot
        ];
    }
}
