<?php

namespace App\Http\Requests;

use App\Domains\Core\Services\ContextManager;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CleaningWorkerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('cleaning.workers.manage') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $workerId = $this->worker ? $this->worker->id : 'NULL';
        $buId = app(ContextManager::class)->getActiveBusinessUnitId();

        return [
            'worker_id' => 'required|string|max:50|unique:cleaning_workers,worker_id,'.$workerId.',id,business_unit_id,'.$buId,
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone_number' => 'nullable|string|max:20',
            'id_number' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'current_branch_id' => 'nullable|exists:branches,id',
            'current_supervisor_id' => 'nullable|exists:users,id',
        ];
    }
}
