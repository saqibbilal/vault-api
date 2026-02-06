<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
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
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'type'    => ['required', 'string', 'in:note,file'],
            // 'file' is required only if 'type' is 'file'
            'file'    => [
                'required_if:type,file',
                'file',
                'mimes:pdf,jpg,jpeg,png,docx,doc',
                'max:10240' // 10MB limit
            ],
        ];
    }
}
