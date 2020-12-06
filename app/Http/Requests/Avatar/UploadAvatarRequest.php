<?php


namespace App\Http\Requests\Avatar;


use Illuminate\Foundation\Http\FormRequest;

class UploadAvatarRequest extends FormRequest
{
    public function rules()
    {
        $maxFilesize = config('filesystems.maxFilesize');

        return [
            'avatar'=> "required|mimetypes:image/jpeg,image/png|max:{$maxFilesize}"
        ];
    }

    public function messages()
    {
        $filesizeInMegabytes = config('filesystems.maxFilesize') / 1024;

        return [
            'avatar.required' => 'Please choose an image.',
            'avatar.mimetypes' => 'The file must be an image of type jpeg or png.',
            'avatar.max' => "The file may not be greater than {$filesizeInMegabytes}MB"
        ];
    }
}
