<?php

/**
 * Flash Helper
 * @param  string|null  $title
 * @param  string|null  $text
 * @return void
 */


function flash($title = null, $text = null, $type='info')
{
  $flash = app('App\Http\Flash');

    // if (func_num_args() == 0) {
    //     return $flash;
    // }
  return $flash->$type($title, $text);
}

/**
 * Language Helper
 * @param  string|null  $phrase
 * @return string
 */
function getPhrase($key = null)
{

  $phrase = app('App\Language');

  if (func_num_args() == 0) {
    return '';
  }

  return  $phrase::getPhrase($key); 
}

/**
 * This method fetches the specified key in the type of setting
 * @param  [type] $key          [description]
 * @param  [type] $setting_type [description]
 * @return [type]               [description]
 */
function getSetting($key, $setting_type)
{
  return App\Settings::getSetting($key, $setting_type);
}

/**
 * This method fetches the specified key in the type of setting
 * @param  [type] $key          [description]
 * @param  [type] $setting_type [description]
 * @return [type]               [description]
 */
function getThemeSetting($key, $setting_type)
{
  return App\SiteTheme::getSetting($key, $setting_type);
}

/**
 * Language Helper
 * @param  string|null  $phrase
 * @return string
 */
function isActive($active_class = '', $value = '')
{
  $value = isset($active_class) ? ($active_class == $value) ? 'active' : '' : '';
  if($value)
    return "class = ".$value;
  return $value; 
}

function isActiveClass($active_class = '', $value = '')
{
  $value = isset($active_class) ? ($active_class == $value) ? 'active' : '' : '';
  if($value)
    return $value;
  return $value; 
}

/**
 * This method returns the path of the user image based on the type
 * It verifies wether the image is exists or not, 
 * if not available it returns the default image based on type
 * @param  string $image [Image name present in DB]
 * @param  string $type  [Type of the image, the type may be thumb or profile, 
 *                       by default it is thumb]
 * @return [string]      [returns the full qualified path of the image]
 */
function getProfilePath($image = '', $type = 'thumb')
{
  $obj = app('App\ImageSettings');
  $path = '';

  if($image=='') {
    if($type=='profile')
      return PREFIX.$obj->getDefaultProfilePicPath();
    return PREFIX.$obj->getDefaultprofilePicsThumbnailpath();
  }
  

  if($type == 'profile')
    $path = $obj->getProfilePicsPath();
  else
    $path = $obj->getProfilePicsThumbnailpath();
  $imageFile = $path.$image;

  if (File::exists($imageFile)) {
    return PREFIX.$imageFile;
  }

  if($type=='profile')
    return PREFIX.$obj->getDefaultProfilePicPath();
  return PREFIX.$obj->getDefaultprofilePicsThumbnailpath();

}

/**
 * This method returns the standard date format set by admin
 * @return [string] [description]
 */
function getDateFormat()
{
  $obj = app('App\GeneralSettings');
  return $obj->getDateFormat(); 
}


function getBloodGroups()
{
  return array(
    'A +ve'    => 'A +ve',
    'A -ve'    => 'A -ve',
    'B +ve'    => 'B +ve',
    'B -ve'    => 'B -ve',
    'O +ve'    => 'O +ve',
    'O -ve'    => 'O -ve',
    'AB +ve'   => 'AB +ve',
    'AB -ve'   => 'AB -ve',
  );
}

function getAge($date)
{


    // return Carbon::createFromDate(1984, 7, 17)->diff(Carbon::now())->format('%y years, %m months and %d days');
}

function getLibrarySettings()
{
  return json_decode((new App\LibrarySettings())->getSettings());
}

function getExamSettings()
{
  return json_decode((new App\ExamSettings())->getSettings());
}

/**
 * This method is used to generate the formatted number based 
 * on requirement with the follwoing formatting options
 * @param  [type]  $sno    [description]
 * @param  integer $length [description]
 * @param  string  $token  [description]
 * @param  string  $type   [description]
 * @return [type]          [description]
 */
function makeNumber($sno, $length=2, $token = '0',$type='left')
{
  if($type=='right')
    return str_pad($sno, $length, $token, STR_PAD_RIGHT);

  return str_pad($sno, $length, $token, STR_PAD_LEFT);

}

/**
 * This method returns the settings for the selected key
 * @param  string $type [description]
 * @return [type]       [description]
 */
function getSettings($type='')
{
  if($type=='lms')
    return json_decode((new App\LmsSettings())->getSettings());

  if($type=='subscription')
    return json_decode((new App\SubscriptionSettings())->getSettings());

  if($type=='general')
    return json_decode((new App\GeneralSettings())->getSettings());

  if($type=='email'){

    $dta = json_decode((new App\EmailSettings())->getSettings());
    return $dta;
  }

  if($type=='attendance')
    return json_decode((new App\AttendanceSettings())->getSettings());

}

/**
 * This method returns the role of the currently logged in user
 * @return [type] [description]
 */
function getRole($user_id = 0)
{
 if($user_id)
  return getUserRecord($user_id)->roles()->first()->name;

  // dd(Auth::user()->roles()->first());
  return Auth::user()->roles()->first()->name;
}

/**
 * This is a common method to send emails based on the requirement
 * The template is the key for template which is available in db
 * The data part contains the key=>value pairs 
 * That would be replaced in the extracted content from db
 * @param  [type] $template [description]
 * @param  [type] $data     [description]
 * @return [type]           [description]
 */
function sendEmail($template, $data)
{
  return (new App\EmailTemplate())->sendEmail($template, $data);
}

/**
 * This method returns the formatted by appending the 0's
 * @param  [type] $number [description]
 * @return [type]         [description]
 */
function formatPercentage($number)
{
 return sprintf('%.2f',$number).' %';
}


/**
 * This method returns the user based on the sent userId, 
 * If no userId is passed returns the current logged in user
 * @param  [type] $user_id [description]
 * @return [type]          [description]
 */
function getUserRecord($user_id = 0)
{
  if($user_id)
   return (new App\User())->where('id','=',$user_id)->first();
 return Auth::user();
}

/**
 * Returns the user record with the matching slug.
 * If slug is empty, it will return the currently logged in user
 * @param  string $slug [description]
 * @return [type]       [description]
 */
function getUserWithSlug($slug='')
{
  if($slug)
   return App\User::where('slug', $slug)->get()->first();
 return Auth::user();
}

/**
 * This method identifies if the url contains the specific string
 * @param  [type] $str [description]
 * @return [type]      [description]
 */
function urlHasString($str)
{
  $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
  if (strpos($url, $str)) 
    return TRUE;
  return FALSE;

}

function checkRole($roles)
{
 if(Entrust::hasRole($roles))
  return true;
return false;
}

function getUserGrade($grade = 2)
{
 switch ($grade) {
   case 1:
   return ['owner'];
   break; 
   case 2:
   return ['owner', 'admin'];
   break;
   case 3:
   return ['owner', 'admin', 'teacher'];
   break;
   case 4:
   return ['owner', 'admin', 'parent'];
   break;
   case 5:
   return ['student'];
   break;
   case 6:
   return ['test'];
   break; 
   case 7:
   return ['owner', 'admin','export'];
   break;
   case 8:
   return ['owner', 'admin','export', 'input'];
   break;
   case 9:
   return ['owner', 'admin', 'input'];
   break;
   case 11:
   return ['owner', 'admin', 'account'];
   break;  

 }
}
 /**
  * Returns the appropriate layout based on the user logged in
  * @return [type] [description]
  */
 function getLayout()
 {
  $layout = 'layouts.student.studentlayout';
  if(checkRole(getUserGrade(2))) {
    $layout             = 'layouts.admin.adminlayout';
  }
  if(checkRole(['parent'])) {
    $layout             = 'layouts.parent.parentlayout';
  }
  if(checkRole(['export'])) {
    $layout             = 'layouts.export.exportlayout';
  }
  if(checkRole(['input'])) {
    $layout             = 'layouts.input.parentlayout';
  }
  if(checkRole(['account'])) {
    $layout             = 'layouts.account.accountlayout';
  }

  return $layout;
}

function validateUser($slug)
{
  if($slug == Auth::user()->slug)
    return TRUE;
  return FALSE;
}

 /**
  * Common method to send user restriction message for invalid attempt 
  * @return [type] [description]
  */
 function prepareBlockUserMessage()
 {
  flash('Thông báo', 'Bạn không có quyền truy cập', 'error');
  return '';
}

 /**
  * Common method to send user restriction message for invalid attempt 
  * @return [type] [description]
  */
 function pageNotFound()
 {
  flash('Trang này không tồn tại', 'Trang này không tồn tại', 'error');
  return '';
}


function isEligible($slug)
{
 if(!checkRole(getUserGrade(2)))
 {
  if(!validateUser($slug)) 
  {
    if(!checkRole(['parent']) || !isActualParent($slug))
    {
     prepareBlockUserMessage();
     return FALSE;
   }
 }
}
return TRUE;
}

 /**
  * This method checks wether the student belongs to the currently loggedin parent or not
  * And returns the boolean value
  * @param  [type]  $slug [description]
  * @return boolean       [description]
  */
 function isActualParent($slug)
 {
   return (new App\User())
   ->isChildBelongsToThisParent(
    getUserWithSlug($slug)->id, 
    Auth::user()->id
  );

 }

/**
 * This method returns the role name or role ID based on the type of parameter passed
 * It returns ID if role name is supplied
 * It returns Name if ID is passed
 * @param  [type] $type [description]
 * @return [type]       [description]
 */
function getRoleData($type)
{

 if(is_numeric($type))
 {
        /**
         * Return the Role Name as the type is numeric
         */
        return App\Role::where('id','=',$type)->first()->name;
        
      }

     //Return Role Id as the type is role name
      return App\Role::where('name','=',$type)->first()->id;

    }

 /**
  * Checks the subscription details and returns the boolean value
  * @param  string  $type [this is the of package]
  * @return boolean       [description]
  */
 function isSubscribed($type = 'main',$user_slug='')
 {
  $user = getUserWithSlug();
  if($user_slug)
    $user = getUserWithSlug($user_slug);

  if($user->subscribed($type))
    return TRUE;
  return FALSE;
}

/**
 * This method will send the random color to use in graph
 * The random color generation is based on the number parameter 
 * As the border and bgcolor need to be same, 
 * We are maintainig number parameter to send the same value for bgcolor and background color
 * @param  string  $type   [description]
 * @param  integer $number [description]
 * @return [type]          [description]
 */
function getColor($type = 'background',$number = 777) {

    $hash = md5('color'.$number); // modify 'color' to get a different palette
    $color = array(
        hexdec(substr($hash, 0, 2)), // r
        hexdec(substr($hash, 2, 2)), // g
        hexdec(substr($hash, 4, 2))); //b
    if($type=='border')
      return 'rgba('.$color[0].','.$color[1].','.$color[2].',1)';
    return 'rgba('.$color[0].','.$color[1].','.$color[2].',0.2)';
  }


  function pushNotification($channels = ['owner','admin'], $event = 'newUser',  $options)
  {

   $pusher = \Illuminate\Support\Facades\App::make('pusher');

   $pusher->trigger( $channels,
    $event, 
    $options
  );



 }

/**
 * This method is used to return the default validation messages
 * @param  string $key [description]
 * @return [type]      [description]
 */
function getValidationMessage($key='required')
{
  $message = '<p ng-message="required">Vui lòng nhập vào đây</p>';    

  if($key === 'required')
    return $message;

  switch($key)
  {
    case 'minlength' : $message = '<p ng-message="minlength">'
    .'Số chữ quá ngắn vui lòng nhập lại'
    .'</p>';
    break;
    case 'maxlength' : $message = '<p ng-message="maxlength">'
    .'Số chữ quá dài vui lòng nhập lại'
    .'</p>';
    break;
    case 'pattern' : $message   = '<p ng-message="pattern">'
    .'Vui lòng nhập chính xác'
    .'</p>';
    break;
    case 'image' : $message   = '<p ng-message="validImage">'
    .'Vui lòng nhập hình'
    .'</p>';
    break;
    case 'email' : $message   = '<p ng-message="email">'
    .'Vui lòng nhập email chính xác'
    .'</p>';
    break;

    case 'number' : $message   = '<p ng-message="number">'
    .'Vui lòng nhập số chính xác'
    .'</p>';
    break;

    case 'confirmPassword' : $message   = '<p ng-message="compareTo">'
    .'Password xác nhận chưa đúng'
    .'</p>';
    break;
    case 'password' : $message   = '<p ng-message="minlength">'
    .'Password quá ngắn vui lòng nhập thêm'
    .'</p>';
    break;
    case 'phone' : $message   = '<p ng-message="minlength">'
    .'Vui lòng nhập số điện thoại chính xác'
    .'</p>';
    break;
  }
  return $message;
}

/**
 * Returns the predefined Regular Expressions for validation purpose
 * @param  string $key [description]
 * @return [type]      [description]
 */
function getRegexPattern($key='name')
{
  $phone_regx = getSetting('phone_number_expression', 'site_settings');
  $pattern = array(
    'name' => '/(^[A-Za-z0-9-. ]+$)+/',
    'email' => '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/',
    'phone'=>$phone_regx
  );
  return $pattern[$key];
}

function getPhoneNumberLength()
{
  return getSetting('site_favicon', 'site_settings');
}


function getArrayFromJson($jsonData)
{
  $result = array();
  if($jsonData)
  {
    foreach(json_decode($jsonData) as $key=>$value)
      $result[$key] = $value;
  }
  return $result;
}


function prepareArrayFromString($string='', $delimeter = '|')
{

  return explode($delimeter, $string);
}

/**
 * Returns the random hash unique code
 * @return [type] [description]
 */
function getHashCode()
{
  return bin2hex(openssl_random_pseudo_bytes(20));
}

/**
 * Sends the default Currency set for the project
 * @return [type] [description]
 */
function getCurrencyCode()
{
  return getSetting('currency_code','site_settings') ;
}

/**
 * Returns the max records per page
 * @return [type] [description]
 */
function getRecordsPerPage()
{
  return RECORDS_PER_PAGE;
}

/**
 * Checks wether the user is eligible to use the current item
 * @param  [type]  $item_id   [description]
 * @param  [type]  $item_type [description]
 * @return boolean            [description]
 */
function isItemPurchased($item_id, $item_type, $user_id = '')
{
  return App\Payment::isItemPurchased($item_id, $item_type, $user_id);
}

function humanizeDate($target_date)
{
 $created = new \Carbon\Carbon($target_date);
 $now = \Carbon\Carbon::now();
 $difference = ($created->diff($now)->days < 1) ? getPhrase('today') 
 : $created->diffForHumans($now);
 return $difference;
}


function getTimeFromSeconds($seconds)
{
  return gmdate("H:i:s",$seconds);
}

function getRazorKey()
{
  return env('RAZORPAY_APIKEY', 'rzp_test_A7YYdxPOae6Dpn');
}

function getRazorSecret()
{
  return env('RAZORPAY_SECRET','j1ikm980d6Lxs4ZNceOv44Sz');     
}

function getTheme()
{ 

  $theme_name  = 'themeone';

  $current_theme  = App\SiteTheme::where('is_active',1)->first();

  if($current_theme){
    $theme_name = $current_theme->theme_title_key;
  }

  Theme::set($theme_name);
  return Theme::current();
}

function getDefaultTheme()
{
  $current_theme  = App\SiteTheme::where('is_active',1)->first();

  if($current_theme){

   $theme_name = $current_theme->theme_title_key;
   return $theme_name;
 }
 return FALSE;
} 


function getThemeColor(){

  $current_theme  = App\SiteTheme::where('is_active',1)->first();
  
  return $current_theme->theme_color; 
  
}

function getLangugesOptions(){

  $languages_data = array();
  
  $languages_data['Japanese'] = 'Japanese';
  $languages_data['Vietnamese'] = 'Vietnamese';

  return $languages_data;
}

function hello()
{
  return 'Hello, World!';
}

function change_furigana($text, $return = "echo") {
  if (!empty($text)) {


    $pattern_box = '/\[box\](.+?)\[\/box\]/si';
    preg_match_all($pattern_box, $text, $matches_box);
    if (is_array($matches_box[0]) && count($matches_box[0]) > 0){
      foreach ($matches_box[0] as $key_box => $value_box) {
        $text_box =  '<div class="hikari_question_box">'.$matches_box[1][$key_box].'</div>';
        $text = str_replace($value_box, $text_box, $text);
      }

    }

    $pattern_border = '/\[border\](.+?)\[\/border\]/si';
    preg_match_all($pattern_border, $text, $matches_border);
    if (is_array($matches_border[0]) && count($matches_border[0]) > 0){
      foreach ($matches_border[0] as $key_border => $value_border) {
        $text_border =  '<span class="hikari_question_border">'.$matches_border[1][$key_border].'</span>';
        $text = str_replace($value_border, $text_border, $text);
      }

    }


    $pattern = '/\[furi (.+?)\]/';
    preg_match_all($pattern, $text, $matches);
    if (is_array($matches[0]) && count($matches[0]) > 0){
      $pattern_kanji = '/k=#(.+?)#/';
      $pattern_furigana = '/f=#(.+?)#/';
      foreach ($matches[0] as $key_m => $value_m) {
        preg_match($pattern_kanji, $value_m, $match_kanji);
        preg_match($pattern_furigana, $value_m, $match_furigana);
        if (count($match_kanji) > 0 && count($match_furigana) > 0) {
          $kanji_m = $match_kanji[1];
          $furi_m = $match_furigana[1];
          $chu_furigana = "<ruby>$kanji_m<rt>$furi_m</rt></ruby>";
          $text = str_replace($value_m, $chu_furigana, $text);
        }
      }
    }

    $pattern = '/\[line\]/';
    preg_match_all($pattern, $text, $matches);
    if (is_array($matches[0]) && count($matches[0]) > 0){
      foreach ($matches[0] as $key_line => $value_line) {
        $line = '<span style="display: block; height: 10px;">&nbsp;</span>';
        $text = str_replace('[line]', $line, $text);
      }

    }

    $pattern = '/\[br\]/';
    preg_match_all($pattern, $text, $matches);
    if (is_array($matches[0]) && count($matches[0]) > 0){
      foreach ($matches[0] as $key_line => $value_line) {
        $line = '<br/>';
        $text = str_replace('[br]', $line, $text);
      }
    }

    $pattern_underline = '/\[u\](.+?)\[\/u\]/si';
    preg_match_all($pattern_underline, $text, $matches_underline);
    if (is_array($matches_underline[0]) && count($matches_underline[0]) > 0){
      foreach ($matches_underline[0] as $key_m => $value_m) {
        $text_underline =  '<u>'.$matches_underline[1][$key_m].'</u>';
        $text = str_replace($value_m, $text_underline, $text);
      }

    }

    $pattern_center = '/\[center\](.+?)\[\/center\]/si';
    preg_match_all($pattern_center, $text, $matches_center);
    if (is_array($matches_center[0]) && count($matches_center[0]) > 0){
      foreach ($matches_center[0] as $key_center => $value_center) {
        $text_center =  '<p style="text-align: center;">'.$matches_center[1][$key_center].'</p>';
        $text = str_replace($value_center, $text_center, $text);
      }

    }
    $pattern_right = '/\[right\](.+?)\[\/right\]/si';
    preg_match_all($pattern_right, $text, $matches_right);
    if (is_array($matches_right[0]) && count($matches_right[0]) > 0){
      foreach ($matches_right[0] as $key_right => $value_right) {
        $text_right =  '<p style="text-align: right;">'.$matches_right[1][$key_right].'</p>';
        $text = str_replace($value_right, $text_right, $text);
      }

    }

    $pattern_audio = '/\[audio\](.+?)\[\/audio\]/s';
    preg_match_all($pattern_audio, $text, $matches_audio);
    if (is_array($matches_audio[0]) && count($matches_audio[0]) > 0){
      foreach ($matches_audio[0] as $key_m => $value_m) {
        $text_audio =  '';
        $text = str_replace($value_m, $text_audio, $text);
      }

    }
    
    $pattern_info = '/\[info\](.+?)\[\/info\]/s';
    preg_match_all($pattern_info, $text, $matches_info);
    if (is_array($matches_info[0]) && count($matches_info[0]) > 0){
      foreach ($matches_info[0] as $key_info => $value_info) {
        $text_info =  '';
        $text = str_replace($value_info, $text_info, $text);
      }

    }

    $pattern_image = '/\[img\](.+?)\[\/img\]/s';
    preg_match_all($pattern_image, $text, $matches_image);
    if (is_array($matches_image[0]) && count($matches_image[0]) > 0){
      foreach ($matches_image[0] as $key_m => $value_m) {
        $text_img =  '<img src="/public/uploads/exams/nghe_gokaku/'.$matches_image[1][$key_m].'">';
        $text = str_replace($value_m, $text_img, $text);
      }

    }

    $pattern_star = '/\[star\](.+?)\[\/star\]/s';
    preg_match_all($pattern_star, $text, $matches_star);

    if (is_array($matches_star[0]) && count($matches_star[0]) > 0){
      foreach ($matches_star[0] as $key_star => $value_star) {
        $star = intval ($matches_star[1][$key_star]);
        $text_star = '';

        switch ($star) {
          case 1:
          $text_star =  '<span class="hikari-sao"><img src="/public/images/sao300/sao1.png"></span>';
          break;
          case 2:
          $text_star =  '<span class="hikari-sao"><img src="/public/images/sao300/sao2.png"></span>';
          break;
          case 3:
          $text_star =  '<span class="hikari-sao"><img src="/public/images/sao300/sao3.png"></span>';
          break;
          case 4:
          $text_star =  '<span class="hikari-sao"><img src="/public/images/sao300/sao4.png"></span>';
          break;
        }

        $text = str_replace($value_star, $text_star, $text);
      }

    }

  }
  if ($return == 'echo') {
    echo $text;
  }
  if ($return == 'return') {
    return $text;
  }  
}

function change_furigana_text($text) {
  if (!empty($text)) {

    $pattern = '/\[furi (.+?)\]/';
    preg_match_all($pattern, $text, $matches);

    if (is_array($matches[0]) && count($matches[0]) > 0){
      $pattern_kanji = '/k=#(.+?)#/';
      $pattern_furigana = '/f=#(.+?)#/';
      foreach ($matches[0] as $key_m => $value_m) {
        preg_match($pattern_kanji, $value_m, $match_kanji);
        preg_match($pattern_furigana, $value_m, $match_furigana);
        if (count($match_kanji) > 0 && count($match_furigana) > 0) {
          $kanji_m = $match_kanji[1];
          $furi_m = $match_furigana[1];
          $chu_furigana = "<ruby>$kanji_m<rt>$furi_m</rt></ruby>";
          $text = str_replace($value_m, $chu_furigana, $text);
        }
      }

    }

    $pattern_info = '/\[info\](.+?)\[\/info\]/s';
    preg_match_all($pattern_info, $text, $matches_info);
    if (is_array($matches_info[0]) && count($matches_info[0]) > 0){
      foreach ($matches_info[0] as $key_info => $value_info) {
        $text_info =  '';
        $text = str_replace($value_info, $text_info, $text);
      }

    }
  }
  echo $text;
}

function change_furigana_admin($text) {
  if (!empty($text)) {

    $pattern = '/\[furi (.+?)\]/';
    preg_match_all($pattern, $text, $matches);

    if (is_array($matches[0]) && count($matches[0]) > 0){
      $pattern_kanji = '/k=#(.+?)#/';
      $pattern_furigana = '/f=#(.+?)#/';
      foreach ($matches[0] as $key_m => $value_m) {
        preg_match($pattern_kanji, $value_m, $match_kanji);
        preg_match($pattern_furigana, $value_m, $match_furigana);
        if (count($match_kanji) > 0 && count($match_furigana) > 0) {
          $kanji_m = $match_kanji[1];
          $furi_m = $match_furigana[1];
          $chu_furigana = "<ruby>$kanji_m<rt>$furi_m</rt></ruby>";
          $text = str_replace($value_m, $chu_furigana, $text);
        }
      }

    }

    $pattern_underline = '/\[u\](.+?)\[\/u\]/si';
    preg_match_all($pattern_underline, $text, $matches_underline);
    if (is_array($matches_underline[0]) && count($matches_underline[0]) > 0){
      foreach ($matches_underline[0] as $key_m => $value_m) {
        $text_underline =  '<u>'.$matches_underline[1][$key_m].'</u>';
        $text = str_replace($value_m, $text_underline, $text);
      }

    }

    $pattern_info = '/\[info\](.+?)\[\/info\]/s';
    preg_match_all($pattern_info, $text, $matches_info);
    if (is_array($matches_info[0]) && count($matches_info) > 0){
      foreach ($matches_info[0] as $key_info => $value_info) {
        $text_info =  $matches_info[1][$key_info];
        $text = str_replace($value_info, $text_info, $text);
      }

    }

    $pattern_box = '/\[box\](.+?)\[\/box\]/si';
    preg_match_all($pattern_box, $text, $matches_box);
    if (is_array($matches_box[0]) && count($matches_box[0]) > 0){
      foreach ($matches_box[0] as $key_box => $value_box) {
        $text_box =  $matches_box[1][$key_box];
        $text = str_replace($value_box, $text_box, $text);
      }

    }

    $pattern_border = '/\[border\](.+?)\[\/border\]/si';
    preg_match_all($pattern_border, $text, $matches_border);
    if (is_array($matches_border[0]) && count($matches_border[0]) > 0){
      foreach ($matches_border[0] as $key_border => $value_border) {
        $text_border =  '['.$matches_border[1][$key_border].']';
        $text = str_replace($value_border, $text_border, $text);
      }

    }

    $pattern_center = '/\[center\](.+?)\[\/center\]/si';
    preg_match_all($pattern_center, $text, $matches_center);
    if (is_array($matches_center[0]) && count($matches_center[0]) > 0){
      foreach ($matches_center[0] as $key_center => $value_center) {
        $text_center =  $matches_center[1][$key_center];
        $text = str_replace($value_center, $text_center, $text);
      }

    }


  }
  return $text;
}
function change_furigana_title($text) {

  if (!empty($text)) {
    $pattern = '/\[furi (.+?)\]/';
    preg_match_all($pattern, $text, $matches);

    if (is_array($matches[0]) && count($matches[0]) > 0){
      $pattern_kanji = '/k=#(.+?)#/';
      $pattern_furigana = '/f=#(.+?)#/';
      foreach ($matches[0] as $key_m => $value_m) {
        preg_match($pattern_kanji, $value_m, $match_kanji);
        preg_match($pattern_furigana, $value_m, $match_furigana);
        if (count($match_kanji) > 0 && count($match_furigana) > 0) {
          $kanji_m = $match_kanji[1];
          $furi_m = $match_furigana[1];
          $chu_furigana = "$kanji_m";
          $text = str_replace($value_m, $chu_furigana, $text);
        }
      }

    }
    $pattern_info = '/\[info\](.+?)\[\/info\]/s';
    preg_match_all($pattern_info, $text, $matches_info);
    if (is_array($matches_info[0]) && count($matches_info[0]) > 0){
      foreach ($matches_info[0] as $key_info => $value_info) {
        $text_info =  '';
        $text = str_replace($value_info, $text_info, $text);
      }

    }

    $pattern_icon = '/\[icon\](.+?)\[\/icon\]/s';
    preg_match_all($pattern_icon, $text, $matches_icon);
    //dd($matches_icon);
    if (is_array($matches_icon[0]) && count($matches_icon[0]) > 0){
      foreach ($matches_icon[0] as $key_icon => $value_icon) {
        $text_icon =  '<i class="text-primary fa fa-'.$matches_icon[1][$key_icon].'"></i>';
        $text = str_replace($value_icon, $text_icon, $text);
      }

    }

  }


  return $text;
}

function change_furigana_show_info($text) {

  if (!empty($text)) {
    $pattern = '/\[furi (.+?)\]/';
    preg_match_all($pattern, $text, $matches);

    if (is_array($matches[0]) && count($matches[0]) > 0){
      $pattern_kanji = '/k=#(.+?)#/';
      $pattern_furigana = '/f=#(.+?)#/';
      foreach ($matches[0] as $key_m => $value_m) {
        preg_match($pattern_kanji, $value_m, $match_kanji);
        preg_match($pattern_furigana, $value_m, $match_furigana);
        if (count($match_kanji) > 0 && count($match_furigana) > 0) {
          $kanji_m = $match_kanji[1];
          $furi_m = $match_furigana[1];
          $chu_furigana = "$kanji_m";
          $text = str_replace($value_m, $chu_furigana, $text);
        }
      }

    }
    $pattern_info = '/\[info\](.+?)\[\/info\]/s';
    preg_match_all($pattern_info, $text, $matches_info);
    if (is_array($matches_info[0]) && count($matches_info[0]) > 0){
      foreach ($matches_info[0] as $key_info => $value_info) {
              // $text_info =  '';
        $text_info =  '['.$matches_info[1][$key_info].']';
        $text = str_replace($value_info, $text_info, $text);
      }

    }
  }
  return $text;
}

function shuffle_assoc($list) { 
  if (!is_array($list)) return $list; 

  $keys = array_keys($list); 
  shuffle($keys); 
  $random = array(); 
  foreach ($keys as $key) { 
    $random[$key] = $list[$key]; 
  }
  return $random; 
} 

function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
  $output = NULL;
  if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
    $ip = $_SERVER["REMOTE_ADDR"];
    if ($deep_detect) {
      if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
  }
  $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
  $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
  $continents = array(
    "AF" => "Africa",
    "AN" => "Antarctica",
    "AS" => "Asia",
    "EU" => "Europe",
    "OC" => "Australia (Oceania)",
    "NA" => "North America",
    "SA" => "South America"
  );
  if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
    $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
    if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
      switch ($purpose) {
        case "location":
        $output = array(
          "city"           => @$ipdat->geoplugin_city,
          "state"          => @$ipdat->geoplugin_regionName,
          "country"        => @$ipdat->geoplugin_countryName,
          "country_code"   => @$ipdat->geoplugin_countryCode,
          "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
          "continent_code" => @$ipdat->geoplugin_continentCode,
          "ip"             => $_SERVER["REMOTE_ADDR"]
        );
        break;
        case "address":
        $address = array($ipdat->geoplugin_countryName);
        if (@strlen($ipdat->geoplugin_regionName) >= 1)
          $address[] = $ipdat->geoplugin_regionName;
        if (@strlen($ipdat->geoplugin_city) >= 1)
          $address[] = $ipdat->geoplugin_city;
        $output = implode(", ", array_reverse($address));
        break;
        case "city":
        $output = @$ipdat->geoplugin_city;
        break;
        case "state":
        $output = @$ipdat->geoplugin_regionName;
        break;
        case "region":
        $output = @$ipdat->geoplugin_regionName;
        break;
        case "country":
        $output = @$ipdat->geoplugin_countryName;
        break;
        case "countrycode":
        $output = @$ipdat->geoplugin_countryCode;
        break;
      }
    }
  }
  return $output;
}

function globalCheck(...$array){
  // array[0] =>{0:lmscategories ; 1:lmsseries ; 2:lmscontents}



  if (empty($array)){

    return false;
  }

  
    if (Auth::id() == null){
        return false;

    }

    $query = DB::table('users')
    ->join('classes_user','users.id','=','classes_user.student_id')
    ->join('classes','classes.id','=','classes_user.classes_id')
    ->join('lms_class','lms_class.classes_id','=','classes.id')
    ->join('lmscategories','lmscategories.id','=','lms_class.lmscategories_id')
    ->where('lms_class.delete_status',0)
    ->where('users.id',Auth::id())
    ->select('users.id')
    ;



    if ($array[0] == 0){
        $query->where('lmscategories.slug',(string)$array[1]);
    } else if ($array[0] == 1){
      $query->join('lmsseries','lmsseries.lms_category_id','=','lmscategories.id')
      ->join('lmsseries_data','lmsseries_data.lmsseries_id','=','lmsseries.id')
      ->join('lmscontents','lmscontents.id','=','lmsseries_data.lmscontent_id')
      ->where('lmscontents.delete_status',0)
      ->where('lmscontents.slug',(string)$array[1])
      ->where('lmsseries.slug',(string)$array[1]);
    }

    $records = $query->get();


 //   dd($records);

    if ($records->isEmpty()){
        return false;
    }

    return true;

}

function checkEmail($email){
  require_once 'VerifyEmail.class.php';
  // Initialize library class
  $mail = new VerifyEmail();

  // Set the timeout value on stream
  $mail->setStreamTimeoutWait(20);

  // Set debug output mode
  $mail->Debug= TRUE;
  $mail->Debugoutput= 'html';

  // Check if email is valid and exist
  if($mail->check($email)){
    return 1;
  }elseif(verifyEmail::validate($email)){
    return 2;
  }else{
    return 3;
  }
}



function sw_get_current_weekday($date) {
    date_default_timezone_set('Asia/Ho_Chi_Minh');
   // dd($weekday);
    $date = strtotime($date);
    $weekday = date("l",($date));
    $weekday = strtolower($weekday);
    switch($weekday) {
        case 'monday':
            $weekday = 'thứ hai';
            break;
        case 'tuesday':
            $weekday = 'thứ ba';
            break;
        case 'wednesday':
            $weekday = 'thứ tư';
            break;
        case 'thursday':
            $weekday = 'thứ năm';
            break;
        case 'friday':
            $weekday = 'thứ sáu';
            break;
        case 'saturday':
            $weekday = 'thứ bảy';
            break;
        default:
            $weekday = 'Chủ nhật';
            break;
    }
    return date('H:i',$date).' '.$weekday.' '.'ngày '.date('d-m-Y',$date);
}

function limit_words($string, $word_limit)
{
    $words = explode(" ",$string);
    return implode(" ", array_splice($words, 0, $word_limit)) ;
}