<?php

namespace App\Api\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Support\Facades\Auth;

/**
 * Class BranchCriteria
 */
class ShopCriteria implements CriteriaInterface
{
    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * Apply criteria in query repository
     *
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $query = $model->newQuery();


        if (!empty($this->params['shop_name'])) {
            $query->where('shop_name', $this->params['shop_name']);

            if (!empty($this->params['name'])) {
                $query->where('name', $this->params['name']);
            }
            if (!empty($this->params['shop_username'])) {
                $query->where('username', $this->params['shop_username']);

            }
            if (!empty($this->params['shop_id'])) {
                $query->where('_id', mongo_id($this->params['shop_id']));
            }

            if (!empty($this->params['seller_id'])) {
                $query->where('seller_id', $this->params['seller_id']);
            }


//
            $query->orderBy('sort_index', 'asc');
            $query->orderBy('updated_at', 'asc');
            //Set language
            // $query->where('lang',app('translator')->getLocale());
            return $query;
        }
    }
}
