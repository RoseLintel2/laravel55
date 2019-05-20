<?php

namespace App\Listeners;

use App\Events\Event;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Model\Member;
use App\Model\UserBonus;

class BonusListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Event  $event
     * @return void
     */
    public function handle(Event $event)
    {

        //事件机制，用户注册成功之后给用户发红包
        $user = new Member();

        $userInfo  = $user->getkkInfo($event->data);
        //用户红包的数据
        $userBonusData = [
            'user_id' => $userInfo->id,
            'bonus_id' => 5,
            'start_time' => date("Y-m-d H:i:s"),
            'end_time'   => date("Y-m-d H:i:s",time()+604800),
        ];

        $userBonus = new UserBonus();

        \DB::table('jy_user_bonus')->insert($userBonusData);

        \Log::info('测试事件机制发红包成功',[$event->data]);
        //dd($event->data);
    }
}
