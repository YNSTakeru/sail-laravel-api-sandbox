<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
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
            'article.title' => 'required|sometimes|max:255|unique:articles,title,i',
            'article.description' => 'required|sometimes|max:255',
            'article.body' => 'required|sometimes|max:1000',
            'article.tagList' => 'required|sometimes|array',
        ];
    }
}
