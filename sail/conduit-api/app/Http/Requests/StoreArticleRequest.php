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
        if(isset($this['article']['tagList']) && is_string($this['article']['tagList'])) {
            $this->merge([
                'article' => [
                    'tagList' => json_decode($this['article']['tagList'], true),
                ],
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
            'article.title' => 'required|max:255|unique:articles,title,i',
            'article.description' => 'required|max:255',
            'article.body' => 'required|max:1000',
            'article.tagList' => ['sometimes','array', 'max:10', 'unique_in_array'],
            'article.tagList.*' => ['string', 'max:255'],
        ];
    }
}
