<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
class PaymentMethod extends Model
{
    protected $table = 'payment_method';
    public function getPointByAmount($amout)
	{
		$point = DB::table('payments_amout')->where('amout',$amout )->first()->point;
		return($point);
	}
	public function getAmountByPoint($point)
	{
		$amout = DB::table('payments_amout')->where('point', $point )->first();
		if(empty($amout)) {
			return 0;
		}
		return $amout->amout;
			
	}
}
