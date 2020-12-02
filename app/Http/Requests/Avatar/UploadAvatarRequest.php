<?php


namespace App\Http\Requests\Avatar;


use Illuminate\Foundation\Http\FormRequest;

class UploadAvatarRequest extends FormRequest
{
    public function rules()
    {
        return [
            'avatar'=> 'required|image|mimes:jpeg,png|max:2048'
        ];
    }

    public function messages()
    {
        return [
            'avatar.required' => 'Please choose an image.',
            'avatar.image' => 'The file must be an image of type jpeg or png.',
            'avatar.mimes' => 'The file must be an image of type jpeg or png.',
            'avatar.max' => 'The file may not be greater than 2MB'
        ];
    }
}
