<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required',
            'plan_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'payment_date' => 'required|date',
            'status' => 'required',
        ];
    }
}
