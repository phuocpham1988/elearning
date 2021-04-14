<?php
namespace App\Http\Controllers\Auth;
use App\User;
use App\RoleUser;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Mail;
use DB;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */
    use RegistersUsers;
    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
       return Validator::make($data, [
        'username' => 'required|max:255|unique:users',
        'name' => 'required|max:255',
        'email' => 'required|email|max:255|unique:users',
        'password' => 'required|min:6|confirmed',
    ]);
   }
   public function getRegister( $role = 'user' )
   {

    /*sendEmail('registration', array('user_name'=>'vampirephp', 'username'=>'ssss', 'to_email' => 'phuocpham1988@yahoo.com', 'password'=>'12345', 'confirmation_link' =>'1232222222'));*/
    /*Mail::send('welcome', '', function($message) {
            $message->to('phuocpham1988@gmail.com', 'Phuoc Pham')->subject('Welcome to the send mail!');
        });*/


        /*$data = array();
        Mail::send('welcome', $data, function($message) {
            $message->to('phuocpham1988@gmail.com', 'Phuoc Pham')->subject('Welcome to the send mail!');
        });
*/
        /*try{
        sendEmail('registration', array('user_name'=>'Phuoc', 'username'=>'phuoc', 'to_email' => 'phuocpham1988@gmail.com', 'password'=>'', 'confirmation_link' => '123'));
          }
         catch(Exception $ex)
        {
        }*/
        /*$data = array();
        Mail::send('welcome', $data, function($message) {
            $message->to('phuocpham1988@gmail.com', 'Phuoc Pham')->subject('Welcome to the send mail!');
        });
        exit;*/
        // try{
        // sendEmail('registration', array('user_name'=>'123', 'username'=>'phuocpham', 'to_email' => 'phuocpham1988@gmail.com', 'password'=>'123456', 'confirmation_link' => 'http://elearning.hikariacademy.edu.vn'));
        //   }
        //  catch(Exception $ex)
        // {
        // }
        // exit;
        $data['active_class']   = 'register';
        $data['title']  = 'Đăng ký';
        $rechaptcha_status    = getSetting('enable_rechaptcha','recaptcha_settings');
        $data['rechaptcha_status']  = $rechaptcha_status;
        // return view('auth.register', $data);
        $view_name = getTheme().'::auth.register';
        return view($view_name, $data);
    }
    public function confirmRegister() {
        echo 123123; 
        exit;
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    public function postRegister(Request $request)
    {
        $rechaptcha_status    = getSetting('enable_rechaptcha','recaptcha_settings');
    if ( $rechaptcha_status  == 'yes') {
         $columns = array(
            'name'     => 'bail|required|max:50|',
            'email'    => 'bail|required|unique:users,email',
            'email'    => 'bail|required|unique:users,email',
            'g-recaptcha-response' => 'required|captcha',
        );
         $messsages = array(
          'g-recaptcha-response.required'=>'Please Select Captcha',
          'email.'=>'Email đã được đăng ký',
      );
         $this->validate($request,$columns,$messsages);
     }
     else {
        $columns = array(
            'name'     => 'bail|required|max:100|',
            'email'    => 'bail|email|required|unique:users,email',
            'phone'    => 'bail|required|unique:users,phone',
        );
        $messsages = array(
          'email.unique'=>'Email đã được đăng ký',
          'email.email'=>'Email chưa đúng',
          'phone.unique'=>'Số điện thoại đã được đăng ký',
      );
        $this->validate($request,$columns,$messsages);
    }
    if(checkEmail($request->email) != 1){
        $columns = array(
            'email_exits'     => 'required',
        );
        $messsages = array(
            'email_exits.required' => 'Email không tồn tại',
        );
        $this->validate($request,$columns,$messsages);
    }
    $role_id = STUDENT_ROLE_ID;
    if ($request->is_student==1)
        $role_id = PARENT_ROLE_ID;
    $user           = new User();
    $name           = $request->name;
    $user->name     = $name;
    //$user->username = $request->username;
    $user->email    = $request->email;
    $user->phone    = $request->phone;
    $user->level    = 5;
    $password       = str_random(8);
    $user->password = bcrypt($password);

    $last_uid = DB::table('users')
        ->whereYear('created_at', '=', date('Y'))
        ->where('uid', '<>', null)
        ->orderBy('created_at', 'desc')
        ->first();
    if ($last_uid) {
        $uid_code  = $last_uid->uid;
        $uid_code  = ++$uid_code;
        $uid_code  = str_pad($uid_code, 5, '0', STR_PAD_LEFT);
        $user->uid = '' . $uid_code . '';
        $uid_code  = 'HID' . date('y') . $uid_code;
    } else {
        $user->uid = '00001';
        $uid_code  = 'HID' . date('y') . '00001';
    }
    $user->hid = $uid_code;
    $user->username = $uid_code;

    // $password       = $request->password;
    // $user->password       = bcrypt($password);
    $user->role_id        = 5;
    $user->is_register    = 1;
    $slug = $user::makeSlug($name);
    $user->slug           = $slug;
    $user->login_enabled  = 1;
    $user->confirmation_code = str_random(30);
    $link = URL_USERS_CONFIRM.'/'.$user->confirmation_code;

    $ip_info = ip_info('Visitor', "Location");
    $user->country_code = $ip_info['country_code'];
    $user->country = $ip_info['country'];
    $user->city = $ip_info['city'];
    $user->state = $ip_info['state'];
    $user->ip = $ip_info['ip'];
    
    $aa = $user->save();
    $user->roles()->attach($user->role_id);
        try{
            sendEmail('registration', array('name'=>$name, 'username'=>$user->username, 'to_email' => $user->email, 'password'=>$password));
        }
        catch(Exception $ex)
        {
        }
        flash('Đăng ký thành công','Bạn hãy kiểm tra email', 'success');
        return redirect( URL_USERS_LOGIN );
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $name = $data['first_name'] . ' ' . $data['last_name'];
        $user    = new User();
        $user->name     = $name;
        $user->first_name     = $data['first_name'];
        $user->last_name    = $data['last_name'];
        $user->email     = $data['email'];
        $user->password = bcrypt($data['password']);
        if( $data['role'] == 'vendor' ) {
            $user->role_id  = VENDOR_ROLE_ID;
        } else {
            $user->role_id  = USER_ROLE_ID;
        }
        $user->slug     = $user->makeSlug($user->name);
        $user->confirmation_code = str_random(30);
        $link = URL_USERS_CONFIRM.'/'.$user->confirmation_code;
        $user->save();
        $user->roles()->attach($user->role_id);
        /*try{
        sendEmail('registration', array('user_name'=>$user->email, 'username'=>$user->email, 'to_email' => $user->email, 'password'=>$data['password'], 'confirmation_link' => $link));
          }
         catch(Exception $ex)
        {
        }*/
        flash('Đăng ký thành công','', 'success');
        return $user;
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function register(Request $request)
    {
        $data = array(
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,           
        );
        $name = $data['first_name'] . ' ' . $data['last_name'];
        $user    = new User();
        $user->name     = $name;
        $user->first_name     = $data['first_name'];
        $user->last_name    = $data['last_name'];
        $user->email     = $data['email'];
        $user->password = bcrypt($data['password']);
        if( $data['role'] == 'vendor' ) {
            $user->role_id  = VENDOR_ROLE_ID;
        } else {
            $user->role_id  = USER_ROLE_ID;
        }
        $user->slug     = $user->makeSlug($user->name);
        $user->confirmation_code = str_random(30);
        $link = URL_USERS_CONFIRM . '/' . $user->confirmation_code;
        $user->save();
        $user->roles()->attach($user->role_id);
        /*try{
        sendEmail('registration', array('user_name'=>$user->email, 'username'=>$user->email, 'to_email' => $user->email, 'password'=>$data['password'], 'confirmation_link' => $link));
          }
         catch(Exception $ex)
        {
        }*/
        flash('success','Bạn đã đăng ký thành công', 'success');
        return redirect( URL_USERS_LOGIN );
    }
    public function studentOnlineRegistration()
    {
        return view('auth.student-online-registration');
    }
}