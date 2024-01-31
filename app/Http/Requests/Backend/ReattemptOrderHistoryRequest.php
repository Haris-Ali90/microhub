<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request;

class ReattemptOrderHistoryRequest extends Request
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

            $after_or_equal_to = $yesterday = date('Y-m-d', strtotime($request['start_date'] .'-1 days'));

            return [
                'start_date' => 'required_without_all:status,end_date|date',
                'end_date' => ['required_without_all:status,start_date','after:'.$after_or_equal_to.''],
                'status' => 'required_without_all:start_date,end_date',
            ];
       }
       else
       {
               return [];
       }
    }
}
