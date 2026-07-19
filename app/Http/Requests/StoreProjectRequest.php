<?php

namespace App\Http\Requests;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin === true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $project = $this->route('project');
        $projectId = $project instanceof Project ? $project->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('projects', 'slug')->ignore($projectId)],
            'province_id' => ['required', 'exists:provinces,id'],
            'investor_id' => ['nullable', 'exists:investors,id'],
            'district' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'former_address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', new Enum(ProjectStatus::class)],
            'application_start_at' => ['nullable', 'date'],
            'application_end_at' => ['nullable', 'date', 'after_or_equal:application_start_at'],
            'total_units' => ['nullable', 'integer', 'min:0'],
            'price_from' => ['nullable', 'integer', 'min:0'],
            'price_to' => ['nullable', 'integer', 'min:0'],
            'area_from' => ['nullable', 'integer', 'min:0'],
            'area_to' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'application_guide' => ['nullable', 'string'],
            'source_name' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'url', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'published' => ['boolean'],
        ];
    }
}
