<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class DataTable extends Component
{
    /**
     * Create a new component instance.
     */

    public $items;
    public $route;
    public $canEdit;
    public $canDelete;
    public $column;
    public $editRoutes = [];
    public $updateActiveRoutes = [];
    public $updateSttRoutes = [];
    public $deleteRoutes = [];

    public function __construct($items, $route, $column)
    {
        $this->items = $items;
        $this->route = trim($route, '/');
        $this->column = $column;
        $this->canEdit = $this->kiemTraQuyen('can_edit') || 0;
        $this->canDelete = $this->kiemTraQuyen('can_delete') || 0;
        
        $this->preprocessingRoutes();
    }

    public function preprocessingRoutes()
    {
        $this->editRoutes = [];
        $this->updateActiveRoutes = [];
        $this->updateSttRoutes = [];

        foreach($this->items as $item)
        {
            $this->editRoutes[$item->id] = "/{$this->route}/{$item->id}/edit";
            $this->updateActiveRoutes[$item->id] = "/{$this->route}/{$item->id}/toggle-active";
            $this->updateSttRoutes[$item->id] = "/{$this->route}/{$item->id}/update-stt";
            $this->deleteRoutes[$item->id] = "/{$this->route}/{$item->id}";
        }
    }
    
    protected function kiemTraQuyen($permission)
    {
        return \kiemTraQuyen($this->route, $permission);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.data-table');
    }
}
