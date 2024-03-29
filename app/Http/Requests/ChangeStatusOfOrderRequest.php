<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed order_id
 * @property mixed status
 */
class ChangeStatusOfOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $statuses = implode(",", config("statics.statuses"));
        return [
            "order_id" => "required|exists:orders,id",
            "status" => "required|in:$statuses"
        ];
    }
}
