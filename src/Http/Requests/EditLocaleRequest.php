<?php

namespace Kodilab\LaravelI18n\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditLocaleRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        $rules = (new CreateLocaleRequest())->rules();

        $rules['ISO_639_1'] = ['required', 'string', 'max:3', 'min:2'];

        return $rules;
    }
}