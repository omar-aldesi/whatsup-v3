<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use App\Rules\MessageFileValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CampaignRequest extends FormRequest
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
        $rules = [
            'message' => 'required',
            'name' => 'required',
            'channel' => ['required', Rule::in([Campaign::WHATSAPP,Campaign::SMS, Campaign::EMAIL])],
            'schedule_date' => 'required|date',
            'repeat_number' => 'required|numeric',
            'subject' => 'required_if:channel,email',
            'smsType' => 'required_if:channel,sms',
            'repeat_format' => 'required|in:year,month,day',
            
        ];

        $fileFields = ['document', 'audio', 'image', 'video'];

        foreach ($fileFields as $field) {
            if ($this->hasFile($field)) {
                $rules[$field] = ['required', new MessageFileValidationRule($field)];
                $rules['message'] = [];
                break;
            }
        }

        return $rules;
    }
}
