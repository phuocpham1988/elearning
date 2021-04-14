<?php
namespace App\Http\Controllers;

use App\ClassesUser;
use App\EmailTemplate;
use App\ExamSeries;
use App\LmsSeries;
use App\LmsSeriesCombo;
use App\Payment;
use App\PaymentMethod;
// use App\Paypal;
use App\Quiz;
use App\User;
use Auth;
use Carbon;
use DB;
use Excel;
use Exception;
use Illuminate\Http\Request;
use Input;
use Razorpay\Api\Api;
use Softon\Indipay\Facades\Indipay;
use Yajra\Datatables\Datatables;
use \App;

class PaymentsController extends Controller
{
    public $payment_records = [];
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function lmsPayments(Request $request, $slug)
    {

        $record = LmsSeriesCombo::getRecordWithSlug($slug);
        if ($record != null && $record->cost == 0) {
            //kiểm tra đơn hàng đã tồn tại
            $lmsseries_combo_check = DB::table('payment_method')
                ->where('item_id', $record->id)
                ->where('user_id', Auth::user()->id)
                ->first();
            if ($lmsseries_combo_check != null) {
                flash('Thông báo', 'Đơn hàng đã được tạo trước đó', 'success');
                if ($record->type == 1) {
                    return redirect('/lms/exam-categories/study');
                }
                return redirect('/lms/exam-categories/list');
            }

            // đặt hàng 0đ
            $orderInfo = $record->title;
            $orderId   = 'HIK' . time() . "";
            $requestId = Auth::user()->id . '_' . $record->id . '_' . $record->type;
            //$requestId_info = explode('_', $requestId);
            DB::beginTransaction();
            try {
                $payment               = new PaymentMethod();
                $payment->user_id      = Auth::user()->id;
                $payment->item_id      = $record->id;
                $payment->item_name    = $orderInfo;
                $payment->amount       = 0;
                $payment->requestId    = $requestId;
                $payment->orderId      = $orderId;
                $payment->orderInfo    = $orderInfo;
                $payment->transId      = mt_srand(10);
                $payment->orderType    = 'Free';
                $payment->payType      = 'Free';
                $payment->extraData    = "merchantName=Hikari Academy";
                $payment->responseTime = date("Y-m-d H:i:s");
                $payment->status       = 1; //Update Giao dich thanh công 0=>1
                $payment->save();

                for ($i = 1; $i <= 5; $i++) {
                    $n = 'n' . $i;
                    if ($record->$n > 0) {
                        DB::table('payments')->insert([
                            'user_id'            => Auth::user()->id,
                            'item_id'            => $record->$n,
                            'time'               => $record->time,
                            'payments_method_id' => $payment->id,
                        ]);
                    }
                }
               
                DB::commit();
                $message_success = "Bạn đã mua khóa học {$orderInfo} thành công";
                flash('Thông báo', $message_success, 'success');
                if ($record->type == 1) {
                    return redirect('/lms/exam-categories/study');
                }
                return redirect('/lms/exam-categories/list');

            } catch (Exception $e) {
                
                $message = "Tạo đơn hàng thất bại";
                flash('Thông báo', $message, 'error');
                if ($record->type == 1) {
                    return redirect('/lms/exam-categories/study');
                }
                return redirect('/lms/exam-categories/list');
            }

        }

        $data['payments_history'] = DB::table('payments')->where([
                                        ['payments.user_id', Auth::id()],
                                        ['payments.status', 1],
                                        ])
                                        ->orderBy('id', 'desc')
                                        ->get();
        $data['active_class'] = 'buypoint';
        $data['title']        = 'Phương thức thanh toán';
        $data['layout']       = getLayout();
        $data['lmsseries']    = $record;
        $view_name            = getTheme() . '::student.payments.lmspayments';
        return view($view_name, $data);
    }

    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }
    public function getMomoQr(Request $request, $slug)
    {

        $endpoint    = env('MOMO_ENDPOINT');
        $partnerCode = env('MOMO_PARTNERCODE');
        $accessKey   = env('MOMO_ACCESSKEY');
        $serectkey   = env('MOMO_SECRET');
        if (env('DEMO_MODE')) {
            $endpoint    = env('MOMO_ENDPOINT_TEST');
            $partnerCode = env('MOMO_PARTNERCODE_TEST');
            $accessKey   = env('MOMO_ACCESSKEY_TEST');
            $serectkey   = env('MOMO_SECRET_TEST');
        }
        $record         = LmsSeriesCombo::getRecordWithSlug($slug);
        $amount         = $record->cost;
        $orderInfo      = $record->title;
        $returnUrl      = SITE_URL . "/payments/momoreturn";
        $notifyurl      = SITE_URL . "/api/payments/momoipn";
        $orderId        = 'HIK' . time() . "";
        $requestId      = Auth::user()->id . '_' . $record->id . '_' . $record->type;
        $requestId_info = explode('_', $requestId);
        $requestType    = "captureMoMoWallet";
        $extraData      = "merchantName=Hikari Academy";
        //before sign HMAC SHA256 signature
        $rawHash   = "partnerCode=" . $partnerCode . "&accessKey=" . $accessKey . "&requestId=" . $requestId . "&amount=" . $amount . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&returnUrl=" . $returnUrl . "&notifyUrl=" . $notifyurl . "&extraData=" . $extraData;
        $signature = hash_hmac("sha256", $rawHash, $serectkey);
        $data      = array(
            'partnerCode' => $partnerCode,
            'accessKey'   => $accessKey,
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'returnUrl'   => $returnUrl,
            'notifyUrl'   => $notifyurl,
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
        );
        $result     = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);
        if ($jsonResult['errorCode'] == 0) {
            $payUrl = $jsonResult['payUrl'];
            return redirect()->away($payUrl);
        } else {
            flash('error', $jsonResult['localMessage'], 'error');
            return redirect()->back();
        }
    }
    public function momoReturn(Request $request)
    {
        if (!empty($_GET)) {
            $secretKey = env('MOMO_SECRET');
            if (env('DEMO_MODE')) {
                $secretKey = 'MLqxUsNWxMuypOnUeV84CvKRh5dp2zR7';
            }
            $partnerCode  = $_GET["partnerCode"];
            $accessKey    = $_GET["accessKey"];
            $orderId      = $_GET["orderId"];
            $localMessage = $_GET["localMessage"];
            $message      = $_GET["message"];
            $transId      = $_GET["transId"];
            $orderInfo    = $_GET["orderInfo"];
            $amount       = $_GET["amount"];
            $errorCode    = $_GET["errorCode"];
            $responseTime = $_GET["responseTime"];
            $requestId    = $_GET["requestId"];
            $extraData    = $_GET["extraData"];
            $payType      = $_GET["payType"];
            $orderType    = $_GET["orderType"];
            $extraData    = $_GET["extraData"];
            $m2signature  = $_GET["signature"];
            //Checksum
            $rawHash = "partnerCode=" . $partnerCode . "&accessKey=" . $accessKey . "&requestId=" . $requestId . "&amount=" . $amount . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo .
                "&orderType=" . $orderType . "&transId=" . $transId . "&message=" . $message . "&localMessage=" . $localMessage . "&responseTime=" . $responseTime . "&errorCode=" . $errorCode .
                "&payType=" . $payType . "&extraData=" . $extraData;
            $partnerSignature = hash_hmac("sha256", $rawHash, $secretKey);
            $requestId_info   = explode('_', $requestId);
            if ($m2signature == $partnerSignature) {
                if ($errorCode == '0') {
                    $message_success = "Bạn đã mua {$orderInfo} thành công";
                    flash($message, $message_success, 'success');
                    if ($requestId_info[2] == 1) {
                        return redirect('/lms/exam-categories/study');
                    }
                    return redirect('/lms/exam-categories/list');
                } else {
                    flash('error', $localMessage, 'error');
                    $lmsseries_combo = DB::table('lmsseries_combo')->where('id', $requestId_info[1])->first();
                    return redirect('/payments/lms/' . $lmsseries_combo->slug);
                }
            } else {
                flash('Thanh toán không thành công', $localMessage, 'error');
                // flash('error', 'Giao dịch không thành công, chữ ký không đúng.', 'error');
                return redirect('/site/courses');
            }
        }
    }
    public function getAtm(Request $request, $bankCode, $slug)
    {

        $record      = LmsSeriesCombo::getRecordWithSlug($slug);
        $amount      = $record->cost;
        $orderInfo   = $record->title;
        $orderId     = 'HIK' . time() . "";
        $requestId   = (string) Auth::user()->id . '_' . $record->id . '_' . $record->type;
        $extraData   = "merchantName=Hikari Academy";
        $requestType = "payWithMoMoATM";
        $returnUrl   = SITE_URL . "/payments/atmreturn";
        $notifyurl   = SITE_URL . "/api/payments/atmipn";
        $endpoint    = env('MOMO_ENDPOINT');
        $partnerCode = env('MOMO_PARTNERCODE');
        $accessKey   = env('MOMO_ACCESSKEY');
        $serectkey   = env('MOMO_SECRET');
        if (env('DEMO_MODE')) {
            $endpoint    = env('MOMO_ENDPOINT_ATM_TEST');
            $partnerCode = env('MOMO_PARTNERCODE_ATM_TEST');
            $accessKey   = env('MOMO_ACCESSKEY_ATM_TEST');
            $serectkey   = env('MOMO_SECRET_ATM_TEST');
            $bankCode    = env('MOMO_BANKCODE_ATM_TEST');
        }
        $rawHash = "partnerCode=" . $partnerCode . "&accessKey=" . $accessKey . "&requestId=" . $requestId . "&bankCode=" . $bankCode . "&amount=" . $amount . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&returnUrl=" . $returnUrl . "&notifyUrl=" . $notifyurl . "&extraData=" . $extraData . "&requestType=" . $requestType;
        //echo $rawHash;
        $signature = hash_hmac("sha256", $rawHash, $serectkey);
        $data      = array('partnerCode' => $partnerCode,
            'accessKey'                      => $accessKey,
            'requestId'                      => $requestId,
            'amount'                         => $amount,
            'orderId'                        => $orderId,
            'orderInfo'                      => $orderInfo,
            'returnUrl'                      => $returnUrl,
            'bankCode'                       => $bankCode,
            'notifyUrl'                      => $notifyurl,
            'extraData'                      => $extraData,
            'requestType'                    => $requestType,
            'signature'                      => $signature);
        $result     = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true); // decode json
        if ($jsonResult['errorCode'] == 0) {
            $payUrl = $jsonResult['payUrl'];
            return redirect()->away($payUrl);
        } else {
            flash('error', $jsonResult['localMessage'], 'error');
            return redirect('/site/courses');
        }
    }
    public function atmReturn(Request $request)
    {
        if (!empty($_GET)) {
            $secretKey = env('MOMO_SECRET');
            if (env('DEMO_MODE')) {
                $secretKey = env('MOMO_SECRET_ATM_TEST');
            }
            $partnerCode  = $_GET["partnerCode"];
            $accessKey    = $_GET["accessKey"];
            $orderId      = $_GET["orderId"];
            $localMessage = $_GET["localMessage"];
            $message      = $_GET["message"];
            $transId      = $_GET["transId"];
            $orderInfo    = $_GET["orderInfo"];
            $amount       = $_GET["amount"];
            $errorCode    = $_GET["errorCode"];
            $responseTime = $_GET["responseTime"];
            $requestId    = $_GET["requestId"];
            $extraData    = $_GET["extraData"];
            $payType      = $_GET["payType"];
            $orderType    = $_GET["orderType"];
            $extraData    = $_GET["extraData"];
            $m2signature  = $_GET["signature"]; //MoMo signature
            //Checksum
            $rawHash = "partnerCode=" . $partnerCode . "&accessKey=" . $accessKey . "&requestId=" . $requestId . "&amount=" . $amount . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo .
                "&orderType=" . $orderType . "&transId=" . $transId . "&message=" . $message . "&localMessage=" . $localMessage . "&responseTime=" . $responseTime . "&errorCode=" . $errorCode .
                "&payType=" . $payType . "&extraData=" . $extraData;
            $partnerSignature = hash_hmac("sha256", $rawHash, $secretKey);
            $requestId_info   = explode('_', $requestId);
            if ($m2signature == $partnerSignature) {
                if ($errorCode == '0') {
                    $message_success = "Bạn đã mua {$orderInfo} thành công";
                    flash($message, $message_success, 'success');
                    if ($requestId_info[2] == 1) {
                        return redirect('/lms/exam-categories/study');
                    }
                    return redirect('/lms/exam-categories/list');
                } else {
                    flash('error', $localMessage, 'error');
                    //return redirect('/site/courses');
                    $lmsseries_combo = DB::table('lmsseries_combo')->where('id', $requestId_info[1])->first();
                    return redirect('/payments/lms/' . $lmsseries_combo->slug);
                }
            } else {
                flash('error', $localMessage, 'error');
                $lmsseries_combo = DB::table('lmsseries_combo')->where('id', $requestId_info[1])->first();
                return redirect('/payments/lms/' . $lmsseries_combo->slug);
            }
        }
    }

    public function index($slug)
    {
        if (!isEligible($slug)) {
            return back();
        }
        $user              = getUserWithSlug($slug);
        $data['is_parent'] = 0;
        $user              = getUserWithSlug($slug);
        if (getRoleData($user->role_id) == 'account') {
            $data['is_parent'] = 1;
        }
        $data['user']         = $user;
        $data['active_class'] = 'subscriptions';
        $data['title']        = getPhrase('subscriptions_list');
        $data['layout']       = getLayout();
        $payment              = new Payment();
        $records              = $payment->updateTransactionRecords($user->id);
        foreach ($records as $record) {
            $rec = Payment::where('id', $record->id)->first();
            $this->isExpired($rec);
        }
        // return view('student.payments.list', $data);
        $view_name = getTheme() . '::student.payments.list';
        return view($view_name, $data);
    }
    public function getDatatable($slug)
    {
        $is_parent = 0;
        $user      = getUserWithSlug($slug);
        if (getRoleData($user->role_id) == 'account') {
            $is_parent   = 1;
            $childs_list = App\User::where('parent_id', '=', $user->id)->get();
            $records     = Payment::join('users', 'users.id', '=', 'payments.user_id')
                ->where('users.parent_id', '=', $user->id)
                ->select(['users.image', 'users.name', 'item_name', 'plan_type', 'start_date', 'end_date', 'payment_gateway', 'payments.updated_at', 'payment_status', 'payments.cost', 'payments.after_discount', 'payments.paid_amount', 'payments.id']);
            $ind = 0;
            foreach ($childs_list as $child) {
                if ($ind++ == 0) {
                    $records->where('user_id', '=', $child->id);
                    continue;
                }
                $records->orWhere('user_id', '=', $child->id);
            }
            $records->orderBy('updated_at', 'desc');
        } else {
            $records = Payment::select(['item_name', 'plan_type', 'start_date', 'end_date', 'payment_gateway', 'updated_at', 'payment_status', 'id', 'cost', 'after_discount', 'paid_amount'])
                ->where('user_id', '=', $user->id)
                ->orderBy('updated_at', 'desc');
        }
        $dta = Datatables::of($records)
            ->addColumn('action', function ($records) {
                if (!($records->payment_status == PAYMENT_STATUS_CANCELLED || $records->payment_status == PAYMENT_STATUS_PENDING)) {
                    $link_data = ' <a >View More</a>';
                    return $link_data;
                }
                return;
            })
            ->editColumn('payment_status', function ($records) {
                $rec = '';
                if ($records->payment_status == PAYMENT_STATUS_CANCELLED) {
                    $rec = '<span class="label label-danger">' . ucfirst($records->payment_status) . '</span>';
                } elseif ($records->payment_status == PAYMENT_STATUS_PENDING) {
                    $rec = '<span class="label label-info">' . ucfirst($records->payment_status) . '</span>';
                } elseif ($records->payment_status == PAYMENT_STATUS_SUCCESS) {
                    $rec = '<span class="label label-success">' . ucfirst($records->payment_status) . '</span>';
                }
                return $rec;
            })
            ->editColumn('plan_type', function ($records) {
                return ucfirst($records->plan_type);
            })
            ->editColumn('start_date', function ($records) {
                if ($records->payment_status == PAYMENT_STATUS_CANCELLED || $records->payment_status == PAYMENT_STATUS_PENDING) {
                    return '-';
                }
                return $records->start_date;
            })
            ->editColumn('end_date', function ($records) {
                if ($records->payment_status == PAYMENT_STATUS_CANCELLED || $records->payment_status == PAYMENT_STATUS_PENDING) {
                    return '-';
                }
                return $records->end_date;
            })
            ->editColumn('payment_gateway', function ($records) {
                $text = ucfirst($records->payment_gateway);
                if ($records->payment_status == PAYMENT_STATUS_SUCCESS) {
                    $extra = '<ul class="list-unstyled payment-col clearfix"><li>' . $text . '</li>';
                    $extra .= '<li><p>Cost:' . $records->cost . '</p><p>Aftr Dis.:' . $records->after_discount . '</p><p>Paid:' . $records->paid_amount . '</p></li></ul>';
                    return $extra;
                }
                return $text;
            })
            ->removeColumn('id')
            ->removeColumn('action');
        if ($is_parent) {
            $dta = $dta->editColumn('image', function ($records) {
                return '<img src="' . getProfilePath($records->image) . '"  /> ';
            })
                ->editColumn('name', function ($records) {
                    return ucfirst($records->name);
                });
        }
        return $dta->make();
    }
    /**
     * This method identifies the type of package user is requesting and redirects to the payment gateway
     * The payments are categorized to 3 modules
     * 1) Combo -- Contains the items related to test series [it may have exams or study materials]
     * 2) LMS  -- It only contains Study materials
     * 3) EXAM -- It only contains single exams package
     * @param  [type] $type ['combo', 'lms', 'exam']
     * @param  [type] $slug [description]
     * @return [type]       [description]
     */
    public function paynow(Request $request, $slug)
    {
        // dd($request);
        if ($request->gateway == 'razorpay') {
            $type = $request->type;
            if ($request->after_discount == 0) {
                $item                               = $this->getPackageDetails($type, $slug);
                $other_details                      = array();
                $other_details['is_coupon_applied'] = $request->is_coupon_applied;
                $other_details['actual_cost']       = $request->actual_cost;
                $other_details['discount_availed']  = $request->discount_availed;
                $other_details['after_discount']    = $request->after_discount;
                $other_details['coupon_id']         = $request->coupon_id;
                $other_details['paid_by_parent']    = $request->parent_user;
                $other_details['child_id']          = $request->selected_child_id;
                $token                              = $this->preserveBeforeSave($item, $type, $request->gateway, $other_details, 1);
                if ($this->validateAndApproveZeroDiscount($token, $request)) {
                    //Valid
                    flash('success', 'your_subscription_was_successfull', 'overlay');
                    $user = Auth::user();
                    return redirect(URL_PAYMENTS_LIST . $user->slug);
                } else {
                    //Cheat
                    flash('Ooops...!', 'invalid_payment_or_coupon_code', 'overlay');
                    $user = Auth::user();
                    return redirect(URL_PAYMENTS_LIST . $user->slug);
                }
                return back();
            }
            $data['layout'] = getLayout();
            $active_class   = 'lms';
            if ($type == 'combo' || $type == 'exams' || $type == 'exam') {
                $active_class = 'exams';
            }
            $record = $this->getModelName($type, $slug);
            // dd($record);
            $data['active_class'] = $active_class;
            $data['type']         = $type;
            $data['slug']         = $slug;
            $data['request']      = $request;
            $data['title']        = $record->title;
            $data['user']         = Auth::user();
            // return view('payments.razorpayform',$data);
            $view_name = getTheme() . '::payments.razorpayform';
            return view($view_name, $data);
        }
        $type = $request->type;
        /**
         * Get the Item Details based on Type supplied type
         * If item is valid, prepare the data to save after successfull payment
         * Preserve the data
         * Redirect to payment
         * @var [type]
         */
        $item = $this->getPackageDetails($type, $slug);
        if (!$item) {
            dd('failed');
        }
        $other_details                      = array();
        $other_details['is_coupon_applied'] = $request->is_coupon_applied;
        $other_details['actual_cost']       = $request->actual_cost;
        $other_details['discount_availed']  = $request->discount_availed;
        $other_details['after_discount']    = $request->after_discount;
        $other_details['coupon_id']         = $request->coupon_id;
        $other_details['paid_by_parent']    = $request->parent_user;
        $other_details['child_id']          = $request->selected_child_id;
        /**
         * If the total amount is 0 after coupon code is applied,
         * once validate is user is really getting the discount after the coupon is applied
         * then give subscription for the package
         * @var [type]
         */
        if ($request->after_discount == 0) {
            $token = $this->preserveBeforeSave($item, $type, $request->gateway, $other_details, 1);
            if ($this->validateAndApproveZeroDiscount($token, $request)) {
                //Valid
                flash('success', 'your_subscription_was_successfull', 'overlay');
                $user = Auth::user();
                return redirect(URL_PAYMENTS_LIST . $user->slug);
            } else {
                //Cheat
                flash('Ooops...!', 'invalid_payment_or_coupon_code', 'overlay');
                $user = Auth::user();
                return redirect(URL_PAYMENTS_LIST . $user->slug);
            }
            return back();
        }
        $payment_gateway = $request->gateway;
        if ($payment_gateway == 'payu') {
            if (!getSetting('payu', 'module')) {
                flash('Ooops...!', 'this_payment_gateway_is_not_available', 'error');
                return back();
            }
            $token                   = $this->preserveBeforeSave($item, $type, $payment_gateway, $other_details);
            $config                  = config();
            $payumoney               = $config['indipay']['payumoney'];
            $payumoney['successUrl'] = URL_PAYU_PAYMENT_SUCCESS . '?token=' . $token;
            $payumoney['failureUrl'] = URL_PAYU_PAYMENT_CANCEL . '?token=' . $token;
            $user                    = Auth::user();
            $parameters              = [
                'tid'         => $token,
                'order_id'    => '',
                'firstname'   => $user->name,
                'email'       => $user->email,
                'phone'       => ($user->phone) ? $user->phone : '45612345678',
                'productinfo' => $request->item_name,
                'amount'      => $request->after_discount,
                'surl'        => URL_PAYU_PAYMENT_SUCCESS . '?token=' . $token,
                'furl'        => URL_PAYU_PAYMENT_CANCEL . '?token=' . $token,
            ];
            return Indipay::purchase($parameters);
            // URL_PAYU_PAYMENT_SUCCESS
            // URL_PAYU_PAYMENT_CANCEL
        } else if ($payment_gateway == 'paypal') {
            if (!getSetting('paypal', 'module')) {
                flash('Ooops...!', 'this_payment_gateway_is_not_available', 'error');
                return back();
            }
            $token                           = $this->preserveBeforeSave($item, $type, $payment_gateway, $other_details);
            $paypal                          = new Paypal();
            $paypal->config['return']        = URL_PAYPAL_PAYMENT_SUCCESS . '?token=' . $token;
            $paypal->config['cancel_return'] = URL_PAYPAL_PAYMENT_CANCEL . '?token=' . $token;
            $paypal->invoice                 = $token;
            $paypal->add($item->title, $request->after_discount); //ADD  item
            $paypal->pay(); //Proccess the payment
        } else if ($payment_gateway == 'offline') {
            if (!getSetting('offline_payment', 'module')) {
                flash('Ooops...!', 'this_payment_gateway_is_not_available', 'error');
                return back();
            }
            $payment_data = [];
            foreach (Input::all() as $key => $value) {
                if ($key == '_token') {
                    continue;
                }
                $payment_data[$key] = $value;
            }
            $data['active_class'] = 'feedback';
            $data['payment_data'] = json_encode($payment_data);
            $data['layout']       = getLayout();
            $data['title']        = getPhrase('offline_payment');
            // return view('payments.offline-payment', $data);
            $view_name = getTheme() . '::payments.offline-payment';
            return view($view_name, $data);
        }
        dd('please wait...');
    }
    /**
     * This method returns the object details
     * @param  [type] $type [description]
     * @param  [type] $slug [description]
     * @return [type]       [description]
     */
    public function getPackageDetails($type, $slug)
    {
        return $this->getmodelName($type, $slug);
    }
    /**
     * This method returns the Class based on the type of request
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public function getModelName($type, $slug)
    {
        switch ($type) {
            case 'combo':
                return ExamSeries::where('slug', '=', $slug)->first();
                break;
            case 'lms':
                return LmsSeries::where('slug', '=', $slug)->first();
                break;
            case 'exam':
                return Quiz::where('slug', '=', $slug)->first();
                break;
        }
        return null;
    }
    public function paypal_cancel()
    {
        if ($this->paymentFailed()) {
            //FAILED PAYMENT RECORD UPDATED SUCCESSFULLY
            //PREPARE SUCCESS MESSAGE
            flash('Ooops...!', 'your_payment_was cancelled', 'overlay');
        } else {
            //PAYMENT RECORD IS NOT VALID
            //PREPARE METHOD FOR FAILED CASE
            pageNotFound();
        }
        //REDIRECT THE USER BY LOADING A VIEW
        $user = Auth::user();
        return redirect(URL_PAYMENTS_LIST . $user->slug);
    }
    public function paypal_success(Request $request)
    {
        $user         = Auth::user();
        $response     = $request->all();
        $package_name = ucwords($response['item_name1']);
        if ($this->paymentSuccess($request)) {
            //PAYMENT RECORD UPDATED SUCCESSFULLY
            //PREPARE SUCCESS MESSAGE
            //   $email_template = 'subscription_success';
            //   $template    = new EmailTemplate();
            //  $content_data =  $template->sendEmailNotification($email_template,
            // array('username' =>$user->name,
            //          'plan'     => $payment_record->plan_type,
            //          'to_email' => $user->email));
            try {
                $user->notify(new \App\Notifications\UsersNotifcations($user, $content_data));
            } catch (Exception $e) {
            }
            flash('success', 'your_subscription_was_successfull', 'success');
            // sendEmail($email_template, array('username'=>$user->name,
            // 'plan' => $package_name,
            // 'to_email' => $user->email));
        } else {
            //PAYMENT RECORD IS NOT VALID
            //PREPARE METHOD FOR FAILED CASE
            pageNotFound();
        }
        //REDIRECT THE USER BY LOADING A VIEW
        return redirect(URL_PAYMENTS_LIST . $user->slug);
    }
    public function payu_success(Request $request)
    {
        $response     = $request->all();
        $package_name = ucwords($response['productinfo']);
        $user         = Auth::user();
        if ($this->paymentSuccess($request)) {
            //PAYMENT RECORD UPDATED SUCCESSFULLY
            //PREPARE SUCCESS MESSAGE
            // $email_template = 'subscription_success';
            //    $template    = new EmailTemplate();
            //   $content_data =  $template->sendEmailNotification($email_template,
            //  array('username' =>$user->name,
            //           'plan'     => $payment_record->plan_type,
            //           'to_email' => $user->email));
            try {
                $user->notify(new \App\Notifications\UsersNotifcations($user, $content_data));
            } catch (Exception $e) {
            }
            // sendEmail($email_template, array('username'=>$user->name,
            // 'plan' => $package_name,
            // 'to_email' => $user->email));
            // flash('success', 'your_subscription_was_successfull', 'success');
        } else {
            //PAYMENT RECORD IS NOT VALID
            //PREPARE METHOD FOR FAILED CASE
            pageNotFound();
        }
        //REDIRECT THE USER BY LOADING A VIEW
        return redirect(URL_PAYMENTS_LIST . $user->slug);
    }
    public function payu_cancel(Request $request)
    {
        if ($this->paymentFailed()) {
            //FAILED PAYMENT RECORD UPDATED SUCCESSFULLY
            //PREPARE SUCCESS MESSAGE
            flash('Ooops...!', 'your_payment_was cancelled', 'overlay');
        } else {
            //PAYMENT RECORD IS NOT VALID
            //PREPARE METHOD FOR FAILED CASE
            pageNotFound();
        }
        //REDIRECT THE USER BY LOADING A VIEW
        $user = Auth::user();
        return redirect(URL_PAYMENTS_LIST . $user->slug);
    }
    /**
     * This method saves the record before going to payment method
     * The exact record can be identified by using the slug
     * By using slug we will fetch the record and update the payment status to completed
     * @param  [type] $item           [description]
     * @param  [type] $payment_method [description]
     * @return [type]                 [description]
     */
    public function preserveBeforeSave($item, $package_type, $payment_method, $other_details, $coupon_zero = 0)
    {
        // dd($item);
        $user = getUserRecord();
        if ($other_details['paid_by_parent']) {
            $user = getUserRecord($other_details['child_id']);
        }
        $payment                  = new Payment();
        $payment->item_id         = $item->id;
        $payment->item_name       = $item->title;
        $payment->plan_type       = $package_type;
        $payment->payment_gateway = $payment_method;
        $payment->slug            = $payment::makeSlug(getHashCode());
        $payment->cost            = $item->cost;
        $payment->user_id         = $user->id;
        $payment->paid_by_parent  = $other_details['paid_by_parent'];
        $payment->payment_status  = PAYMENT_STATUS_PENDING;
        $payment->other_details   = json_encode($other_details);
        if (!$coupon_zero) {
            if ($payment_method == 'offline') {
                $payment->notes = $other_details['payment_details'];
            }
        }
        $payment->save();
        return $payment->slug;
    }
    /**
     * Common method to handle payment failed records
     * @return [type] [description]
     */
    protected function paymentFailed()
    {
        if (env('DEMO_MODE')) {
            return true;
        }
        $params = explode('?token=', $_SERVER['REQUEST_URI']);
        if (!count($params)) {
            return false;
        }
        $slug           = $params[1];
        $payment_record = Payment::where('slug', '=', $slug)->first();
        if (!$this->processPaymentRecord($payment_record)) {
            return false;
        }
        $payment_record->payment_status = PAYMENT_STATUS_CANCELLED;
        $payment_record->save();
        return true;
    }
    /**
     * Common method to handle success payments
     * @return [type] [description]
     */
    protected function paymentSuccess(Request $request)
    {
        if (env('DEMO_MODE')) {
            return true;
        }
        $params = explode('?token=', $_SERVER['REQUEST_URI']);
        if (!count($params)) {
            return false;
        }
        $slug           = $params[1];
        $payment_record = Payment::where('slug', '=', $slug)->first();
        if ($this->processPaymentRecord($payment_record)) {
            $payment_record->payment_status = PAYMENT_STATUS_SUCCESS;
            $item_details                   = '';
            if ($payment_record->plan_type == 'combo') {
                $item_model = new ExamSeries();
            }
            if ($payment_record->plan_type == 'exam') {
                $item_model = new Quiz();
            }
            if ($payment_record->plan_type == 'lms') {
                $item_model = new LmsSeries();
            }
            $item_details                    = $item_model->where('id', '=', $payment_record->item_id)->first();
            $daysToAdd                       = '+' . $item_details->validity . 'days';
            $payment_record->start_date      = date('Y-m-d');
            $payment_record->end_date        = date('Y-m-d', strtotime($daysToAdd));
            $details_before_payment          = (object) json_decode($payment_record->other_details);
            $payment_record->coupon_applied  = $details_before_payment->is_coupon_applied;
            $payment_record->coupon_id       = $details_before_payment->coupon_id;
            $payment_record->actual_cost     = $details_before_payment->actual_cost;
            $payment_record->discount_amount = $details_before_payment->discount_availed;
            $payment_record->after_discount  = $details_before_payment->after_discount;
            if ($payment_record->payment_gateway == 'paypal') {
                $payment_record->paid_amount    = $request->mc_gross;
                $payment_record->transaction_id = $request->txn_id;
                $payment_record->paid_by        = $request->payer_email;
            }
            //Capcture all the response from the payment.
            //In case want to view total details, we can fetch this record
            $payment_record->transaction_record = json_encode($request->request->all());
            $payment_record->save();
            if ($payment_record->coupon_applied) {
                $this->couponcodes_usage($payment_record);
            }
            return true;
        }
        return false;
    }
    public function couponcodes_usage($payment_record)
    {
        $coupon_usage['user_id']              = $payment_record->user_id;
        $coupon_usage['item_id']              = $payment_record->item_id;
        $coupon_usage['item_type']            = $payment_record->plan_type;
        $coupon_usage['item_cost']            = $payment_record->actual_cost;
        $coupon_usage['total_invoice_amount'] = $payment_record->paid_amount;
        $coupon_usage['discount_amount']      = $payment_record->discount_amount;
        $coupon_usage['coupon_id']            = $payment_record->coupon_id;
        $coupon_usage['updated_at']           = new \DateTime();
        DB::table('couponcodes_usage')->insert($coupon_usage);
        return true;
    }
    /**
     * This method validates the payment record before update the payment status
     * @param  [type]  $payment_record [description]
     * @return boolean                 [description]
     */
    protected function isValidPaymentRecord(Payment $payment_record)
    {
        $valid = false;
        if ($payment_record) {
            if ($payment_record->payment_status == PAYMENT_STATUS_PENDING || $payment_record->payment_gateway == 'offline') {
                $valid = true;
            }
        }
        return $valid;
    }
    /**
     * This method checks the age of the payment record
     * If the age is > than MAX TIME SPECIFIED (30 MINS), it will update the record to aborted state
     * @param  payment $payment_record [description]
     * @return boolean                 [description]
     */
    protected function isExpired(Payment $payment_record)
    {
        $is_expired      = false;
        $to_time         = strtotime(Carbon\Carbon::now());
        $from_time       = strtotime($payment_record->updated_at);
        $difference_time = round(abs($to_time - $from_time) / 60, 2);
        if ($difference_time > PAYMENT_RECORD_MAXTIME) {
            $payment_record->payment_status = PAYMENT_STATUS_CANCELLED;
            $payment_record->save();
            return $is_expired = true;
        }
        return $is_expired;
    }
    /**
     * This method Process the payment record by validating through
     * the payment status and the age of the record and returns boolen value
     * @param  Payment $payment_record [description]
     * @return [type]                  [description]
     */
    protected function processPaymentRecord(Payment $payment_record)
    {
        if (!$this->isValidPaymentRecord($payment_record)) {
            flash('Oops', 'invalid_record', 'error');
            return false;
        }
        if ($this->isExpired($payment_record)) {
            flash('Oops', 'time_out', 'error');
            return false;
        }
        return true;
    }
    /**
     * This method handles the request before payment page
     * It shows the checkout page and gives an option for coupon codes
     * @param  [type] $slug [description]
     * @return [type]       [description]
     */
    public function checkout($type, $slug)
    {
        $record = $this->getModelName($type, $slug);
        if ($isValid = $this->isValidRecord($record)) {
            return redirect($isValid);
        }
        $user = Auth::user();
        //Check if user is already paid to the same item and the item is in valid date
        // if($user->role_id == 6){
        //   $children_ids  = App\User::where('parent_id',$user->id)->pluck('id')->toArray();
        //   $is_paid  = [];
        //   foreach ($children_ids as $key => $value) {
        //      $is_paid[]  = Payment::isParentPurchased($record->id, $type, $value);
        //   }
        //   // dd($is_paid);
        //   $paid_staus  = in_array('notpurchased', $is_paid);
        //   if(!$paid_staus){
        //       flash($user->name, 'Bạn đã sẵn sàng mua khóa học này', 'overlay');
        //       return back();
        //   }
        // }
        if (Payment::isItemPurchased($record->id, $type, $user->id)) {
            //User already purchased this item and it is valid
            //Return the user to back by the message
            flash('Hey ' . $user->name, 'Bạn đã mua khóa học này', 'overlay');
            return back();
        }
        $active_class = 'lms';
        if ($type == 'combo' || $type == 'exams' || $type == 'exam') {
            $active_class = 'exams';
        }
        $data['active_class'] = $active_class;
        $data['pay_by']       = '';
        $data['title']        = $record->title;
        $data['item_type']    = $type;
        $data['item']         = $record;
        $current_theme        = getDefaultTheme();
        if ($current_theme == 'default') {
            $data['right_bar']       = false;
            $data['right_bar_class'] = 'order-user-details';
            $data['right_bar_path']  = 'student.payments.billing-address-right-bar';
            $data['right_bar_data']  = array(
                'item' => $record,
            );
        }
        $data['layout']      = getLayout();
        $data['parent_user'] = false;
        if (checkRole(getUserGrade(7))) {
            $data['parent_user'] = true;
            $data['children']    = App\User::where('parent_id', '=', $user->id)->get();
        }
        $data['use_razorpay'] = false;
        // return view('student.payments.checkout', $data);
        $view_name = getTheme() . '::student.payments.checkout';
        return view($view_name, $data);
    }
    public function isValidRecord($record)
    {
        if ($record === null) {
            flash('Ooops...!', getPhrase("page_not_found"), 'error');
            return $this->getRedirectUrl();
        }
        return false;
    }
    public function getReturnUrl()
    {
        return URL_EXAM_SERIES;
    }
    /**
     * This method saves the submitted data from user and waits for the admin approval
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function updateOfflinePayment(Request $request)
    {
        $payment_data = json_decode($request->payment_data);
        $item         = $this->getPackageDetails($payment_data->type, $payment_data->item_name);
        if (!$item) {
            dd('failed');
        }
        $other_details                      = array();
        $other_details['is_coupon_applied'] = $payment_data->is_coupon_applied;
        $other_details['actual_cost']       = $payment_data->actual_cost;
        $other_details['discount_availed']  = $payment_data->discount_availed;
        $other_details['after_discount']    = $payment_data->after_discount;
        $other_details['coupon_id']         = $payment_data->coupon_id;
        $other_details['paid_by_parent']    = $payment_data->parent_user;
        $other_details['child_id']          = $payment_data->selected_child_id;
        $other_details['payment_details']   = $request->payment_details;
        $payment_gateway                    = $payment_data->gateway;
        $token                              = $this->preserveBeforeSave($item, $payment_data->type, $payment_gateway, $other_details);
        try {
            $owner     = App\User::where('role_id', 1)->first();
            $paid_user = getUserRecord();
            $owner->notify(new \App\Notifications\UserOfflinePaymentSubmit($owner, $paid_user, $item));
            $paid_user->notify(new \App\Notifications\PaidUserOfflinePayment($paid_user, $item));
        } catch (Exception $e) {
            // dd($e->getMessage());
        }
        flash('success', 'your_request_was_submitted_to_admin', 'overlay');
        return redirect(URL_PAYMENTS_LIST . Auth::user()->slug);
    }
    public function approveOfflinePayment(Request $request)
    {
        // dd($request);
        $payment_record = Payment::where('id', '=', $request->record_id)->first();
        // dd($payment_record);
        try {
            if ($request->submit == 'approve') {
                $this->approvePayment($payment_record, $request);
            } else {
                $user = getUserRecord($payment_record->user_id);
                // sendEmail('offline_subscription_failed', array('username'=>$user->name,
                //   'plan' => $payment_record->plan_type,
                //   'to_email' => $user->email, 'admin_comment'=>$request->admin_comment));
                $payment_record->payment_status = PAYMENT_STATUS_CANCELLED;
                $payment_record->admin_comments = $request->admin_comment;
                $payment_record->save();
                $template     = new EmailTemplate();
                $subject      = getPhrase('offline_subscription_failed');
                $content_data = $template->sendEmailNotification('offline_subscription_failed',
                    array('username' => $user->name,
                        'plan'           => $payment_record->plan_type,
                        'to_email'       => $user->email,
                        'admin_comment'  => $request->admin_comment));
                // $user->notify(new \App\Notifications\UsersNotifcations($user,$content_data,$subject));
                try {
                    $user->notify(new \App\Notifications\AdminCancelledOfflinePayment($user, $payment_record->plan_type, $request->admin_comment));
                } catch (Exception $e) {
                }
            }
            flash('success', 'record_was_updated_successfully', 'success');
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            flash('Oops..!', $message, 'warning');
        }
        return redirect(URL_OFFLINE_PAYMENT_REPORTS);
    }
    public function overallPayments($slug)
    {
        $paymentObject        = new Payment();
        $payments             = Payment::where('payment_status', '=', 'success')->get();
        $payments             = Payment::all();
        $data['active_class'] = 'analysis';
        $data['title']        = getPhrase('quiz_attempts');
        $data['exam_record']  = $exam_record;
        $data['layout']       = getLayout();
        // return view('payments.reports.overall-analysis', $data);
        $view_name = getTheme() . '::payments.reports.overall-analysis';
        return view($view_name, $data);
    }
    /**
     * This method redirects the user to view the onlinepayments reports dashboard
     * It contains an optional slug, if slug is null it will redirect the user to dashboard
     * If the slug is success/failed/cancelled/all it will show the appropriate result based on slug status from payments table
     * @param  string $slug [description]
     * @return [type]       [description]
     */
    public function onlinePaymentsReport()
    {
        if (!checkRole(getUserGrade(11))) {
            prepareBlockUserMessage();
            return back();
        }
        $data['active_class']          = 'reports';
        $data['title']                 = getPhrase('online_payments');
        $data['payments']              = (object) $this->prepareSummaryRecord('online');
        $data['payments_chart_data']   = (object) $this->getPaymentStats($data['payments']);
        $data['payments_monthly_data'] = (object) $this->getPaymentMonthlyStats();
        $data['payment_mode']          = 'online';
        $data['layout']                = getLayout();
        $view_name                     = getTheme() . '::payments.reports.payments-report';
        return view($view_name, $data);
    }
    /**
     * This method list the details of the records
     * @param  [type] $slug [description]
     * @return [type]       [description]
     */
    public function listOnlinePaymentsReport($slug)
    {
        if (!checkRole(getUserGrade(11))) {
            prepareBlockUserMessage();
            return back();
        }
        if (!in_array($slug, ['all', 'success', 'cancelled'])) {
            pageNotFound();
            return back();
        }
        $payment = new Payment();
        $this->updatePaymentTransactionRecords($payment->updateTransactionRecords('online'));
        $data['active_class']  = 'reports';
        $data['payments_mode'] = 'Thanh toán Online';
        if ($slug == 'all') {
            $data['title'] = 'Tất cả Thanh toán';
        } elseif ($slug == 'success') {
            $data['title'] = 'Thành công';
        } elseif ($slug == 'pending') {
            $data['title'] = getPhrase('pending_list');
        } elseif ($slug = 'cancelled') {
            $data['title'] = 'Hủy';
        }
        $data['layout']       = getLayout();
        $data['ajax_url']     = URL_ONLINE_PAYMENT_REPORT_DETAILS_AJAX . $slug;
        $data['payment_mode'] = 'online';
        $view_name            = getTheme() . '::payments.reports.payments-report-list';
        return view($view_name, $data);
    }
    public function updatePaymentTransactionRecords($records)
    {
        foreach ($records as $record) {
            $rec = Payment::where('id', $record->id)->first();
            $this->isExpired($rec);
        }
    }
    public function getOnlinePaymentReportsDatatable($slug)
    {
        if (!checkRole(getUserGrade(11))) {
            prepareBlockUserMessage();
            return back();
        }
        $records = Payment::join('users', 'users.id', '=', 'payments.user_id')
            ->select(['users.name', 'item_name', 'plan_type', 'payment_gateway', 'payments.updated_at', 'payment_status', 'payments.cost', 'payments.after_discount', 'payments.paid_amount', 'payments.id'])
            ->where('payment_gateway', '!=', 'offline')
            ->orderBy('updated_at', 'desc');
        if ($slug != 'all') {
            $records->where('payment_status', '=', $slug);
        }
        return Datatables::of($records)
            ->editColumn('payment_status', function ($records) {
                $rec = '';
                if ($records->payment_status == PAYMENT_STATUS_CANCELLED) {
                    $rec = '<span class="label label-danger">' . ucfirst($records->payment_status) . '</span>';
                } elseif ($records->payment_status == PAYMENT_STATUS_PENDING) {
                    $rec = '<span class="label label-info">' . ucfirst($records->payment_status) . '</span>';
                } elseif ($records->payment_status == PAYMENT_STATUS_SUCCESS) {
                    $rec = '<span class="label label-success">' . ucfirst($records->payment_status) . '</span>';
                }
                return $rec;
            })
            ->editColumn('name', function ($records) {
                return ucfirst($records->name);
            })
            ->editColumn('item_name', function ($records) {
                return 'Momo';
            })
            ->editColumn('plan_type', function ($records) {
                return '200.000';
            })
            ->editColumn('payment_gateway', function ($records) {
                $text = ucfirst($records->payment_gateway);
                if ($records->payment_status == PAYMENT_STATUS_SUCCESS) {
                    $extra = '<ul class="list-unstyled payment-col clearfix"><li>' . $text . '</li>';
                    $extra .= '<li><p>Số tiền: 200000 </p><p>Thanh toán:' . 'Momo' . '</p><p>Nội dung:' . 'HIKARI XU' . '</p></li></ul>';
                    return $extra;
                }
                return $text;
            })
            ->removeColumn('id')
            ->removeColumn('action')
            ->make();
    }
    /**
     * This method redirects the user to view the onlinepayments reports dashboard
     * It contains an optional slug, if slug is null it will redirect the user to dashboard
     * If the slug is success/failed/cancelled/all it will show the appropriate result based on slug status from payments table
     * @param  string $slug [description]
     * @return [type]       [description]
     */
    public function offlinePaymentsReport()
    {
        if (!checkRole(getUserGrade(11))) {
            prepareBlockUserMessage();
            return back();
        }
        $data['active_class']          = 'reports';
        $data['title']                 = getPhrase('offline_payments');
        $data['payments']              = (object) $this->prepareSummaryRecord('offline');
        $data['payments_chart_data']   = (object) $this->getPaymentStats($data['payments']);
        $data['payments_monthly_data'] = (object) $this->getPaymentMonthlyStats('offline', '=');
        $data['payment_mode']          = 'offline';
        $data['layout']                = getLayout();
        // return view('payments.reports.payments-report', $data);
        $view_name = getTheme() . '::payments.reports.payments-report';
        return view($view_name, $data);
    }
    /**
     * This method list the details of the records
     * @param  [type] $slug [description]
     * @return [type]       [description]
     */
    public function listOfflinePaymentsReport($slug)
    {
        if (!in_array($slug, ['all', 'pending', 'success', 'cancelled'])) {
            pageNotFound();
            return back();
        }
        $data['active_class']  = 'reports';
        $data['payments_mode'] = getPhrase('offline_payments');
        if ($slug == 'all') {
            $data['title'] = getPhrase('all_payments');
        } elseif ($slug == 'success') {
            $data['title'] = getPhrase('success_list');
        } elseif ($slug == 'pending') {
            $data['title'] = getPhrase('pending_list');
        } elseif ($slug = 'cancelled') {
            $data['title'] = getPhrase('cancelled_list');
        }
        $data['layout']       = getLayout();
        $data['ajax_url']     = URL_OFFLINE_PAYMENT_REPORT_DETAILS_AJAX . $slug;
        $data['payment_mode'] = 'offline';
        // return view('payments.reports.payments-report-list', $data);
        $view_name = getTheme() . '::payments.reports.payments-report-list';
        return view($view_name, $data);
    }
    /**
     * This method gets the list of records
     * @param  [type] $slug [description]
     * @return [type]       [description]
     */
    public function getOfflinePaymentReportsDatatable($slug)
    {
        if (!checkRole(getUserGrade(11))) {
            prepareBlockUserMessage();
            return back();
        }
        $records = Payment::join('users', 'users.id', '=', 'payments.user_id')
            ->select(['users.image', 'users.name', 'item_name', 'plan_type', 'start_date', 'end_date', 'payment_gateway', 'payments.updated_at', 'payment_status', 'payments.id', 'payments.cost', 'payments.after_discount', 'payments.paid_amount'])
            ->where('payment_gateway', '=', 'offline')
            ->orderBy('updated_at', 'desc');
        if ($slug != 'all') {
            $records->where('payment_status', '=', $slug);
        }
        return Datatables::of($records)
            ->editColumn('payment_status', function ($records) {
                $rec = '';
                if ($records->payment_status == PAYMENT_STATUS_CANCELLED) {
                    $rec = '<span class="label label-danger">' . ucfirst($records->payment_status) . '</span>';
                } elseif ($records->payment_status == PAYMENT_STATUS_PENDING) {
                    $rec = '<span class="label label-info">' . ucfirst($records->payment_status) . '</span>&nbsp;<button class="btn btn-primary btn-sm" onclick="viewDetails(' . $records->id . ');">' . getPhrase('view_details') . '</button>';
                } elseif ($records->payment_status == PAYMENT_STATUS_SUCCESS) {
                    $rec = '<span class="label label-success">' . ucfirst($records->payment_status) . '</span>';
                }
                return $rec;
            })
            ->editColumn('image', function ($records) {
                return '<img src="' . getProfilePath($records->image) . '"  /> ';
            })
            ->editColumn('name', function ($records) {
                return ucfirst($records->name);
            })
            ->editColumn('payment_gateway', function ($records) {
                $text = ucfirst($records->payment_gateway);
                if ($records->payment_status == PAYMENT_STATUS_SUCCESS) {
                    $extra = '<ul class="list-unstyled payment-col clearfix"><li>' . $text . '</li>';
                    $extra .= '<li><p>Cost:' . $records->cost . '</p><p>Aftr Dis.:' . $records->after_discount . '</p><p>Paid:' . $records->paid_amount . '</p></li></ul>';
                    return $extra;
                }
                return $text;
            })
            ->editColumn('plan_type', function ($records) {
                return ucfirst($records->plan_type);
            })
            ->editColumn('start_date', function ($records) {
                if ($records->payment_status == PAYMENT_STATUS_CANCELLED || $records->payment_status == PAYMENT_STATUS_PENDING) {
                    return '-';
                }
                return $records->start_date;
            })
            ->editColumn('end_date', function ($records) {
                if ($records->payment_status == PAYMENT_STATUS_CANCELLED || $records->payment_status == PAYMENT_STATUS_PENDING) {
                    return '-';
                }
                return $records->end_date;
            })
            ->removeColumn('id')
            ->removeColumn('users.image')
            ->removeColumn('action')
            ->make();
    }
    /**
     * This method prepares different variations of reports based on the type
     * This is a common method to prepare online, offline and overall reports
     * @param  string $type [description]
     * @return [type]       [description]
     */
    public function prepareSummaryRecord($type = 'overall')
    {
        $payments = [];
        if ($type == 'online') {
            $payments['all']       = $this->getRecordsCount('online');
            $payments['success']   = $this->getRecordsCount('online', 'success');
            $payments['cancelled'] = $this->getRecordsCount('online', 'cancelled');
            $payments['pending']   = $this->getRecordsCount('online', 'pending');
        } else if ($type == 'offline') {
            $payments['all']       = $this->getRecordsCount('offline');
            $payments['success']   = $this->getRecordsCount('offline', 'success');
            $payments['cancelled'] = $this->getRecordsCount('offline', 'cancelled');
            $payments['pending']   = $this->getRecordsCount('offline', 'pending');
        }
        return $payments;
    }
    /**
     * This is a helper method for fetching the data and preparing payment records count
     * @param  [type] $type   [description]
     * @param  string $status [description]
     * @return [type]         [description]
     */
    public function getRecordsCount($type, $status = '')
    {
        $count = 0;
        if ($type == 'online') {
            $count = PaymentMethod::where('is_online', '=', 1)->count();
            // if($status=='')
            //   $count = PaymentMethod::where('is_online', '=', 1)->count();
            // else
            // {
            //   $count = Payment::where('payment_gateway', '!=', 'offline')
            //                     ->where('payment_status', '=', $status)
            //                     ->count();
            // }
        } else if ($type == 'offline') {
            $count = PaymentMethod::where('is_online', '=', '0')->count();
            /* if($status=='')
        $count = PaymentMethod::where('is_online', '=', '0')->count();
        else
        {
        $count = Payment::where('payment_gateway', '=', 'offline')
        ->where('payment_status', '=', $status)
        ->count();
        } */
        }
        return $count;
    }
    /**
     * This method prepares the chart data for success and failed records
     * @param  [type] $payment_data [description]
     * @return [type]               [description]
     */
    public function getPaymentStats($payment_data)
    {
        if (!checkRole(getUserGrade(11))) {
            prepareBlockUserMessage();
            return back();
        }
        $payment_dataset        = [$payment_data->success];
        $payment_labels         = [getPhrase('success')];
        $payment_dataset_labels = [getPhrase('total')];
        $payment_bgcolor        = [getColor('', 4), getColor('', 9), getColor('', 18)];
        $payment_border_color   = [getColor('background', 4), getColor('background', 9), getColor('background', 18)];
        $payments_stats['data'] = (object) array(
            'labels'        => $payment_labels,
            'dataset'       => $payment_dataset,
            'dataset_label' => $payment_dataset_labels,
            'bgcolor'       => $payment_bgcolor,
            'border_color'  => $payment_border_color,
        );
        $payments_stats['type']  = 'bar';
        $payments_stats['title'] = getPhrase('overall_statistics');
        return $payments_stats;
    }
    /**
     * This method returns the overall monthly summary of the payments made with status success
     * @return [type] [description]
     */
    public function getPaymentMonthlyStats($type = 'offline', $symbol = '!=')
    {
        if (!checkRole(getUserGrade(11))) {
            prepareBlockUserMessage();
            return back();
        }
        $paymentObject          = new App\Payment();
        $payment_data           = (object) $paymentObject->getSuccessMonthlyData('', $type, $symbol);
        $payment_dataset        = [];
        $payment_labels         = [];
        $payment_dataset_labels = [getPhrase('total')];
        $payment_bgcolor        = [];
        $payment_border_color   = [];
        foreach ($payment_data as $record) {
            $color_number           = rand(0, 999);
            $payment_dataset[]      = $record->total;
            $payment_labels[]       = $record->month;
            $payment_bgcolor[]      = getColor('', $color_number);
            $payment_border_color[] = getColor('background', $color_number);
        }
        $payments_stats['data'] = (object) array(
            'labels'        => $payment_labels,
            'dataset'       => $payment_dataset,
            'dataset_label' => $payment_dataset_labels,
            'bgcolor'       => $payment_bgcolor,
            'border_color'  => $payment_border_color,
        );
        $payments_stats['type']  = 'line';
        $payments_stats['title'] = getPhrase('payments_reports_in') . ' ' . getCurrencyCode();
        return $payments_stats;
    }
    /**
     * This method displays the form for export payments list with different combinations
     * @return [type] [description]
     */
    public function exportPayments()
    {
        if (!checkRole(getUserGrade(11))) {
            prepareBlockUserMessage();
            return back();
        }
        $data['active_class'] = 'reports';
        $data['title']        = getPhrase('export_payments_report');
        $data['layout']       = getLayout();
        $data['record']       = false;
        $view_name            = getTheme() . '::payments.reports.payments-export';
        return view($view_name, $data);
    }
    public function doExportPayments(Request $request)
    {
        if (!checkRole(getUserGrade(11))) {
            prepareBlockUserMessage();
            return back();
        }
        $columns = array(
            'all_records' => 'bail|required',
        );
        if ($request->all_records == 0) {
            $columns['from_date'] = 'bail|required';
            $columns['to_date']   = 'bail|required';
        }
        $this->validate($request, $columns);
        $payment_status = $request->payment_status;
        $payment_type   = $request->payment_type;
        $record_type    = $request->all_records;
        $from_date      = '';
        $to_date        = '';
        if (!$record_type) {
            $from_date = $request->from_date;
            $to_date   = $request->to_date;
        }
        $records = [];
        $query   = '';
        if ($payment_status == 'all' && $payment_type == 'all' && $record_type == '1') {
            $query = Payment::whereRaw("1 = 1");
        } else {
            if ($record_type == 0) {
                $query = Payment::where('created_at', '>=', $from_date)
                    ->where('created_at', '<=', $to_date);
            } else {
                $query = Payment::whereRaw("1 = 1");
            }
            if ($payment_type != 'all') {
                if ($payment_type == 'online') {
                    $query->where('payment_gateway', '!=', 'offline');
                } else {
                    $query->where('payment_gateway', '=', 'offline');
                }
            }
            if ($payment_status != 'all') {
                $query->where('payment_status', '=', $payment_status);
            }
        }
        $records               = $query->get();
        $this->payment_records = $records;
        $this->downloadExcel();
    }
    public function getPaymentRecords()
    {
        return $this->payment_records;
    }
    public function downloadExcel()
    {
        if (!checkRole(getUserGrade(11))) {
            prepareBlockUserMessage();
            return back();
        }
        Excel::create('payments_report', function ($excel) {
            $excel->sheet('payments_records', function ($sheet) {
                $sheet->row(1, array('sno', 'ItemID', 'Purchased Item Name', 'User ID', 'Plan Startdate', 'Plan Enddate', 'Subscription Type', 'Payment Gateway', 'TransactionID', 'Paid by parent', 'Paid UserID', 'Cost', 'Coupon Applied', 'CouponID', 'Actual Cost', 'Discount Amount', 'After Discount', 'Paid Amount', 'Payment status', 'created_datetime', 'updated_datetime'));
                $records = $this->getPaymentRecords();
                $cnt     = 2;
                foreach ($records as $item) {
                    $item_type = ucfirst($item->plan_type);
                    if ($item->plan_type == 'combo') {
                        $item_type = 'Exam Series';
                    }
                    $sheet->appendRow($cnt, array(($cnt - 1), $item->item_id, $item->item_name, $item->user_id, $item->start_date, $item->end_date, $item_type, $item->payment_gateway, $item->transaction_id, $item->paid_by_parent, $item->paid_by, $item->cost, $item->coupon_applied, $item->coupon_id, $item->actual_cost, $item->discount_amount, $item->after_discount, $item->paid_amount, $item->payment_status, $item->created_at, $item->updated_at));
                    $cnt++;
                }
            });
        })->download('xlsx');
    }
    public function getPaymentRecord(Request $request)
    {
        if (!checkRole(getUserGrade(11))) {
            prepareBlockUserMessage();
            return back();
        }
        $payment_record   = Payment::where('id', '=', $request->record_id)->first();
        $result['status'] = 0;
        $result['record'] = null;
        if ($payment_record) {
            $result['status'] = 1;
            $result['record'] = $payment_record;
        }
        return json_encode($result);
    }
    public function validateAndApproveZeroDiscount($token, Request $request)
    {
        $payment_record = Payment::where('slug', '=', $token)->first();
        return $this->approvePayment($payment_record, $request, 1);
    }
    public function approvePayment(Payment $payment_record, Request $request, $iscoupon_zero = 0)
    {
        if ($payment_record->plan_type == 'combo') {
            $item_model = new ExamSeries();
        }
        if ($payment_record->plan_type == 'exam') {
            $item_model = new Quiz();
        }
        if ($payment_record->plan_type == 'lms') {
            $item_model = new LmsSeries();
        }
        $item_details                    = $item_model->where('id', '=', $payment_record->item_id)->first();
        $daysToAdd                       = '+' . $item_details->validity . 'days';
        $payment_record->start_date      = date('Y-m-d');
        $payment_record->end_date        = date('Y-m-d', strtotime($daysToAdd));
        $details_before_payment          = (object) json_decode($payment_record->other_details);
        $payment_record->coupon_applied  = $details_before_payment->is_coupon_applied;
        $payment_record->coupon_id       = $details_before_payment->coupon_id;
        $payment_record->actual_cost     = $details_before_payment->actual_cost;
        $payment_record->discount_amount = $details_before_payment->discount_availed;
        $payment_record->after_discount  = $details_before_payment->after_discount;
        $payment_record->paid_amount     = $details_before_payment->after_discount;
        if (!$iscoupon_zero) {
            $payment_record->admin_comments = $request->admin_comment;
        }
        $payment_record->payment_status = PAYMENT_STATUS_SUCCESS;
        $user                           = getUserRecord($payment_record->user_id);
        $email_template                 = 'offline_subscription_success';
        try {
            if ($iscoupon_zero) {
                // $email_template = 'subscription_success';
                // sendEmail($email_template, array('username'=>$user->name,
                // 'plan' => $payment_record->plan_type,
                // 'to_email' => $user->email));
                $email_template = 'subscription_success';
                $subject        = getPhrase($email_template);
                $template       = new EmailTemplate();
                $content_data   = $template->sendEmailNotification($email_template,
                    array('username' => $user->name,
                        'plan'           => $payment_record->plan_type,
                        'to_email'       => $user->email));
                // $user->notify(new \App\Notifications\UsersNotifcations($user,$content_data,$subject));
                try {
                    $user->notify(new \App\Notifications\AdminApproveOfflinePayment($user, $payment_record->plan_type));
                } catch (Exception $e) {
                }
            } else {
                // sendEmail($email_template, array('username'=>$user->name,
                // 'plan' => $payment_record->plan_type,
                // 'to_email' => $user->email, 'admin_comment'=>$request->admin_comment));
                $template     = new EmailTemplate();
                $subject      = getPhrase($email_template);
                $content_data = $template->sendEmailNotification($email_template,
                    array('username' => $user->name,
                        'plan'           => $payment_record->plan_type,
                        'to_email'       => $user->email,
                        'admin_comment'  => $request->admin_comment));
                // $user->notify(new \App\Notifications\UsersNotifcations($user,$content_data,$subject));
                try {
                    $user->notify(new \App\Notifications\AdminApproveOfflinePayment($user, $payment_record->plan_type));
                } catch (Exception $e) {
                }
            }
        } catch (Exception $ex) {
            $message   = getPhrase('\ncannot_send_email_to_user, please_check_your_server_settings');
            $exception = 1;
        }
        $payment_record->save();
        if ($payment_record->coupon_applied) {
            $this->couponcodes_usage($payment_record);
        }
        return true;
    }
    public function razorpaySuccess(Request $request)
    {
        // dd($request);
        $user = Auth::user();
        //Input items of form
        $input = Input::all();
        //get API Configuration
        $api = new Api(env('RAZORPAY_APIKEY'), env('RAZORPAY_SECRET'));
        //Fetch payment information by razorpay_payment_id
        $payment = $api->payment->fetch($input['razorpay_payment_id']);
        if (count($input) && !empty($input['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));
                // dd($response);
                $item = $this->getPackageDetails($request->type, $request->item_name);
                $user = getUserRecord();
                if ($request->parent_user) {
                    $user = getUserRecord($request->selected_child_id);
                }
                $payment_record                  = new Payment();
                $payment_record->transaction_id  = $request->razorpay_payment_id;
                $payment_record->item_id         = $item->id;
                $payment_record->item_name       = $item->title;
                $payment_record->plan_type       = $request->type;
                $payment_record->payment_gateway = 'Razorpay';
                $payment_record->slug            = $payment_record::makeSlug(getHashCode());
                $payment_record->cost            = $item->cost;
                $payment_record->user_id         = $user->id;
                $payment_record->payment_status  = PAYMENT_STATUS_SUCCESS;
                $payment_record->coupon_applied  = $request->is_coupon_applied;
                $payment_record->coupon_id       = $request->coupon_id;
                $payment_record->actual_cost     = $request->actual_cost;
                $payment_record->discount_amount = $request->discount_availed;
                $payment_record->after_discount  = $request->after_discount;
                $payment_record->paid_by         = $response->email;
                $payment_record->paid_amount     = $request->after_discount;
                $payment_record->paid_by_parent  = $request->parent_user;
                $daysToAdd                       = '+' . $item->validity . 'days';
                $payment_record->start_date      = date('Y-m-d');
                $payment_record->end_date        = date('Y-m-d', strtotime($daysToAdd));
                $payment_record->save();
                if ($payment_record->coupon_applied) {
                    $this->couponcodes_usage($payment_record);
                }
            } catch (\Exception $e) {
// dd($e->getMessage());
                flash('Ooops..!', $e->getMessage(), 'overlay');
                return redirect(URL_PAYMENTS_CHECKOUT . $request->type . '/' . $request->item_name);
            }
            flash('success', 'your_payment_done_successfully', 'success');
            return redirect(URL_PAYMENTS_LIST . $user->slug);
            // Do something here for store payment details in database...
        }
    }
    public function ajaxcheckout(Request $request)
    {
        $data        = $request->all();
        $user        = getUserRecord();
        $item        = LmsSeries::where('id', '=', $request->item)->first();
        $user_record = User::find($user->id);
        if ($item->cost <= $user_record->point) {
            $payment            = new Payment();
            $payment->item_id   = $request->item;
            $payment->item_name = $item->title;
            $payment->plan_type = 'lms';
            // $payment->payment_gateway = $payment_method;
            // $payment->slug            = $payment::makeSlug(getHashCode());
            $payment->cost    = $item->cost;
            $payment->user_id = $user->id;
            // $payment->paid_by_parent  = $other_details['paid_by_parent'];
            $payment->payment_status = 'success';
            // $payment->other_details   = json_encode($other_details);
            $payment->save();
            $user_record->point = $user->point - $item->cost;
            $user_record->save();
            $data = array('error' => 0);
        } else {
            $data = array('error' => 1, 'message' => 'Bạn không đủ số Hi Koi, vui lòng nạp thêm.');
        }
        return json_encode($data);
    }
    public function addOfflinePayment()
    {
        if (!checkRole(getUserGrade(11))) {
            prepareBlockUserMessage();
            return back();
        }
        $data['id']           = 45;
        $data['layout']       = getLayout();
        $data['active_class'] = 'users';
        $data['heading']      = 'Thanh toán Offline';
        $data['title']        = 'Thanh toán Offline';
        $view_name            = getTheme() . '::payments.offline.classes-details';
        return view($view_name, $data);
    }
    public function updateOfflinePayments(Request $request)
    {
        /*if(!checkRole(getUserGrade(4)))
        {
        prepareBlockUserMessage();
        return back();
        }*/
        // $user = User::where('slug', '=', $slug)->first();
        // $role_id = getRoleData('parent');
        $message  = '';
        $hasError = 0;
        DB::beginTransaction();
        //User is not having an account, create it and send email
        //Update the newly created user ID to the current user parent record
        $payment_method            = new PaymentMethod();
        $payment_method->type      = 0; //2 = offline. 1
        $payment_method->is_online = 0; //0 = offline
        $payment_method->user_id   = $request->parent_user_id;
        $payment_method->money     = $request->money;
        $payment_method->point     = $request->money / 1000;
        try {
            $payment_method->save();
            $user        = User::find($request->parent_user_id);
            $user->point = $user->point + $payment_method->point;
            $user->save();
            DB::commit();
            $message = 'Thanh toán thành công';
        } catch (Exception $ex) {
            DB::rollBack();
            $hasError = 1;
            $message  = $ex->getMessage();
        }
        if (!$hasError) {
            flash($message, '', 'success');
        } else {
            flash('Ooops', $message, 'error');
        }
        return back();
    }
    /**
     * This method returns the datatables data to view
     * @return [type] [description]
     */
    public function indexOfflinePayments($slug = '')
    {
        // if(!checkRole(getUserGrade(2)))
        // {
        //   prepareBlockUserMessage();
        //   return back();
        // }
        $records = PaymentMethod::join('users', 'users.id', '=', 'payment_method.user_id')
            ->select(['users.name', 'payment_method.money', 'payment_method.point', 'payment_method.created_at'])
            ->where('payment_method.is_online', '=', 0)
            ->orderBy('payment_method.created_at', 'desc');
        $table = Datatables::of($records);
        return $table->make();
    }
    public function getAllOnlinePayment()
    {
        if (!checkRole(getUserGrade(11))) {
            prepareBlockUserMessage();
            return back();
        }
        $data['id']           = 45;
        $data['layout']       = getLayout();
        $data['active_class'] = 'users';
        $data['heading']      = 'Thanh toán Online';
        $data['title']        = 'Thanh toán Online';
        $view_name            = getTheme() . '::payments.online.classes-details';
        return view($view_name, $data);
    }
    public function indexOnlinePayments($slug = '')
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }
        DB::statement(DB::raw('set @rownum=0'));
        $records = PaymentMethod::join('users', 'users.id', '=', 'payment_method.user_id')
            ->select([DB::raw('@rownum  := @rownum  + 1 AS stt'), 'users.name', 'payment_method.orderInfo',
                'payment_method.orderId', 'payment_method.amount', 'payment_method.orderType', 'payment_method.created_at'])
            ->where('payment_method.status', 1)
            ->distinct()
            ->orderBy('payment_method.created_at', 'desc')
            ->get();
        /*DB::statement(DB::raw('set @rownum=0'));
        $records = DB::table('payments')
        ->join('users', 'users.id', '=', 'payments.user_id')
        ->select([DB::raw('@rownum  := @rownum  + 1 AS stt'), 'users.name', 'payments.orderInfo',
        'payments.orderId', 'payments.amount', 'payments.orderType', 'payments.created_at'])
        ->where('payments.status', 1)
        ->orderBy('payments.created_at', 'desc')
        ->distinct()->get();*/
        $i = 1;
        foreach ($records as $record) {
            $record->stt = $i;
            $i++;
        }
        $table = Datatables::of($records)
        /*->editColumn('type', function ($records) {
        if ($records->type == 1) {
        $type = "Momo";
        }
        return $type;
        })*/
            ->editColumn('amount', function ($records) {
                return number_format($records->amount, 0, 0, '.') . 'đ';
            })
            ->editColumn('created_at', function ($records) {
                return date_format(date_create($records->created_at), 'm-d-Y H:s:i');
            })
        ;
        return $table->make();
    }
    public function historyUser()
    {
        // if(!checkRole(getUserGrade(4)))
        // {
        //   prepareBlockUserMessage();
        //   return back();
        // }
        $data['id']           = 45;
        $data['layout']       = getLayout();
        $data['active_class'] = 'users';
        $data['heading']      = 'Lịch sử thanh toán';
        $data['title']        = 'Lịch sử thanh toán';
        $view_name            = getTheme() . '::payments.history.classes-details';
        return view($view_name, $data);
    }
    public function historyPayment($slug = '')
    {
        // if(!checkRole(getUserGrade(2)))
        // {
        //   prepareBlockUserMessage();
        //   return back();
        // }
        $records = PaymentMethod::join('users', 'users.id', '=', 'payment_method.user_id')
            ->select(['payment_method.orderId', 'payment_method.is_online', 'payment_method.type', 'payment_method.amount', 'payment_method.point', 'payment_method.created_at'])
            ->where('payment_method.user_id', '=', Auth::user()->id)
            ->where('payment_method.status', '=', 1)
            ->orderBy('payment_method.created_at', 'desc');
        $table = Datatables::of($records)
            ->editColumn('is_online', function ($records) {
                $type = "Offline";
                if ($records->is_online == 1) {
                    $type = "Online";
                }
                return $type;
            })
            ->editColumn('type', function ($records) {
                $type = "";
                if ($records->type == 1) {
                    $type = "Momo";
                }
                return $type;
            });
        return $table->make();
    }
    public function historyBuyItem()
    {
        // if(!checkRole(getUserGrade(4)))
        // {
        //   prepareBlockUserMessage();
        //   return back();
        // }
        $data['id']           = 45;
        $data['layout']       = getLayout();
        $data['active_class'] = 'users';
        $data['heading']      = 'Lịch sử thanh toán';
        $data['title']        = 'Lịch sử thanh toán';
        $view_name            = getTheme() . '::payments.history.buy-item';
        return view($view_name, $data);
    }
    public function getHistoryBuyItem($slug = '')
    {
        // if(!checkRole(getUserGrade(2)))
        // {
        //   prepareBlockUserMessage();
        //   return back();
        // }
        $records = Payment::select(['item_name', 'cost', 'created_at'])
            ->where('user_id', '=', Auth::user()->id)
        // ->where('payment_method.is_online','=', 1)
            ->orderBy('created_at', 'desc');
        $table = Datatables::of($records)
        ;
        return $table->make();
    }
    public function atmTransfer(Request $request)
    {
        if ($request->ajax()) {
            if (!Auth::check()) {
                $data = array('error' => 2, 'message' => 'Vui lòng đăng nhập');
                return json_encode($data);
            }
            $record = LmsSeriesCombo::getRecordWithSlug($request->slug);
            if ($record != null) {
                $lmsseries_combo_check = DB::table('payment_method')
                    ->where('item_id', $record->id)
                    ->where('user_id', Auth::user()->id)
                    ->where('status', 2)
                    ->first();
                /* $data = array('error'=>$record,'message' =>$lmsseries_combo_check, 'id' =>Auth::user()->id );
                return json_encode($data);*/
                if ($lmsseries_combo_check != null) {
                    $data = array('error' => 3, 'message' => 'Đơn hàng đã được tạo trước đó');
                    return json_encode($data);
                }
            } else {
                $data = array('error' => 4, 'message' => 'Lỗi không xác định');
                return json_encode($data);
            }
            $amount         = $record->cost;
            $orderInfo      = $record->title;
            $orderId        = 'HIK' . time() . "";
            $requestId      = Auth::user()->id . '_' . $record->id . '_' . $record->type;
            $requestId_info = explode('_', $requestId);
            DB::beginTransaction();
            try {
                $payment               = new PaymentMethod();
                $payment->user_id      = Auth::user()->id;
                $payment->item_id      = $record->id;
                $payment->item_name    = $orderInfo;
                $payment->amount       = $amount;
                $payment->requestId    = $requestId;
                $payment->orderId      = $orderId;
                $payment->orderInfo    = $orderInfo;
                $payment->transId      = mt_srand(10);
                $payment->orderType    = 'transfer';
                $payment->payType      = 'transfer';
                $payment->extraData    = "merchantName=Hikari Academy";
                $payment->responseTime = date("Y-m-d H:i:s");
                $payment->status       = 2; //Update Giao dich thanh công 0=>1
                $payment->save();
                /*$lmsseries_combo = DB::table('lmsseries_combo')->where('id',$requestId_info[1])->first();
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
                }*/
                // phần gửi mail
                $thongtinchuyenkhoan = Auth::user()->username . ' ' . $record->code;
                sendEmail('taodonhang', array('to_email' => Auth::user()->email, 'name' => Auth::user()->name, 'donhang' => $record->title, 'gia' => $record->cost, 'thongtinchuyenkhoan' => $thongtinchuyenkhoan));
                DB::commit();
                $data = array('error' => 1, 'message' => 'Tạo đơn hàng thành công');
                return json_encode($data);
            } catch (Exception $e) {
                DB::rollBack();
                $data = array('error' => 0, 'message' => 'Tạo đơn hàng thất bại');
                return json_encode($data);
            }
        }
    }

    public function delatmTransfer(Request $request)
    {
        if ($request->ajax()) {
            if (!Auth::check()) {
                $data = array('error' => 2, 'message' => 'Vui lòng đăng nhập');
                return json_encode($data);
            }

            DB::beginTransaction();
            try {


                $record = PaymentMethod::where('id', '=', $request->slug)->get()->first();

                $record->status = 0;
                $record->updated_at  =date("Y-m-d H:i:s");
                $record->save();
                
                DB::commit();
                $data = array('error' => 1, 'message' => 'Hủy đơn hàng thành công');
                return json_encode($data);
            } catch (Exception $e) {
                DB::rollBack();
                $data = array('error' => 0, 'message' => 'Hủy đơn hàng thất bại');
                return json_encode($data);
            }
        }
    }

    public function listorderPayment()
    {
        if (!checkRole(getUserGrade(11))) {
            prepareBlockUserMessage();
            return back();
        }
        $data['active_class'] = 'order';
        $data['title']        = getPhrase('order_payments');
        /*$data['payments']              = (object) $this->prepareSummaryRecord('online');
        $data['payments_chart_data']   = (object) $this->getPaymentStats($data['payments']);
        $data['payments_monthly_data'] = (object) $this->getPaymentMonthlyStats();
        $data['payment_mode']          = 'online';*/
        $data['layout'] = getLayout();
        $view_name      = getTheme() . '::payments.order.list';
        return view($view_name, $data);
    }
    public function indexlistorderPayment($slug = '')
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }
        DB::statement(DB::raw('set @rownum=0'));
        $records = PaymentMethod::join('users', 'users.id', '=', 'payment_method.user_id')
            ->join('lmsseries_combo', 'lmsseries_combo.id', '=', 'payment_method.item_id')
            ->select([DB::raw('@rownum  := @rownum  + 1 AS stt'), 'users.name', 'users.email', 'payment_method.orderInfo',
                'lmsseries_combo.type', 'payment_method.amount', 'payment_method.created_at', 'payment_method.status', 'payment_method.id'])
            ->where('payment_method.orderType', 'transfer')
            ->distinct()
            ->orderBy('payment_method.created_at', 'desc')
            ->get();
        /*DB::statement(DB::raw('set @rownum=0'));
        $records = DB::table('payments')
        ->join('users', 'users.id', '=', 'payments.user_id')
        ->select([DB::raw('@rownum  := @rownum  + 1 AS stt'), 'users.name', 'payments.orderInfo',
        'payments.orderId', 'payments.amount', 'payments.orderType', 'payments.created_at'])
        ->where('payments.status', 1)
        ->orderBy('payments.created_at', 'desc')
        ->distinct()->get();*/
        $i = 1;
        foreach ($records as $record) {
            $record->stt = $i;
            $i++;
        }
        $table = Datatables::of($records)
            ->editColumn('amount', function ($records) {
                return number_format($records->amount, 0, 0, '.') . 'đ';
            })
            ->editColumn('created_at', function ($records) {
                return date_format(date_create($records->created_at), 'm-d-Y H:s:i');
            })
            ->editColumn('type', function ($records) {
                $dr_type = array(0 => 'Khóa học', 1 => 'Khóa luyện thi');
                return $dr_type[$records->type];
            })
            ->editColumn('status', function ($records) {
                $dr_status = array(0 => 'not', 1 => 'Success', 2 => 'Pending');
                return $dr_status[$records->status];
            })
            ->addColumn('action', function ($records) {
                if ($records->status == 1) {
                    $link_data = '<p>Đã xác nhận</p>';
                } else {
                    $link_data = '<a class="btn btn-success btn-rounded btn-fw" onclick="successOrder(' . $records->id . ')" ><i class="mdi mdi-check "></i> Xác nhận</a>';
                }
                return $link_data;
            })
        ;
        return $table->removeColumn('id')->make();
    }
    public function successorderPayment(Request $request)
    {
        if (!checkRole(getUserGrade(11))) {
            prepareBlockUserMessage();
            return back();
        }
        $record = PaymentMethod::where('id', '=', $request->slug)->first();
        try {
            DB::beginTransaction();
            $record->status       = 1;
            $record->responseTime = date("Y-m-d H:i:s");
            $record->created_by   = Auth::user()->id;
            $record->updated_at   = date("Y-m-d H:i:s");
            $record->save();
            $lmsseries_combo = DB::table('lmsseries_combo')->where('id', $record->item_id)->first();
            for ($i = 1; $i <= 5; $i++) {
                $n = 'n' . $i;
                if ($lmsseries_combo->$n > 0) {
                    DB::table('payments')->insert([
                        'user_id'            => $record->user_id,
                        'item_id'            => $lmsseries_combo->$n,
                        'time'               => $lmsseries_combo->time,
                        'payments_method_id' => $record->id,
                    ]);
                }
            }
            DB::commit();
            /* flash('Thêm thành công','', 'success');
        return redirect(url('payments-order'));*/
        } catch (Exception $e) {
            DB::rollBack();
            $data = array('error' => 2, 'message' => 'Xác nhận thất bại');
            return json_encode($data);
            // dd($e);
            /*flash('error','Thông báo sẽ tự đóng sau 1s', 'error');
        return redirect(url('payments-order'));*/
        }
        $data = array('error' => 1, 'message' => 'Xác nhận thành công');
        return json_encode($data);
    }
}
