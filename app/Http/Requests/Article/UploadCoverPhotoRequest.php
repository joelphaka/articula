<?php


namespace App\Http\Requests\Avatar;


use Illuminate\Foundation\Http\FormRequest;

class UploadCoverPhotoRequest extends FormRequest
{
    public function rules()
    {
        return [
            'cover_photo'=> 'required|image|mimes:jpeg,png|max:2048'
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
