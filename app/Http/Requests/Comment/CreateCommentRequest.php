<?php


namespace App\Http\Requests\Comment;


use Illuminate\Foundation\Http\FormRequest;

class CreateCommentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'content' => 'required|min:1|max:255',
        ];
    }
}
