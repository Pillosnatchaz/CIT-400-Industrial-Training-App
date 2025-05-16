<?php

declare(strict_types=1);

namespace App\DataTables;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTablesEditor;

/**
 * @extends DataTablesEditor<User>
 */
class WarehousesDataTableEditor extends DataTablesEditor
{
    protected $model = Warehouse::class;

    /**
     * Get create action validation rules.
     */
    public function createRules(): array
    {
        return [
            'name' => 'required|max:255',
            'address' => 'required||max:255',
        ];
    }

    /**
     * Get edit action validation rules.
     */
    public function editRules(Model $model): array
    {
        return [
            'name' => 'sometimes|required|max:255',
            'address' => 'sometimes|required|max:255',
        ];
    }

    /**
     * Get remove action validation rules.
     */
    public function removeRules(Model $model): array
    {
        return [
            'DT_RowId' => 'required|not_in:'.auth()->id(),
        ];
    }

    protected function messages(): array
    {
        return [
            'DT_RowId.not_in' => 'You cannot delete record :values',
        ];
    }

    /**
     * Event hook that is fired after `creating` and `updating` hooks, but before
     * the model is saved to the database.
     */
    public function saving(Model $model, array $data): array
    {
        // Before saving the model, hash the password.
        if (! empty(data_get($data, 'password'))) {
            data_set($data, 'password', bcrypt($data['password']));
        }

        return $data;
    }

    /**
     * Event hook that is fired after `created` and `updated` events.
     */
    public function saved(Model $model, array $data): Model
    {
        // do something after saving the model

        return $model;
    }
}
