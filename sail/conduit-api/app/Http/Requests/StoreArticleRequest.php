<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation()
    {
        if(is_string($this->tagList)) {
            $this->merge([
                'tagList' => json_decode($this->tagList, true),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'title' => 'required|max:255|unique:articles,title,i',
            'description' => 'required|max:255',
            'body' => 'required|max:1000',
            'tagList' => ['array', 'max:10', 'unique_in_array'],
            'tagList.*' => ['string', 'max:255'],
        ];
    }
}
