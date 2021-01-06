<?php


namespace App\Http\Requests\Comment;


use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'content' => 'required|max:255',
        ];
    }
}
