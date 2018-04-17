<?php

namespace Yajra\DataTables;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ApiResourceDataTable extends CollectionDataTable
{
    /**
     * Collection object.
     *
     * @var \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public $collection;

    /**
     * Collection object.
     *
     * @var \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public $original;

    /**
     * Can the DataTable engine be created with these parameters.
     *
     * @param mixed $source
     * @return bool
     */
    public static function canCreate($source)
    {
        return $source instanceof AnonymousResourceCollection;
    }

    /**
     * Factory method, create and return an instance for the DataTable engine.
     *
     * @param \Illuminate\Http\Resources\Json\AnonymousResourceCollection $source
     * @return ApiResourceDataTable|DataTableAbstract
     */
    public static function create($source)
    {
        return parent::create($source);
    }

    /**
     * CollectionEngine constructor.
     *
     * @param \Illuminate\Http\Resources\Json\AnonymousResourceCollection $collection
     */
    public function __construct(AnonymousResourceCollection $collection)
    {
        $this->request    = app('datatables.request');
        $this->config     = app('datatables.config');
        $this->collection = collect($collection->toArray($this->request));
        $this->original   = $collection;
        $this->columns    = array_keys($this->serialize(collect($collection->toArray($this->request))->first()));
        if ($collection->resource instanceof LengthAwarePaginator) {
            $this->isFilterApplied = true;
        }
    }

    /**
     * Count total items.
     *
     * @return int
     */
    public function totalCount()
    {
        if ($this->original->resource instanceof LengthAwarePaginator) {
            return $this->totalRecords ? $this->totalRecords : $this->original->resource->total();
        } else {
            return $this->totalRecords ? $this->totalRecords : $this->collection->count();
        }
    }

    // /**
    //  * Count results.
    //  *
    //  * @return int
    //  */
    // public function count()
    // {
    //     if($this->original->resource instanceof LengthAwarePaginator) {
    //         return intval($this->request->length) == 0 ? $this->collection->count() : intval($this->request->length);
    //     } else {
    //         return $this->collection->count() > $this->totalRecords ? $this->totalRecords : $this->collection->count();
    //     }
    // }
}
