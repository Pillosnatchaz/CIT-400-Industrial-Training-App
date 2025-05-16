<?php

declare(strict_types=1);

namespace App\DataTables;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\support\Facades\Auth;
use Yajra\DataTables\DataTablesEditor;


/**
 * @extends DataTablesEditor<User>
 */
class ProjectsDataTableEditor extends DataTablesEditor
{
    protected $model = Project::class;

    /**
     * Get create action validation rules.
     */
    public function createRules(): array
    {
        return [
            'name' => 'required|max:255',
            'client_name' => 'required|max:255',
            'start_range' => 'required|max:255',
            'end_range' => 'required|max:255|date',
            'location' => 'required|max:255',
            'description' => 'nullable|max:255',
        ];
    }

    /**
     * Get edit action validation rules.
     */
    public function editRules(Model $model): array
    {
        return [
            'name' => 'required|max:255',
            'client_name' => 'required|max:255',
            'start_range' => 'required|max:255',
            'end_range' => 'required|max:255|date',
            'location' => 'required|max:255',
            'description' => 'required|max:255',
        ];
    }

    public function creating(Model $model, array $data): array
{
    // ... other fields

    $data['created_by'] = Auth::id(); // Get the ID of the logged-in user

    return $data;
}

    /**
     * Get remove action validation rules.
     */
    // public function removeRules(Model $model): array
    // {
    //     return [
    //         'DT_RowId' => 'required|not_in:'.auth()->id(),
    //     ];
    // }

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
