<?php

namespace App\Api\Repositories\Eloquent;

use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\PositionRepository;
use App\Api\Entities\Position;
use App\Api\Validators\PositionValidator;

/**
 * Class PositionRepositoryEloquent
 */
class PositionRepositoryEloquent extends BaseRepository implements PositionRepository
{
    protected $userRepository;
    protected $positionRepository;
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Position::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
//    public function deletePosition($id,$limit =0)
//    {
//        //Nếu client trả về 1 array id, vd như check vào các checkbox sẽ xóa những item dc check
//        if(is_array($id))
//        {
//            foreach($id as $row)
//            {
//                $user=$this->userRepository->deleteWhere(['shop_id'=>mongo_id($row->id)]);
//                $position=$this->positionRepository->deleteWhere(['shop_id'=>mongo_id($row->id)]);
//            }
//
//        }
//        //Chỉ check 1 item thôi
//        else
//        {
//            $user=$this->userRepository->deleteWhere(['shop_id'=>mongo_id($id)]);
//            $position=$this->positionRepository->deleteWhere(['shop_id'=>mongo_id($id)]);
//        }
//        return $this->successRequest("Đã xóa thành công");
//    }
}
