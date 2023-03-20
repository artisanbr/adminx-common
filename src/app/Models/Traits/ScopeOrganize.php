<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * @property array $defaultOrganizeColumns
 */
trait ScopeOrganize
{

    private function getDefaultColumns()
    {
        return $this->defaultOrganizeColumns ?? ['title' => 'asc'];
    }

    /**
     *
     * @param Builder      $query
     * @param string|array<string,string>|string[] $columns = ['column' => 'direction']
     *
     * @return void
     */
    public function scopeOrganizeBy(Builder $query, string|array $columns){
        $this->scopeOrganize($query, $columns);
    }

    /**
     * @param Builder      $query
     * @param string|array<string,string>|string[] $columns = ['column' => 'direction']
     *
     * @return Builder
     */
    public function scopeOrganize(Builder $query, string|array $columns = []): Builder
    {
        if (empty($columns)) {
            $columns = $this->getDefaultColumns();
        }

        $columns = is_array($columns) ? $columns : [$columns];

        if (array_is_list($columns)) {
            foreach ($columns as $column) {
                $query->orderBy($column);
            }
        }
        else {
            foreach ($columns as $column => $direction) {
                if(is_string($column)){
                    $query->orderBy($column, $direction);
                }
                else{
                    $query->orderBy($direction);
                }
            }
        }

        return $query->orderBy('updated_at')->orderBy('created_at');
    }
}
