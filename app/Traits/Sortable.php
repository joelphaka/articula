<?php


namespace App\Traits;


trait Sortable
{
    /**
     * @return string[]
     */
    public function getSortableColumns(): array
    {
        return isset($this->sortableColumns) && is_array($this->sortableColumns)
            ? $this->sortableColumns
            : ['id', 'created_at'];
    }
}
