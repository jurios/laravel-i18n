<?php

namespace Kodilab\LaravelI18n\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Kodilab\LaravelI18n\Models\Locale;

class CreateLocaleRequest extends FormRequest
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
        return [
            'ISO_639_1' => ['required', 'string', 'max:3', 'min:2', function ($attribute, $value, $fail) {
                $region = $this->request->get('region');

                if (!Locale::where('ISO_639_1', $value)->where('region', $region)->get()->isEmpty())
                {
                    return $fail('This locale already exists');
                }
            }],
            'region' => 'nullable|string|max:3|min:2',
            'description' => 'nullable|string|max:255',
            'dialect_of_id' => 'nullable|numeric',
            'carbon_locale' => 'nullable|string|max:3|min:2',
            'carbon_tz' => 'nullable|string',
            'laravel_locale' => 'nullable|string|max:3|min:2',
            'currency_number_decimals' => 'nullable|integer',
            'currency_decimals_punctuation' => 'nullable|string|size:1',
            'currency_thousands_separator' => 'nullable|string|size:1',
            'currency_symbol' => 'nullable|string',
            'currency_symbol_position' => 'nullable|string'
        ];
    }
}