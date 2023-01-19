<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GameMoveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'token' => 'required|exists:games',
            'player' => ['required','integer', Rule::in([1,2])],
            'row' => ['required','integer', Rule::in([0,1,2])],
            'col' => ['required','integer', Rule::in([0,1,2])],
        ];
    }
}
