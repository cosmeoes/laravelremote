<?php

namespace App\Http\Requests;

use App\Rules\GreaterThanIfPresent;
use Illuminate\Foundation\Http\FormRequest;

class PurshaseJobPostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company' => 'required',
            'position' => 'required',
            'job_type' => 'required',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'location' => 'required',
            'salary_min' => 'nullable|integer|min:0',
            'salary_max' => ['nullable', 'required_with:salary_min', 'integer', 'min:0', new GreaterThanIfPresent('salary_min')],
            'body' => 'required',
            'apply_url' => 'required|url',
            'sticky' => 'nullable|boolean',
            'with_company_color' => 'nullable|boolean',
            'company_color' => 'required_if:with_company_color,true|regex:/^#[a-fA-F0-9]{6}$/',
            'with_logo' => 'nullable|boolean',
            'logo' => 'required_if:with_logo,true|file',
        ];
    }
}
