<?php

namespace Veterinaria\Http\Requests;

use Veterinaria\Http\Requests\Request;

class ProductRequest extends Request
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
            'name'              => 'required',
            'product_type_id'   => 'required|numeric',
            'provider_id'       => 'required|numeric',
            'quantity'          => 'required|numeric'
        ];
    }
}