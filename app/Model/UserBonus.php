<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;


class UserBonus extends Model
{
    protected $table="jy_user_bonus";
    public $timestamps = true;


    //红包的发送及记录
    public  function getSendRecord($where = [])
    {
        $records = self::select('jy_user_bonus.id','username','phone','honus_name','start_time','end_time','jy_user_bonus.status')
                    ->leftJoin('jy_bonus', 'jy_bonus.id','=','jy_user_bonus.bonus_id')
                    ->leftJoin('jy_user','jy_user.id','=','jy_user_bonus.user_id')
                    ->where($where)
                    ->orderBy('jy_user_bonus.id','desc')
                    ->paginate(5);
        return $records;
    }
    //通过用户uid获取记录列表
    public function getRecordByUid($userId, $where=[])
    {
        $record = self::select('honus_name','money','min_money','start_time','end_time')
                    ->leftJoin('jy_bonus', 'jy_bonus.id','=','jy_user_bonus.bonus_id')
                    ->where('user_id', $userId)
                    ->where($where)
                    ->orderBy('jy_user_bonus.id','desc')
                    ->get()
                    ->toArray();
        return $record;
    }
    //执行红包发送的操作
    public function sendBonusMany($userIds, $bonusId, $expires=7)
    {
        $bonusInfo = [];
        foreach ($userIds as $key => $value) {
            $bonusInfo[] = [
                'user_id' =>$value,
                'bonus_id' => $bonusId,
                'start_time' => date("Y-m-d H:i:s"),
                'end_time'   => date("Y-m-d H:i:s", strtotime('+ '.$expires.' days')),
            ];
        }
        return self::insert($bonusInfo);
    }
}
