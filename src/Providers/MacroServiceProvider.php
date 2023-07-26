<?php

namespace Adminx\Common\Providers;

use Illuminate\Database\Eloquent\Builder as EBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //region Eloquent Macros
        EBuilder::macro('whereLike', function (array|string $attributes, string $searchTerm, bool $sensitive = false) {

            if(!$sensitive){
                $searchTerm = Str::lower($searchTerm);
            }

            $this->where(function (EBuilder $query) use ($attributes, $searchTerm, $sensitive) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        Str::contains($attribute, '.'),
                        function (EBuilder $query) use ($attribute, $searchTerm, $sensitive) {
                            [$relationName, $relationAttribute] = explode('.', $attribute);

                            $query->orWhereHas($relationName, function (EBuilder $query) use ($relationAttribute, $searchTerm, $sensitive) {
                                /*if(!$sensitive){
                                    $relationAttribute = DB::raw('lower(product)');
                                }*/
                                $query->where(!$sensitive ? DB::raw("lower({$relationAttribute})") : $relationAttribute, 'LIKE', "%{$searchTerm}%");
                            });
                        },
                        function (EBuilder $query) use ($attribute, $searchTerm, $sensitive) {
                            $query->orWhere(!$sensitive ? DB::raw("lower({$attribute})") : $attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            });

            return $this;
        });
        //endregion

        //region Collection macros

        Collection::macro('even', function () {
            return $this->filter(static fn($item, $key) => $key % 2 === 0);
        });

        Collection::macro('odd', function () {
            return $this->filter(static fn($item, $key) => $key % 2 !== 0);
        });

        Collection::macro('mapRecursive', function (callable $callback) {
            $items = $this->toArray();
            array_walk_recursive($items, $callback,);
            return new static($items);
        });
        Collection::macro('keyByValues', function () {
            return $this->keyBy(fn($item) => $item);
        });

        Collection::macro('dataGet', function ($key, $default = null) {
            return collect(data_get($this->toArray(), $key, $default));
        });


        Collection::macro('toExtensions', function () {
            return $this->map(function($item) {
                try{
                    return ".{$item}";
                }catch (\Exception $e){
                    dd($item);
                }
            });
        });

        Collection::macro('whereLike', function (array|string  $attributes, array|string $searchTerm, callable $escape) {
            return $this->filter(function($item) use($attributes, $searchTerm, $escape){

                $attributes = Collection::wrap($attributes)->toArray();

                $searchTerms = Collection::wrap($searchTerm)->map(fn($st) => Str::lower($st))->values()->toArray();

                foreach($attributes as $attr){
                    if(isset($item[$attr]) && Str::contains(Str::lower($item[$attr]), $searchTerms)){
                        return true;
                    }else if(($escape ?? false) && is_callable($escape) && $escape($item)){
                        return true;
                    }
                }

                return false;
            });
        });
        //endregion

        //region DB Builder Macros
        Builder::macro('whereLike', function (array|string  $attributes, array|string $searchTerm) {

            $terms = Collection::wrap($searchTerm);

            foreach ($terms as $term){
                $this->where(function (Builder $query) use ($attributes, $term) {
                    foreach (Arr::wrap($attributes) as $attribute) {
                        $query->orWhere($attribute, 'LIKE', "%{$term}%");
                    }
                });
            }

            return $this;
        });
        //endregion
    }
}
