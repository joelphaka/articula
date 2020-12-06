<?php


namespace App\Http\Requests\Article;


use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{

    public function rules()
    {
        $maxFilesize = config('filesystems.maxFilesize');

        return [
            'title' => 'sometimes|required|min:6|max:60',
            'content' => 'sometimes|required|min:40',
            'cover_photo' => "sometimes|required|mimetypes:image/jpeg,image/png|max:{$maxFilesize}"
        ];
    }

    public function messages()
    {
        $filesizeInMegabytes = config('filesystems.maxFilesize') / 1024;

        return [
            'cover_photo.required' => 'Please choose an image.',
            'cover_photo.mimetypes' => 'The file must be an image of type jpeg or png.',
            'cover_photo.max' => "The file may not be greater than {$filesizeInMegabytes}MB"
        ];
    }

}
