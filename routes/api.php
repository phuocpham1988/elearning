<?php
use Illuminate\Http\Request;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
// use DB;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
Route::get('/dotthi', function (Request $request) {
    return \App\ExamSeriesfree::where('start_date','<=',\Carbon\Carbon::now())->orderBy('id','desc')->get();
});
Route::get('/dotthimoinhat', function (Request $request) {
    return \App\ExamSeriesfree::where('start_date','<=',\Carbon\Carbon::now())->orderBy('id','desc')->first();
});
Route::get('/ketqua', function (Request $request) {
    return \App\QuizResultfinish::join('users','users.id','=','quizresultfinish.user_id')
                  ->join('examseries','examseries.id','=','quizresultfinish.examseri_id')
                  ->select(['users.name', 'quizresultfinish.*'])
                  ->where('exam_free_id','=',$request->id)
                  ->where('examseries.category_id','=',$request->trinhdo)
                  ->where('finish','=',3)
                  ->where('total_marks','>',0)
                  ->orderBy('total_marks','desc')->get();
});
Route::get('/diemthimoi', function (Request $request) {
    return \App\QuizResultfinish::join('users','users.id','=','quizresultfinish.user_id')
                  ->join('examseries','examseries.id','=','quizresultfinish.examseri_id')
                  ->select(['users.name', 'quizresultfinish.*'])
                  ->where('exam_free_id','=', $request->dotthi)
                  ->where('examseries.category_id','=',$request->trinhdo)
                  ->where('finish','=',3)
                  ->where('total_marks','>',0)
                  ->orderBy('total_marks','desc')
                  ->take(5)
                  ->get();
});
Route::post('auth/adminlogin', 'UserController@adminLogin');
Route::post('auth/login', 'UserController@login');
Route::post('auth/loginhid', 'UserController@uid');
Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('auth/user-info', 'UserController@getUserInfo');
    Route::get('auth/logout', 'UserController@logout');
    Route::post('auth/register', 'UserController@register');
    Route::get('auth/list', 'UserController@listuid');
    Route::get('auth/refresh', 'UserController@refresh');
});
//Route::middleware('jwt.refresh')->get('/token/refresh', 'UserController@refresh');
Route::post('/payments/momoipn', function (Request $request) {
  if (!empty($_POST)) {
    $serectkey = env('MOMO_SECRET');
    if(env('DEMO_MODE')) {
      $serectkey   = env('MOMO_SECRET_TEST');
    }
    $partnerCode = $_POST["partnerCode"];
    $accessKey = $_POST["accessKey"];
    $orderId = $_POST["orderId"];
    $localMessage = $_POST["localMessage"];
    $message = $_POST["message"];
    $transId = $_POST["transId"];
    $orderInfo = $_POST["orderInfo"];
    $amount = $_POST["amount"];
    $errorCode = $_POST["errorCode"];
    $responseTime = $_POST["responseTime"];
    $requestId = $_POST["requestId"];
    $extraData = $_POST["extraData"];
    $payType = $_POST["payType"];
    $orderType = $_POST["orderType"];
    $m2signature = $_POST["signature"]; //MoMo signature
    //Checksum
    $rawHash = "partnerCode=" . $partnerCode . "&accessKey=" . $accessKey . "&requestId=" . $requestId . "&amount=" . $amount . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo .
       "&orderType=" . $orderType . "&transId=" . $transId . "&message=" . $message . "&localMessage=" . $localMessage . "&responseTime=" . $responseTime . "&errorCode=" . $errorCode .
       "&payType=" . $payType . "&extraData=" . $extraData;
    $partnerSignature = hash_hmac("sha256", $rawHash, $serectkey);
    if ($m2signature == $partnerSignature) {
        if ($errorCode == '0') {
          $requestId_info = explode('_',  $requestId);
          DB::beginTransaction();
          try {

            $payment = new \App\PaymentMethod();
            $payment->user_id = $requestId_info[0];
            $payment->item_id = $requestId_info[1];
            $payment->type = $requestId_info[2];
            $payment->item_name = $orderInfo;
            $payment->amount = $amount;
            $payment->requestId = $requestId;
            $payment->orderId = $orderId;
            $payment->orderInfo = $orderInfo;
            $payment->transId      = $transId;
            $payment->orderType = $orderType;
            $payment->payType = $payType;
            $payment->extraData    = $extraData;
            $payment->responseTime = $responseTime; 
            $payment->status = 1; //Update Giao dich thanh công 0=>1
            $payment->save();

            $lmsseries_combo = DB::table('lmsseries_combo')->where('id',$requestId_info[1])->first();

            for ($i=1; $i <=5 ; $i++) {
              $n = 'n' . $i; 
              if ($lmsseries_combo->$n > 0) {
                DB::table('payments')->insert([
                    'user_id' => $requestId_info[0],
                    'item_id' => $lmsseries_combo->$n,
                    'time' => $lmsseries_combo->time,
                    'payments_method_id' => $payment->id,
                ]);
              }
            }


              // Commit Transaction
              DB::commit();
          } catch (\Exception $e) {
              // Rollback Transaction
              DB::rollback();
          }
          return 1;
        } 
    } 
  }
  return 0;
});
Route::post('/payments/atmipn', function (Request $request) {
  if (!empty($_POST)) {
    $serectkey   = env('MOMO_SECRET');
    if(env('DEMO_MODE')) {
      $serectkey   = env('MOMO_SECRET_ATM_TEST');
      // $serectkey    = 'mD9QAVi4cm9N844jh5Y2tqjWaaJoGVFM';
    }
    $partnerCode = $_POST["partnerCode"];
    $accessKey = $_POST["accessKey"];
    $orderId = $_POST["orderId"];
    $localMessage = $_POST["localMessage"];
    $message = $_POST["message"];
    $transId = $_POST["transId"];
    $orderInfo = $_POST["orderInfo"];
    $amount = $_POST["amount"];
    $errorCode = $_POST["errorCode"];
    $responseTime = $_POST["responseTime"];
    $requestId = $_POST["requestId"];
    $extraData = $_POST["extraData"];
    $payType = $_POST["payType"];
    $orderType = $_POST["orderType"];
    $extraData = $_POST["extraData"];
    $m2signature = $_POST["signature"]; //MoMo signature
    //Checksum
    $rawHash = "partnerCode=" . $partnerCode . "&accessKey=" . $accessKey . "&requestId=" . $requestId . "&amount=" . $amount . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo .
        "&orderType=" . $orderType . "&transId=" . $transId . "&message=" . $message . "&localMessage=" . $localMessage . "&responseTime=" . $responseTime . "&errorCode=" . $errorCode .
        "&payType=" . $payType . "&extraData=" . $extraData;
    $requestId_info = explode('_',  $requestId);
    $partnerSignature = hash_hmac("sha256", $rawHash, $serectkey);
    if ($m2signature == $partnerSignature) {
        if ($errorCode == '0') {
          //userid_lmsid_type 
          DB::beginTransaction();
          try {
              $payment = new \App\PaymentMethod();
              $payment->user_id = $requestId_info[0];
              $payment->item_id = $requestId_info[1];
              $payment->item_name = $orderInfo;
              $payment->amount = $amount;
              $payment->requestId = $requestId;
              $payment->orderId = $orderId;
              $payment->orderInfo = $orderInfo;
              $payment->transId      = $transId;
              $payment->orderType = $orderType;
              $payment->payType = $payType;
              $payment->extraData    = $extraData;
              $payment->responseTime = $responseTime; 
              $payment->status = 1; //Update Giao dich thanh công 0=>1
              $payment->save();

              $lmsseries_combo = DB::table('lmsseries_combo')->where('id',$requestId_info[1])->first();

              for ($i=1; $i <=5 ; $i++) {
                $n = 'n' . $i; 
                if ($lmsseries_combo->$n > 0) {
                  DB::table('payments')->insert([
                      'user_id' => $requestId_info[0],
                      'item_id' => $lmsseries_combo->$n,
                      'time' => $lmsseries_combo->time,
                      'payments_method_id' => $payment->id,
                  ]);
                }
              }

              // Commit Transaction
              DB::commit();
          } catch (\Exception $e) {
              // Rollback Transaction
              DB::rollback();
          }
          return 1;
        } 
    } 
  }
});
