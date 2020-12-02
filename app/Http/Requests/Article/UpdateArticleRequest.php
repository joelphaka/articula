<?php


namespace App\Http\Requests\Article;


use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{

    public function rules()
    {
        return [
            'title' => 'sometimes|required|min:6|max:60',
            'content' => 'sometimes|required|min:40',
            'cover_photo' => 'sometimes|required|image|mimes:jpeg,png|max:2048'
        ];
    }

    public function messages()
    {
        return [
            'cover_photo.required' => 'Please choose an image.',
            'cover_photo.image' => 'The file must be an image of type jpeg or png.',
            'cover_photo.mimes' => 'The file must be an image of type jpeg or png.',
            'cover_photo.max' => 'The file may not be greater than 2MB'
        ];
    }

}
