<?php


namespace App\Http\Requests\User;


use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'first_name' => 'sometimes|required|min:2',
            'last_name' => 'sometimes|required|min:2',
            'gender' => 'nullable|in:0,1',
            'bio' => 'nullable|min:40',
        ];
    }
}
