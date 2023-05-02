<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:50',
            'mokam_id' => 'nullable|integer|exists:App\Models\Mokam,id',
            'showroom_id' => 'required|integer|exists:App\Models\Showroom,id',
            'genus' => 'required|max:50',
            'email' => 'nullable|max:50|email',
            'phone' => 'nullable|max:50',
            'address' => 'nullable|max:150',
            'description' => 'nullable|max:500',
            'balance' => 'nullable|numeric|min:0',
        ];
    }
}
