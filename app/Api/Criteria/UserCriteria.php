<?php

namespace App\Api\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserCriteria
 */
class UserCriteria implements CriteriaInterface
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

        if(!empty($this->params['user_ids'])) {
            $query->whereIn('_id',$this->params['user_ids']);
        }

        if(!empty($this->params['user_id'])) {
            $query->where('_id',$this->params['user_id']);
        }

        if(!empty($this->params['filter']['name'])){
            $regexp = '%'.$this->params['filter']['name'].'%';
            $query->where('name', 'like', $regexp);
        }

        if(!empty($this->params['shop_id'])) {
            $query->where('shop_ids',$this->params['shop_id']);
        }

        if(!empty($this->params['sort_by_name'])) {
            $query->orderBy('name', 'asc');
        }
        
        
        //Set by shop_id
        // $auth = Auth::user();
        // $query->where('shop_ids',Auth::getPayload()->get('shop_id'));
        
        //Set language
        // $query->where('lang',app('translator')->getLocale());

        // $query->orderBy('created_at', 'desc');

        return $query;
    }
}
