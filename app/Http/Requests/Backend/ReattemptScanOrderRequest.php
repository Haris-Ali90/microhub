<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request;

class ReattemptScanOrderRequest extends Request
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
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [

        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $request = $this->all();

        if (count($request) > 0){

            return [
              //  'search_date' => 'required_without_all:status|date',
              //  'status' => 'required_without_all:search_date',
            ];
       }
       else
       {
               return [];
       }
    }
}
