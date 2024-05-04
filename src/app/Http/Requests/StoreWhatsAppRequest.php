<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ScheduleLimitRule;

class StoreWhatsAppRequest extends FormRequest
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
        return [
            'schedule_date' => ['required_if:schedule,2', new ScheduleLimitRule(request()->schedule_date)],
            'group_id'      => 'nullable|array|min:1',
            'group_id.*'    => 'nullable|exists:groups,id',
            'message'       => 'required'
        ];
    }
}
