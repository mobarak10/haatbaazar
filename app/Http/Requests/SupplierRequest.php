<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
            'showroom_id' => 'required|integer|exists:App\Models\Showroom,id',
            'company_name' => 'required|max:50',
            'name' => 'required|max:50',
            'genus' => 'required|max:50',
            'email' => 'nullable|max:50|email',
            'phone' => 'nullable|max:50',
            'address' => 'nullable|max:150',
            'description' => 'nullable|max:500',
            'balance' => 'nullable|numeric',
        ];
    }
}
