<?php
namespace App\Http\Controllers;
use \App;
use App\Payment;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\LmsCategory;
use App\LmsContent;
use App\LmsSeries;
use mysql_xdevapi\Exception;
use PhpParser\Node\Stmt\If_;
use Yajra\Datatables\Datatables;
use DB;
use Auth;
use Image;
use ImageSettings;
use File;
use Response;
class StudentLmsController extends Controller
{
  public function __construct()
  {
  $this->middleware('auth')->except('showLesson','studentAudit','studentExercise','showCombo');;
  }
  /**
  * Listing method
  * @return Illuminate\Database\Eloquent\Collection
  */
  public function index()
  {

      if(checkRole(getUserGrade(2)))
    {
      return back();
    }
    $data['active_class']       = 'lms';
    $data['title']              = 'Khóa học';
    $data['layout']              = getLayout();
    $data['categories']         = [];
    $user = Auth::user();
    $interested_categories      = null;
    if($user->settings)
    {
      $interested_categories =  json_decode($user->settings)->user_preferences;
    }
    if($interested_categories)    {
      if(count($interested_categories->lms_categories))
        $data['categories']         = Lmscategory::
      whereIn('id',(array) $interested_categories->lms_categories)
      ->paginate(getRecordsPerPage());
    }
    $data['user'] = $user;
    // return view('student.lms.categories', $data);
    $view_name = getTheme().'::student.lms.categories';
    return view($view_name, $data);
  }
  public function viewCategoryItems($slug)
  {
    $record = LmsCategory::getRecordWithSlug($slug);
    if($isValid = $this->isValidRecord($record))
      return redirect($isValid);
    $data['active_class']       = 'lms';
    $data['user']               = Auth::user();
    $data['title']              = 'Khóa học';
    $data['layout']             = getLayout();
    $data['series']             = LmsSeries::where('lms_category_id','=',$record->id)
    ->where('start_date','<=',date('Y-m-d'))
    ->where('end_date','>=',date('Y-m-d'))
    ->paginate(getRecordsPerPage());
    // return view('student.lms.lms-series-list', $data);
    $view_name = getTheme().'::student.lms.lms-series-list';
    return view($view_name, $data);
  }
  /**
  * This method displays the list of series available
  * @return [type] [description]
  */
  public function series()
  {
    if(checkRole(getUserGrade(2)))
    {
      return back();
    }
    $data['active_class']       = 'lms';
    $data['title']              = 'Khóa học';
    $data['layout']             = getLayout();
    $data['series']             = [];
    $user = Auth::user();
    $interested_categories      = null;
    if($user->settings)
    {
      $interested_categories =  json_decode($user->settings)->user_preferences;
    }
    if($interested_categories){
      if(count($interested_categories->lms_categories))
        $data['series']             = LmsSeries::
      where('start_date','<=',date('Y-m-d'))
      //->where('end_date','>=',date('Y-m-d'))
      //->whereIn('lms_category_id',(array) $interested_categories->lms_categories)
      ->paginate(getRecordsPerPage());
    }
    $data['user']               = $user;
    // return view('student.lms.lms-series-list', $data);
    $view_name = getTheme().'::student.lms.lms-series-list';
    return view($view_name, $data);
  }
  /**
  * This method displays all the details of selected exam series
  * @param  [type] $slug [description]
  * @return [type]       [description]
  */

        public function viewItem($slug, $content_slug='')
  {
    $record = LmsSeries::getRecordWithSlug($slug);
    if($isValid = $this->isValidRecord($record))
      return redirect($isValid);
    $content_record = FALSE;
    if($content_slug) {
      $content_record = LmsContent::getRecordWithSlug($content_slug);
      if($isValid = $this->isValidRecord($content_record))
        return redirect($isValid);
    }
    if($content_record){
      if($record->is_paid) {
        if(!isItemPurchased( $record->id, 'lms'))
        {
          prepareBlockUserMessage();
          return back();
        }
      }
    }
    $data['active_class']       = 'lms';
    $data['pay_by']             = '';
    $data['title']              = $record->title;
    $data['item']               = $record;
    $data['content_record']     = $content_record;
    $data['layout']              = getLayout();
    $view_name = getTheme().'::student.lms.series-view-item';
    return view($view_name, $data);
  }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */
        public function verifyPaidItem($slug, $content_slug)
  {
    if(!checkRole(getUserGrade(5)))
    {
      prepareBlockUserMessage();
      return back();
    }
    $record = LmsSeries::getRecordWithSlug($slug);
    if($isValid = $this->isValidRecord($record))
      return redirect($isValid);
    $content_record = LmsContent::getRecordWithSlug($content_slug);
    if($isValid = $this->isValidRecord($content_record))
      return redirect($isValid);
    if($content_record){
      if($record->is_paid) {
        if(!isItemPurchased($record->id, 'lms'))
        {
          return back();
        }
        else{
          $pathToFile= "public/uploads/lms/content"."/".$content_record->file_path;
          return Response::download($pathToFile);
        }
      }
      else{
        $pathToFile= "public/uploads/lms/content"."/".$content_record->file_path;
        return Response::download($pathToFile);
      }
    }
    else{
      flash('Ooops','File Does Not Exit','overlay');
      return back();
    }
  }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */
        public function content(Request $request, $req_content_type)
  {
    $content_type = $this->getRequestContentType($req_content_type);
    $category = FALSE;
    $query = LmsContent::where('content_type', '=', $content_type)
    ->where('is_approved',1);
    if($request->has('category')){
      $category = $request->category;
      $category_record = Lmscategory::getRecordWithSlug($category);
      $query->where('category_id',$category_record->id);
    }
    $data['category'] = $category;
    $data['content_type'] = $req_content_type;
    $data['list'] = $query->get();
    // dd($data['list']);
    $data['active_class']       = 'lms';
    $data['title']              = $req_content_type;
    $data['categories']         = Lmscategory::all();
    // return view('student.lms.content-categories', $data);
    $view_name = getTheme().'::student.lms.content-categories';
    return view($view_name, $data);
  }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */

        public function getRequestContentType($type)
  {
    if($type == 'video-course' || $type == 'video-courses')
      return 'vc';
    if($type == 'community-links')
      return 'cl';
    return 'sm';
  }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */

        public function getContentTypeFullName($type)
  {
    if($type=='sm')
      return 'study-materal';
    if($type=='vc')
      return 'video-courses';
    return 'community-links';
  }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */
        public function showContent($slug)
  {
    $record = Lmscontent::getRecordWithSlug($slug);
    if($isValid = $this->isValidRecord($record))
      return redirect($isValid);
    $data['active_class']       = 'lms';
    $data['title']              = $record->title;
    $data['category']           = $record->category;
    $data['record']             = $record;
    $data['content_type']     = $this->getContentTypeFullName($record->content_type);
    $data['series']       = array();
    if($record->is_series){
      $parent_id = $record->id;
      if($record->parent_id != 0)
        $parent_id = $record->parent_id;
      $data['series']     = LmsContent::where('parent_id', $parent_id)->get();
    }
    // return view('student.lms.show-content', $data);
    $view_name = getTheme().'::student.lms.show-content';
    return view($view_name, $data);
  }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */

    public function dequy_showLesson($data){
        // ['content_q' => $content_q, 'content_view' => $content_view,'stt' => $stt, 'slug' => $slug]

        $combo_slug = $data['combo_slug'];
        $records = $data['content_q'];
        $content_view = $data['content_view'];
        $stt = $data['stt'];
        $slug = $data['slug'];
        $parent_id = $data['parent_id'];
        $result = [];
        $array_video = ['1','2','6','9'];
        $array_loop = ['1','2','3','4','6','7'];
        $is_loop = false;
        foreach ($records as $key => $r){
            if ($r->parent_id == $parent_id){
                # pre check
                $class_color = ($content_view[0]->stt >= $r->stt) ? '#28a745' : '#9e9e9e';
                $class_i_color = ($content_view[0]->stt >= $r->stt) ? '#2a93e2' : '#000';

                $is_active = ($r->id == $stt) ? 'lesson_active' : null;

                if(in_array($r->type, $array_video)){

                    ($content_view[0]->stt >= $r->stt) ? $i_tag = '<img src="/public/assets/images/icon-seriess/play.png" style="width: 20px;margin-right: 5px;" alt="play">': $i_tag = '<img src="/public/assets/images/icon-seriess/sand-clock.png" style="width: 20px;margin-right: 5px;" alt="sand-clock">' ;

                    $video_url = ($content_view[0]->stt >= $r->stt)
                        ? PREFIX."learning-management/lesson/show/$combo_slug/$slug/".$r->id : '#';
                }else{
                    ($content_view[0]->stt >= $r->stt) ? $i_tag = '<img src="/public/assets/images/icon-seriess/checklist.png" style="width: 20px;margin-right: 5px;" alt="checklist">' : $i_tag = '<img src="/public/assets/images/icon-seriess/sand-clock.png" style="width: 20px;margin-right: 5px;" alt="sand-clock">';
                    $video_url = ($content_view[0]->stt >= $r->stt)
                        ? PREFIX."learning-management/lesson/exercise/$combo_slug/$slug/".$r->id : '#';
                }
                if($r->type == 5){
                    ($content_view[0]->stt >= $r->stt) ? $i_tag = '<img src="/public/assets/images/icon-seriess/compliant.png" style="width: 20px;margin-right: 5px;" alt="checklist">' : $i_tag = '<img src="/public/assets/images/icon-seriess/sand-clock.png" style="width: 20px;margin-right: 5px;" alt="sand-clock">';
                    $video_url = ($content_view[0]->stt >= $r->stt)
                        ? PREFIX.'learning-management/lesson/audit/'.$combo_slug.'/'.$slug.'/'.$r->id : '#';
                }
                $i_viewed = ($content_view[0]->stt > $r->stt)
                    ? '<i class="fa fa-check" style="color: green !important"></i>' : null;
                if($r->type == 8){
                    $i_tag = '<i style="color: '.$class_color.'" class="fa fa-angle-double-right" aria-hidden="true"></i>';
                }
               /* if($r->id == $stt){
                    if(!in_array($r->type,$array_video)){
                        return redirect(PREFIX."learning-management/lesson/show/$combo_slug/$slug/".$pre_lesson);
                    }
                }*/
                # end pre check

                if($r->parent_id == null){
                    $i_tag = ($r->type == 5) ? $i_tag : '<img src="/public/assets/images/icon-seriess/books.png" style="width: 20px;margin-right: 5px;" alt="books">';

                    $video_url = ($r->type == 5) ? $video_url : 'javascript:void(0)';
                    // $open_ul = ($r->type == 5) ? '<ul>' : '<ul>';
                    $result[$r->id]['tag'] = '
          <li>
          <h3>
          <a href="'.$video_url.'">
          '.$i_tag.'
          '.$r->bai.' '.$r->title.'
          </a>
          </h3><ul>
          ';
                    $result[$r->id]['level'] = '0';
                    $result[$r->id]['type'] = $r->type;
                    if($r->type == 9){
                        $class_color = ($content_view[0]->stt >= $r->stt) ? '#28a745' : null;
                        $i_tag = '<img src="/public/assets/images/icon-seriess/play.png" style="width: 20px;margin-right: 5px;" alt="play">';
                        $video_url = ($content_view[0]->stt >= $r->stt)
                            ? PREFIX."learning-management/lesson/show/$combo_slug/$slug/".$r->id : 'javascript:void(0)';
                        $result[$r->id]['tag'] = '
            <li>
            <h3>
            <a class="none-after '.$is_active.'" href="'.$video_url.'" style="color: '.$class_color.'">
            '.$i_tag.'
            '.$r->bai.' '.$r->title.'
            </a>
            </h3><ul>
            ';
                        $result[$r->id]['level'] = '0';
                        $result[$r->id]['type'] = $r->type;
                    }
                }else{
                    if($r->type == '8'){
                        $result[$r->id]['tag'] = '<li>
            <a href="javascript:void(0)" style="color: '.$class_color.'">'.$i_tag.$r->bai.'</a><ul>
            ';
                        $result[$r->id]['level'] = '1';
                        $result[$r->id]['type'] = $r->type;
                    }else{
                        $result[$r->id]['tag'] = '
            <li class="'.$is_active.'">
            <a href="'.$video_url.'" style="color: '.$class_color.'">'
                            .$i_tag.$r->bai.'
            </a>
            </li>
            ';
                        $result[$r->id]['level'] = '2';
                        $result[$r->id]['type'] = $r->type;
                    }
                }

                unset($records[$key]);
                $child = $this->dequy_showLesson(['content_q' => $records, 'content_view' => $content_view,
                    'stt' => $stt, 'slug' => $slug, 'parent_id' => $r->id,'combo_slug' => $combo_slug]);
                $result = $result + $child;
            }
        }
        return $result;
    }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */

    public function dequy_tryshowLesson($data){
        // ['content_q' => $content_q, 'content_view' => $content_view,'stt' => $stt, 'slug' => $slug]
        $combo_slug = $data['combo_slug'];
        $records = $data['content_q'];
        $content_view = $data['content_view'];
        //dump($content_view);
        $stt = $data['stt'];
        $slug = $data['slug'];
        $parent_id = $data['parent_id'];
        $result = [];
        $array_video = ['1','2','6','9'];
        $array_loop = ['1','2','3','4','6','7'];
        $is_loop = false;
        foreach ($records as $key => $r){
            if ($r->parent_id == $parent_id){
                # pre check
                $class_color = ($r->el_try == 1  ) ? '#2a93e2' : null;
                $class_i_color = ($r->el_try == 1 ) ? '#2a93e2' : '#000000';


                $is_active = ($r->id == $stt) ? 'lesson_active' : null;

                if(in_array($r->type, $array_video)){
                    $i_tag = '<img src="/public/assets/images/icon-seriess/play.png" style="width: 20px;margin-right: 5px;" alt="play">';
                    $video_url = ($r->el_try == 1)
                        ? PREFIX."learning-management/lesson/show/$combo_slug/$slug/".$r->id : 'javascript:void(0)';
                    $l_tag = ($r->el_try != 1) ? '<i style="color: #000;" class="fa fa-lock float-right pt-2" aria-hidden="true"></i>' : '';
                    $o_tag = ($r->el_try != 1) ? 'onclick="showpayment()"' : null;
                }else{
                    $i_tag = '<img src="/public/assets/images/icon-seriess/checklist.png" style="width: 20px;margin-right: 5px;" alt="checklist">';
                    $video_url = ($r->el_try == 1)
                        ? PREFIX."learning-management/lesson/exercise/$combo_slug/$slug/".$r->id : 'javascript:void(0)';
                    $l_tag = ($r->el_try != 1) ? '<i style="color: #000;" class="fa fa-lock float-right pt-2" aria-hidden="true"></i>' : '';
                    $o_tag = ($r->el_try != 1) ? 'onclick="showpayment()"' : null;
                }
                if($r->type == 5){
                    $i_tag = '<img src="/public/assets/images/icon-seriess/compliant.png" style="width: 20px;margin-right: 5px;" alt="compliant">';
                    $video_url = ($r->el_try == 1)
                        ? PREFIX.'learning-management/lesson/audit/'.$combo_slug.'/'.$slug.'/'.$r->id : 'javascript:void(0)';
                    $l_tag = ($r->el_try != 1) ? '<i style="color: #000;" class="fa fa-lock float-right pt-2" aria-hidden="true"></i>' : '';
                    $o_tag = ($r->el_try != 1) ? 'onclick="showpayment()"' : null;
                }
                $i_viewed = ($r->el_try == 1)
                    ? '<i class="fa fa-check" style="color: green !important"></i>' : null;
                if($r->type == 8){
                    $i_tag = '<i class="fa fa-angle-double-right" aria-hidden="true"></i>';
                }
               /* if($r->id == $stt){
                    if(!in_array($r->type,$array_video)){
                        return redirect(PREFIX."learning-management/lesson/show/$combo_slug/$slug/".$pre_lesson);
                    }
                }*/
                # end pre check

                if($r->parent_id == null){
                    $i_tag = ($r->type == 5) ? $i_tag : '<img src="/public/assets/images/icon-seriess/books.png" style="width: 20px;margin-right: 5px;" alt="books">';
                    $video_url = ($r->type == 5) ? $video_url : 'javascript:void(0)';
                    // $open_ul = ($r->type == 5) ? '<ul>' : '<ul>';
                    $result[$r->id]['tag'] = '
          <li>
          <h3>
          <a href="'.$video_url.'">
          '.$i_tag.'
          '.$r->bai.' '.$r->title.'
          </a>
          </h3><ul>
          ';
                    $result[$r->id]['level'] = '0';
                    $result[$r->id]['type'] = $r->type;
                    if($r->type == 9){
                        $o_tag = ($r->el_try != 1) ? 'onclick="showpayment()"' : null;
                        $i_tag = '<img src="/public/assets/images/icon-seriess/play.png" style="width: 20px;margin-right: 5px;" alt="play">';
                        $video_url = ($r->el_try == 1)
                            ? PREFIX."learning-management/lesson/show/$combo_slug/$slug/".$r->id :  'javascript:void(0)';
                        $l_tag = ($r->el_try != 1) ? '<i style="color: #000;" class="fa fa-lock float-right pt-2" aria-hidden="true"></i>' : '';
                        $result[$r->id]['tag'] = '
            <li>
            <h3>
            <a '.$o_tag.' class="none-after '.$is_active.'" href="'.$video_url.'">
            '.$i_tag.'
            '.$r->bai.' '.$r->title.'  '.$l_tag.'
            </a>
            </h3><ul>
            ';
                        $result[$r->id]['level'] = '0';
                        $result[$r->id]['type'] = $r->type;
                    }
                }else{
                    if($r->type == '8'){
                        $result[$r->id]['tag'] = '<li>
            <a href="javascript:void(0)" style="color: '.$class_color.'">'.$i_tag.$r->bai.'</a><ul>
            ';
                        $result[$r->id]['level'] = '1';
                        $result[$r->id]['type'] = $r->type;
                    }else{
                        $result[$r->id]['tag'] = '
            <li class="'.$is_active.'">
            <a '.$o_tag.' href="'.$video_url.'" style="color: '.$class_color.'">'
                            .$i_tag.$r->bai.$l_tag.'
            </a>
            </li>
            ';
                        $result[$r->id]['level'] = '2';
                        $result[$r->id]['type'] = $r->type;
                    }
                }

                unset($records[$key]);
                $child = $this->dequy_tryshowLesson(['content_q' => $records, 'content_view' => $content_view,
                    'stt' => $stt, 'slug' => $slug, 'parent_id' => $r->id,'combo_slug' => $combo_slug]);
                $result = $result + $child;
            }
        }
        return $result;
    }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */

  public function showLesson($combo_slug = '',$slug = '',$stt = ''){


    $data['active_class']       = 'exams';
    $data['title']              = 'Khóa luyện thi';
    $data['series']             = false;
    $data['layout']              = getLayout();

    $view_name = getTheme().'::student.lms.show-lesson';


    $data['current_series'] = DB::table('lmsseries')
    ->select('title')
    ->where([
      ['slug',$slug],
    ])->get()->first();

    if($slug == ''){
      flash('Ooops...!', getPhrase("page_not_found"), 'error');
      return back();
    }

    if (Auth::check()){
          $data['total_course'] = DB::table('lmsseries')
              ->join('lmscontents','lmsseries.id','=','lmscontents.lmsseries_id')
              ->where([
                  ['lmsseries.delete_status',0],
                  ['lmsseries.slug',$slug],
                  ['lmscontents.delete_status',0],
              ])
              ->whereNotIn('lmscontents.type', [0,8])
              ->distinct()
              ->get()->count();

          //dump($data['total_course'] );

          $data['current_course'] = DB::table('lms_student_view')
              ->join('lmscontents','lmscontents.id','=','lms_student_view.lmscontent_id')
              ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
              ->where([
                  ['users_id',Auth::id()],
                  ['lmsseries.slug',$slug],
                  //['finish',1],
                  ['lmscontents.delete_status',0],
                  ['lmsseries.delete_status',0],
              ])
              ->whereNotIn('lmscontents.type', [0,8])
              ->distinct()
              ->get()->count();

          //dump($data['current_course']);

      }

    $data['hi_combo'] = DB::table('lmsseries_combo')
        ->where('slug',$combo_slug)
        ->where('delete_status',0)
        ->get()->first();


    if (!Auth::check()){

        $data['url_categories'] = PREFIX.'lms/exam-categories/list';

        $content_q = DB::table('lmscontents')
            ->select('lmscontents.*')
            ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
            ->where([
                ['lmsseries.slug',$slug],
                ['lmscontents.delete_status',0]
            ])
            ->orderBy('stt','asc')
            ->get();

       // dd($content_q);

        # check empty content
        if($content_q->isEmpty()){
            // dd('empty content');
            flash('Ooops...!', getPhrase("page_not_found"), 'error');
            return back();
        }


        $check_hocthu  = DB::table('lmscontents')
            ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
            ->where([
                ['lmsseries.delete_status',0],
                ['lmsseries.slug',$slug],
                ['lmscontents.delete_status',0],
                ['lmscontents.el_try',1]
            ])
            ->distinct()
            ->count();

        if ($check_hocthu == 0){
            flash('Yêu cầu sở hữu khóa học', 'Vui lòng sở hữu khóa học để xem nội này', 'error');
            return redirect('/home');
        }

        $data['hi_koi'] = DB::table('lmsseries')->select('lmsseries.*')  ->where([
            ['lmsseries.slug',$slug],
        ]) ->get()->first();

        # check viewd lesson + time view
        $content_view = DB::table('lmscontents')
            ->select(['lmscontents.*'])
            ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
            ->where([
                ['lmsseries.slug',$slug],
                ['lmscontents.delete_status',0]
            ])
            ->whereNotIn('type',['0','8'])
            ->orderBy('stt','asc')
            ->get();



        if ($stt == ''){
            $stt = $content_view[0]->id;
        }
        $cur_stt = $stt;
        $data['current_lesson'] = '';
        $array_video = ['1','2','6','9'];
        $array_loop = ['1','2','3','4','6','7'];
        foreach($content_q as $r){
            if($r->id < $stt && in_array($r->type, $array_video)){
                $pre_lesson = $r->id;
            }

            if($r->id == $cur_stt){
                if(!in_array($r->type,$array_video)){
                    return redirect(PREFIX."learning-management/lesson/show/$slug/".$pre_lesson);
                }
                $class_color = '#e62020';
                $data['current_lesson'] = ($r->title == null) ? $r->bai : $r->bai .': '. $r->title;
                $data['current_description'] = $r->description;
                $data['current_video'] = ($r->el_try == 1 ? $r->file_path : null );
                $data['current_poster'] = IMAGE_PATH_UPLOAD_LMS_CONTENTS. $r->image;
                $data['contentslug'] = $r->id;
                $data['url_excer'] = PREFIX.'learning-management/lesson/exercise/'.$slug.'/'.$r->id;
                //dump($r->el_try);
            }
        }


        $new = $this->dequy_tryshowLesson(['content_q' => $content_q,
            'content_view' => $content_view,'stt' => $stt, 'slug' => $slug, 'parent_id' => null,'combo_slug' => $combo_slug,]);

        $lesson = '';
        $is_loop = false;

        $i = 0;
        foreach($new as $r){
            if(!in_array($r['type'], $array_loop) && $is_loop === true){
                $is_loop = 'end';
            }
            if($r['level'] == '0' && $i > 0){
                $lesson .= '</ul>';
            }
            if($is_loop === 'end'){
                $lesson .= '</ul></li>';
                $is_loop = false;
            }
            if($r['type'] != 8){
                $lesson .= $r['tag'];
            }elseif ($r['type'] == 8) {
                $lesson .= $r['tag'];
                $is_loop = true;
            }

            $i++;
        }

        $data['lesson_menu'] = $lesson;
        //die();
    }else{
        if(Auth::user()->role_id == 6){
            $_00content = DB::table('lmscontents')
                ->select('lmscontents.*')
                ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                ->where([
                    ['lmsseries.slug',$slug],
                ])
                ->whereNotIn('type',['0','8'])
                ->get();

            foreach($_00content as $r){
                $x = DB::table('lms_student_view')
                    ->updateOrInsert(
                        [
                            'lmscontent_id' => $r->id,
                            'users_id'      => Auth::id(),
                        ],
                        [
                            'finish'        => 1,
                            'type'          => $r->type,
                        ]
                    );
            }
        }else{

        }

        $data['url_categories'] = PREFIX.'lms/exam-categories/list';

        $content_q = DB::table('lmscontents')
            ->select('lmscontents.*')
            ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
            ->where([
                ['lmsseries.slug',$slug],
                ['lmscontents.delete_status',0]
            ])
            ->get();
        # check empty content
        if($content_q->isEmpty()){
            // dd('empty content');
            flash('Ooops...!', getPhrase("page_not_found"), 'error');
            return back();
        }

        $data['hi_koi'] = DB::table('lmsseries')->select('lmsseries.*')  ->where([
            ['lmsseries.slug',$slug],
        ]) ->get()->first();

        /*$data['checkpay'] = DB::table('payments')
            ->join('lmsseries', 'payments.item_id', '=', 'lmsseries.id')
            ->select('lmsseries.*')
            ->where([
                ['lmsseries.slug',$slug],
                ['payments.user_id',Auth::id()],
        ])->count();;*/

        $data['checkpay'] = DB::table('payment_method')
            ->select(DB::raw("(SELECT COUNT(id)  FROM payment_method WHERE payment_method.item_id 
                  = ".$data['hi_combo']->id."  AND payment_method.user_id = ".Auth::id()."  AND payment_method.status = 1 
                     AND  DATE_ADD(responseTime, INTERVAL IF(".$data['hi_combo']->time." = 0,90,IF(".$data['hi_combo']->time." = 1,180,365)) DAY) > NOW()) as payment"))
            ->distinct()
            ->get()->first();

        /*$data['checkpays'] = DB::select('select * from payment_method where  payment_method.item_id = :item_id and payment_method.user_id = :user_id and payment_method.status  = 1', ['item_id' => $data['hi_combo']->id,'user_id' => Auth::id()]);

        dump($data['checkpays'] );*/

        if ($data['hi_combo']->cost == 0  || $data['checkpay']->payment > 0 || Auth::user()->role_id == 6){

            # check viewd lesson + time view
            $content_view = DB::table('lms_student_view')
                ->select(['lms_student_view.*','lmscontents.stt','lmscontents.slug'])
                ->join('lmscontents','lmscontents.id','=','lms_student_view.lmscontent_id')
                ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                ->where([
                    ['users_id',Auth::id()],
                    ['lmsseries.slug',$slug],
                    ['finish',0],
                    ['lmscontents.delete_status',0]
                ])
                ->orderBy('lmscontent_id','desc')
                ->get();
            # check first view or next lesson view
            if($content_view->isEmpty()){
                # next lesson view
                $viewed_content = DB::table('lms_student_view')
                    ->select(['lms_student_view.*','lmscontents.stt','lmscontents.slug'])
                    ->join('lmscontents','lmscontents.id','=','lms_student_view.lmscontent_id')
                    ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                    ->where([
                        ['users_id',Auth::id()],
                        ['lmsseries.slug',$slug],
                        ['finish',1],
                        ['lmscontents.delete_status',0]
                    ])
                    ->orderBy('stt','desc')
                    ->get();
                if($viewed_content->isEmpty()){
                    # check first view
                    $id_content = DB::table('lmscontents')
                        ->select('lmscontents.*')
                        ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                        ->where([
                            ['lmsseries.slug',$slug],
                            ['lmscontents.delete_status',0],
                        ])
                        ->whereNotIn('type',['0','8'])
                        ->get()->first();
                }else{
                    # next lesson
                    $id_content = DB::table('lmscontents')
                        ->select('lmscontents.*')
                        ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                        ->where([
                            ['lmsseries.slug',$slug],
                            ['lmscontents.stt','>',$viewed_content[0]->stt],
                            ['lmscontents.delete_status',0],
                        ])
                        ->whereNotIn('type',['0','8'])
                        ->get()->first();
                }
                if($id_content != null){
                    $new_id = DB::table('lms_student_view')
                        ->insertGetId([
                            'lmscontent_id'       => $id_content->id,
                            'users_id'            => Auth::id(),
                            'view_time'           => 0,
                            'finish'              => 0,
                            'type'                => $id_content->type,
                        ]);
                    $content_view = DB::table('lms_student_view')
                        ->select(['lms_student_view.*','lmscontents.stt','lmscontents.slug'])
                        ->join('lmscontents','lmscontents.id','=','lms_student_view.lmscontent_id')
                        ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                        ->where([
                            ['users_id',Auth::id()],
                            ['lmsseries.slug',$slug],
                            ['finish',0],
                            ['lmscontents.delete_status',0]
                        ])
                        ->orderBy('lmscontent_id','desc')
                        ->get();
                }else{
                    $content_view = DB::table('lms_student_view')
                        ->select(['lms_student_view.*','lmscontents.stt','lmscontents.slug'])
                        ->join('lmscontents','lmscontents.id','=','lms_student_view.lmscontent_id')
                        ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                        ->where([
                            ['users_id',Auth::id()],
                            ['lmsseries.slug',$slug],
                            ['finish',1],
                            ['lmscontents.delete_status',0]
                        ])
                        ->orderBy('lmscontent_id','desc')
                        ->get();
                }

            }else{
                $data['current_time'] = $content_view[0]->view_time;
            }
            // dump($content_view);
            # check if come to page from series
            $pre_stt = $stt;
            if($stt == ''){
                $stt = $content_view[0]->lmscontent_id;
                // dump($stt);
            }else{
                $check_view = DB::table('lms_student_view')
                    ->select('finish')
                    ->join('lmscontents','lmscontents.id','=','lms_student_view.lmscontent_id')
                    ->where([
                        ['users_id',Auth::id()],
                        ['lmscontents.id',$stt],
                        ['lmscontents.delete_status',0]
                    ])
                    ->get();

                if($check_view->isEmpty()){

                    flash('Ooops...!', getPhrase("page_not_found"), 'error');
                    return back();
                }else{
                    if($check_view[0]->finish == '1'){
                        $data['viewed_video'] = true;
                        unset($data['current_time']);
                    }
                }
            }
            # get lesson
            $check_end_view = DB::table('lmscontents')
                ->select(['lmscontents.stt','lmscontents.id'])
                ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                ->where([
                    ['lmsseries.slug',$slug],
                    ['lmscontents.delete_status',0],
                ])
                ->whereNotIn('type',['0','8'])
                ->orderBy('stt','desc')
                ->get()->first();

            $cur_stt = $stt;
            if($check_end_view->id != null){
                if($stt == $check_end_view->id && $pre_stt == ''){
                    $check_first_view = DB::table('lmscontents')
                        ->select(['lmscontents.stt','lmscontents.id'])
                        ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                        ->where([
                            ['lmsseries.slug',$slug],
                            ['lmscontents.delete_status',0],
                        ])
                        ->whereNotIn('type',['0','8'])
                        ->orderBy('stt','asc')
                        ->get()->first();
                    $cur_stt = $check_first_view->id;
                }
            }
            $lesson = [];
            $i_parent = 0;
            $data['current_lesson'] = '';
            $array_video = ['1','2','6','9'];
            $array_loop = ['1','2','3','4','6','7'];
            foreach($content_q as $r){
                if($r->id < $stt && in_array($r->type, $array_video)){
                    $pre_lesson = $r->id;
                }

                if($r->id == $cur_stt){
                    if(!in_array($r->type,$array_video)){
                        return redirect(PREFIX."learning-management/lesson/show/$combo_slug/$slug/".$pre_lesson);
                    }
                    $class_color = '#e62020';
                    $data['current_lesson'] = ($r->title == null) ? $r->bai : $r->bai .': '. $r->title;
                    $data['current_description'] = $r->description;
                    $data['current_video'] = $r->file_path;
                    $data['current_poster'] = IMAGE_PATH_UPLOAD_LMS_CONTENTS. $r->image;
                    $data['contentslug'] = $r->id;
                    $data['url_excer'] = PREFIX.'learning-management/lesson/exercise/'.$slug.'/'.$r->id;
                }
            }
            $new = $this->dequy_showLesson(['content_q' => $content_q, 'content_view' => $content_view,
                'stt' => $cur_stt, 'slug' => $slug, 'parent_id' => null,'combo_slug' => $combo_slug,]);

            $lesson = '';
            $is_loop = false;

            $i = 0;
            foreach($new as $r){
                if(!in_array($r['type'], $array_loop) && $is_loop === true){
                    $is_loop = 'end';
                }
                if($r['level'] == '0' && $i > 0){
                    $lesson .= '</ul>';
                }
                if($is_loop === 'end'){
                    $lesson .= '</ul></li>';
                    $is_loop = false;
                }
                if($r['type'] != 8){
                    $lesson .= $r['tag'];
                }elseif ($r['type'] == 8) {
                    $lesson .= $r['tag'];
                    $is_loop = true;
                }

                $i++;
            }

        }else{



            $check_hocthu  = DB::table('lmscontents')
                ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                ->where([
                    ['lmsseries.delete_status',0],
                    ['lmsseries.slug',$slug],
                    ['lmscontents.delete_status',0],
                    ['lmscontents.el_try',1]
                ])
                ->distinct()
                ->count();

            if ($check_hocthu == 0){
                flash('Yêu cầu sở hữu khóa học', 'Vui lòng sở hữu khóa học để xem nội này', 'error');
                return redirect('/payments/lms/'.$combo_slug);
            }


            # check viewd lesson + time view
            $content_view = DB::table('lmscontents')
                ->select(['lmscontents.*'])
                ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                ->where([
                    ['lmsseries.slug',$slug],
                    ['lmscontents.delete_status',0]
                ])
                ->whereNotIn('type',['0','8'])
                ->orderBy('stt','asc')
                ->get();



            if ($stt == ''){
                $stt = $content_view[0]->id;
            }
            $cur_stt = $stt;
            $data['current_lesson'] = '';
            $array_video = ['1','2','6','9'];
            $array_loop = ['1','2','3','4','6','7'];
            foreach($content_q as $r){
                if($r->id < $stt && in_array($r->type, $array_video)){
                    $pre_lesson = $r->id;
                }

                if($r->id == $cur_stt){
                    if(!in_array($r->type,$array_video)){
                        return redirect(PREFIX."learning-management/lesson/show/$combo_slug/$slug/".$pre_lesson);
                    }
                    $class_color = '#e62020';
                    $data['current_lesson'] = ($r->title == null) ? $r->bai : $r->bai .': '. $r->title;
                    $data['current_description'] = $r->description;
                    $data['current_video'] = ($r->el_try == 1 ? $r->file_path : null );
                    $data['current_poster'] = IMAGE_PATH_UPLOAD_LMS_CONTENTS. $r->image;
                    $data['contentslug'] = $r->id;
                    $data['url_excer'] = PREFIX.'learning-management/lesson/exercise/'.$slug.'/'.$r->id;
                }
            }


            $new = $this->dequy_tryshowLesson(['content_q' => $content_q,
                'content_view' => $content_view,'stt' => $stt, 'slug' => $slug, 'parent_id' => null,'combo_slug' => $combo_slug,]);

            $lesson = '';
            $is_loop = false;

            $i = 0;
            foreach($new as $r){
                if(!in_array($r['type'], $array_loop) && $is_loop === true){
                    $is_loop = 'end';
                }
                if($r['level'] == '0' && $i > 0){
                    $lesson .= '</ul>';
                }
                if($is_loop === 'end'){
                    $lesson .= '</ul></li>';
                    $is_loop = false;
                }
                if($r['type'] != 8){
                    $lesson .= $r['tag'];
                }elseif ($r['type'] == 8) {
                    $lesson .= $r['tag'];
                    $is_loop = true;
                }

                $i++;
            }
        }


        $data['lesson_menu'] = $lesson;

    }

    // get commments
      $data['comment'] = DB::table('comments')
          ->where([
              ['user_id',Auth::id()],
              ['lmsseries_id',$data['hi_koi']->id],
              ['lmscombo_id',$data['hi_combo']->id],
              ['lmscontent_id',$stt],
              ['parent_id',0],
          ])
          ->get();

      $data['comment_child'] = DB::table('comments')
          ->where([
              ['user_id',Auth::id()],
              ['lmsseries_id',$data['hi_koi']->id],
              ['lmscombo_id',$data['hi_combo']->id],
              ['lmscontent_id',$stt],
              ['parent_id','!=',0],
          ])

          ->get();

      try {
          DB::table('comments')
              ->where([
                  ['user_id',Auth::id()],
                  ['lmsseries_id',$data['hi_koi']->id],
                  ['lmscombo_id',$data['hi_combo']->id],
                  ['lmscontent_id',$stt],
                  ['parent_id',0],
              ])
              ->update(
                  [
                      'status' => 2,
                      'updated_at' =>date("Y-m-d H:i:s"),
                  ]
              );
      }catch(Exception $e){
      }
      // end get commments
          $data['slug'] = $stt;
          $data['series'] = $slug;
          $data['combo_slug'] = $combo_slug;
// dd( $data['layout'] );
    return view($view_name, $data);
  }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */

    public function showCombo($slug){

        //$data['series'] =array();
        $record_combo = DB::table('lmsseries_combo')
            ->select('lmsseries_combo.n1','lmsseries_combo.n2','lmsseries_combo.n3','lmsseries_combo.n4','lmsseries_combo.n5')

            ->where('slug',$slug)
            ->distinct()
            ->get();

        $data['record_combo'] = DB::table('lmsseries_combo')

                ->where('slug',$slug)
                ->distinct()
                ->get()->first();

       // dd($data['record_combo']);
        $data_combo = array();
        for ($i = 1; $i <=5 ;$i ++){
            if ($record_combo[0]->{"n$i"} != null){
                $data_combo[] = $record_combo[0]->{"n$i"};
            }

        }


        $data['series'] = DB::table('lmsseries')
            ->select('lmsseries.*',DB::raw("(SELECT COUNT(id)  FROM lmscontents  
        WHERE lmscontents.delete_status = 0 AND type NOT IN(0,8) AND lmscontents.lmsseries_id = lmsseries.id) as lmscontents"),
                DB::raw("(SELECT COUNT(id)  FROM lmscontents  
        WHERE lmscontents.delete_status = 0 AND lmscontents.el_try = 1 
        AND type NOT IN(0,8) AND lmscontents.lmsseries_id = lmsseries.id) as try_lmscontents"),
                DB::raw("(0) as payment"))
            ->where([
                ['lmsseries.delete_status',0],

            ])
            ->whereIn('id',$data_combo)
            ->orderBy('order_by')
            ->distinct()
            ->get();




        if (Auth::check() && Auth::user()->role_id != 6  ){

            //dd(2);
            $data['checkpay'] = DB::table('payment_method')
                ->select(DB::raw("(SELECT COUNT(id)  FROM payment_method WHERE payment_method.item_id 
                  = ".$data['record_combo']->id."  AND payment_method.user_id = ".Auth::id()."  AND payment_method.status = 1
                   AND DATE_ADD(responseTime, INTERVAL IF(".$data['record_combo']->time." = 0,90,IF(".$data['record_combo']->time." = 1,180,365)) DAY) > NOW()) as payment"))
                ->distinct()
                ->get()->first();

            $data['series'] = DB::table('lmsseries')
                    ->select('lmsseries.*',DB::raw("(SELECT COUNT(id)  FROM lmscontents  
            WHERE lmscontents.delete_status = 0 AND type NOT IN(0,8) AND lmscontents.lmsseries_id = lmsseries.id) as lmscontents"),
                        DB::raw("(SELECT COUNT(id)  FROM lmscontents  
            WHERE lmscontents.delete_status = 0 AND lmscontents.el_try = 1 
            AND type NOT IN(0,8) AND lmscontents.lmsseries_id = lmsseries.id) as try_lmscontents"),
                        DB::raw("(SELECT COUNT(id)  FROM payment_method WHERE payment_method.item_id 
                      = ".$data['record_combo']->id." AND payment_method.user_id = ".Auth::id()."  AND payment_method.status = 1
                        AND DATE_ADD(responseTime, INTERVAL IF(".$data['record_combo']->time." = 0,90,IF(".$data['record_combo']->time." = 1,180,365)) DAY) > NOW()) as payment"))
                    ->where([
                        ['lmsseries.delete_status',0],

                    ])
                    ->whereIn('id',$data_combo)
                    ->orderBy('order_by')
                    ->distinct()
                    ->get();


        }

        if (Auth::check() && Auth::user()->role_id == 6){
            $data['series'] = DB::table('lmsseries')
                ->select('lmsseries.*',DB::raw("(SELECT COUNT(id)  FROM lmscontents  
        WHERE lmscontents.delete_status = 0 AND type NOT IN(0,8) AND lmscontents.lmsseries_id = lmsseries.id) as lmscontents"),
                    DB::raw("(SELECT COUNT(id)  FROM lmscontents  
        WHERE lmscontents.delete_status = 0 AND lmscontents.el_try = 1 
        AND type NOT IN(0,8) AND lmscontents.lmsseries_id = lmsseries.id) as try_lmscontents"),
                    DB::raw("(1) as payment"))
                ->where([
                    ['lmsseries.delete_status',0],

                ])
                ->whereIn('id',$data_combo)
                ->orderBy('order_by')
                ->distinct()
                ->get();
        }



        //dd($data['series']);
        $data['title']              = 'Khóa combo';
       // $data['series']             = false;
        $data['key']          = 'Combo';
        $data['active_class'] = 'Combo';
        $data['layout']              = getLayout();
        $view_name = getTheme().'::student.lms.showcombo';
        return view($view_name, $data);
    }

  /**
  * This method displays the list of series available
  * @return [type] [description]
  */
        public function updateTimeVideo(Request $request){

    if($request->ajax()){

        if (Auth::user() != null) {

            $record = DB::table('lmscontents')
                ->where('id', (string)$request->slug)
                ->select('stt', 'id')
                ->get();


            if (!$record->isEmpty()) {
                $affected = DB::table('lms_student_view')
                    ->where('users_id', Auth::id())
                    ->where('lmscontent_id', $record[0]->id)
                    /*   ->where('view_time','<=',(int)$request->currentTime)*/
                    ->update([
                        'view_time' => (int)$request->currentTime,
                    ]);

            }
        }

    }
  }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */
        public function finishTimeVideo(Request $request){
         if($request->ajax()){
        if (Auth::user() != null){
              $record =DB::table('lmscontents')
              ->where('id',(string) $request->slug)
              ->select('stt','id')
              ->get();
        if (!$record->isEmpty()){
        $affected = DB::table('lms_student_view')
        ->where('users_id', Auth::id())
        ->where('lmscontent_id', $record[0]->id)
        ->update([
          'finish' => 1,
        ]);
      }}
    }
  }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */

   public function nextUrl(Request $request){
    if($request->ajax()){

      $sendUrl = null;
      $status = 0;
      $message = 'fail';
      $record =DB::table('lmscontents')
      ->where('id',(int) $request->slug)
      ->where('delete_status',0)
      ->select('stt','lmsseries_id')
      ->get();

      if (!$record->isEmpty()){
        $recordurl = DB::table('lmscontents')
        ->where('stt','>=',((int)$record[0]->stt +1))
        ->where('lmsseries_id',$record[0]->lmsseries_id)
        ->where('delete_status',0)
        ->whereNotIn('type',[0,8])
        ->select('id','type')
        ->get();


        if (!$recordurl->isEmpty()){

          switch ($recordurl[0]->type) {
            case 1:
            case 2:
            case 6:
            $sendUrl = PREFIX.'learning-management/lesson/show/'.$request->combo.'/'.$request->series.'/'.$recordurl[0]->id;
            break;
            case 3:
            case 4:
            $sendUrl = PREFIX.'learning-management/lesson/exercise/'.$request->combo.'/'.$request->series.'/'.$recordurl[0]->id;
            break;
            case 5:
            $sendUrl = PREFIX.'learning-management/lesson/audit/'.$request->combo.'/'.$request->series.'/'.$recordurl[0]->id;
            break;
            default:
            $sendUrl = null;
            break;
          }

          $status = 1;
          $message = 'ok';
        }

      }

      return response()->json([
        'url' => $sendUrl,
        'status' =>(int) $status,
        'message' => $message,
      ]);
    }
  }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */

  public function studentExercise(Request $request,$combo_slug = '',$series = '',$slug = ''){
      if (Auth::check() && Auth::user()->role_id != 6){
          $check = DB::table('lms_student_view')
              ->select('id')
              ->where('lmscontent_id',$slug)
              ->where('users_id',Auth::id())
              ->get();

          if ($check->isEmpty()){
              return redirect('home');
          }

          $data['hi_combo'] = DB::table('lmsseries_combo')
              ->where('slug',$combo_slug)
              ->where('delete_status',0)
              ->get()->first();

          $data['checkpay'] = DB::table('payment_method')
              ->select(DB::raw("(SELECT COUNT(id)  FROM payment_method WHERE payment_method.item_id 
                  = ".$data['hi_combo']->id."  AND payment_method.user_id = ".Auth::id()."     AND 
                  DATE_ADD(responseTime, INTERVAL IF(".$data['hi_combo']->time." = 0,90,IF(".$data['hi_combo']->time." = 1,180,365)) DAY) > NOW()) as payment"))
              ->distinct()
              ->get()->first();

          if ($data['checkpay']->payment == 0){
              return redirect('home');
          }

      }



    $records = DB::table('lmscontents')
    ->join('lms_exams','lms_exams.content_id','=','lmscontents.id')
    ->where('lmscontents.id',$slug)
    ->where('lmscontents.delete_status',0)
    ->where('lms_exams.delete_status',0)
     /*->where('lms_exams.dang','=',5)*/
     ->whereNotNull('lms_exams.dang')
    ->select('lms_exams.id','label','dang','cau','mota','dapan',DB::raw("CONCAT_WS('-,-',luachon1,luachon2,luachon3,luachon4) AS answers") )
    ->get();

    $data['name'] = DB::table('lmscontents')
    ->select('bai')
    ->where('lmscontents.id',$slug)
    ->first();

//      header('Content-type: text/html; charset=UTF-8') ;
//       foreach($records as $r){
//         //dump($r);
//
//           echo mb_convert_encoding(str_replace('A：','A。',$r->mota),"UTF-8","auto");
//           echo '<br>';
//       }
// die;



    if (!$records->isEmpty()){
      foreach ($records as $key => $value) {
            //$records[$key]->answers_furigana = explode(',', change_furigana(trim($value->answers,'return')));
        $records[$key]->mota = change_furigana( mb_convert_encoding(str_replace('＿＿','__',$value->mota),"UTF-8","auto"),'return');
        $records[$key]->answers = explode('-,-', trim($value->answers));

      }


      foreach ($records as $key => $record) {
        $valueAnswers = array();
        foreach ($record->answers as $answer){
          $valueAnswers[] = change_furigana( $answer,'return');
        }
        $records[$key]->answers = $valueAnswers;

      }
      /*$record5 = array();
      foreach ($records as $key => $record) {
        if ($record->dang == 5){
          if (count($record->answers) >1){
            $record5[] = $records[$key];
          }
          $records->forget($key);
                //unset($records[$key]);
        }
      }


        $reindex = count($records); //normalize index

        $count5 = count($record5);

        $record5 = array_merge(array('dang' => 5,'quest' => $record5));

        $records = $records->toArray();
        if ($count5 > 0 ){
            $records[$reindex] = (object) $record5;
          }*/
          $sendUrl = null;
          $recordback =DB::table('lmscontents')
          ->where('id',(int) $request->slug)
          ->select('stt','lmsseries_id')
          ->get();


          $recordurl = DB::table('lmscontents')
          ->where('stt','<',((int)$recordback[0]->stt))
          ->where('lmsseries_id',$recordback[0]->lmsseries_id)
         // ->where('el_try',1)
          ->whereNotIn('type',[3,4,5,0,8])
          ->select('id','type')
          ->orderBy('stt', 'desc')
          ->get();

            //dd($recordurl);
          if (!$recordurl->isEmpty()){
            $sendUrl = PREFIX.'learning-management/lesson/show/'.$combo_slug.'/'.$request->series.'/'.$recordurl[0]->id;
          }else{
            $sendUrl = PREFIX;
          }

        }
        if(!isset($sendUrl)){
          $sendUrl = PREFIX;
        }

        $data['class']       = 'exams';
        $data['title']              = 'Khóa học Tổng hợp - Luyện thi N4';
        $data['series']             = $series;
        $data['slug']                = $slug;
        $data['combo_slug']          = $combo_slug;
        $data['records']            = $records;
        $data['count_records']      = count($records);
        $data['back_url']            = $sendUrl;
        $data['sendUrl']        = $sendUrl;
        $data['layout']              = 'layouts.exercise.exerciselayout';
        $view_name = getTheme().'::student.exercise.student-exercise';

        return view($view_name, $data);
      }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */

  public function studentAudit(Request $request,$combo_slug ='',$series = '',$slug = ''){


          if (Auth::check() && Auth::user()->role_id != 6){
              $check = DB::table('lms_student_view')
                  ->select('id')
                  ->where('lmscontent_id',$slug)
                  ->where('users_id',Auth::id())
                  ->get();

              if ($check->isEmpty()){
                  return redirect('home');
              }

              $data['hi_combo'] = DB::table('lmsseries_combo')
                  ->where('slug',$combo_slug)
                  ->where('delete_status',0)
                  ->get()->first();

              $data['checkpay'] = DB::table('payment_method')
                  ->select(DB::raw("(SELECT COUNT(id)  FROM payment_method WHERE payment_method.item_id 
                  = ".$data['hi_combo']->id."  AND payment_method.user_id = ".Auth::id()."     AND 
                  DATE_ADD(responseTime, INTERVAL IF(".$data['hi_combo']->time." = 0,90,IF(".$data['hi_combo']->time." = 1,180,365)) DAY) > NOW()) as payment"))
                  ->distinct()
                  ->get()->first();

              if ($data['checkpay']->payment == 0){
                  return redirect('home');
              }
          }


        $records = DB::table('lmscontents')
        ->join('lms_test','lms_test.content_id','=','lmscontents.id')
        ->where('lmscontents.id',$slug)
        ->where('lmscontents.delete_status',0)
        ->where('lms_test.delete_status',0)
            ->whereNotNull('lms_test.dang')
        //->where('lms_test.dang','=',7)
        ->select('lms_test.id','dang','cau','mota','dapan','display',DB::raw("CONCAT_WS('-,-',luachon1,luachon2,luachon3,luachon4) AS answers") )
       /* ->orderBy('lms_test.cau')
       ->orderBy('lms_test.dang')*/
       ->orderBy('lms_test.id')
       ->get();



       if (!$records->isEmpty()){
        foreach ($records as $key => $value) {
            //$records[$key]->answers_furigana = explode(',', change_furigana(trim($value->answers,'return')));
          $records[$key]->mota = change_furigana( trim($value->mota),'return');
          if($records[$key]->dang == '7'){
            $records[$key]->mota = str_replace("\n", "\n\n", $records[$key]->mota);
          }
          $records[$key]->answers = explode('-,-', trim($value->answers));

        }


        foreach ($records as $key => $record) {
          $valueAnswers = array();
          foreach ($record->answers as $answer){
            $valueAnswers[] = change_furigana( $answer,'return');
          }
          $records[$key]->answers = $valueAnswers;

        }



      /*  $result = array();

        foreach($records as $key => $value){

          $result[$value->dang][] = (object) $value;

        }

        $records = $result;*/

           $sendUrl = null;
           $recordback =DB::table('lmscontents')
               ->where('id',(int) $request->slug)
               ->select('stt','lmsseries_id')
               ->get();


           $recordurl = DB::table('lmscontents')
               ->where('stt','<',((int)$recordback[0]->stt))
               ->where('lmsseries_id',$recordback[0]->lmsseries_id)
               ->whereNotIn('type',[3,4,5,0,8])
               ->select('id','type')
               ->orderBy('stt', 'desc')
               ->get()
           ;

           //dd($recordurl);
           if (!$recordurl->isEmpty()){
               $sendUrl = PREFIX.'learning-management/lesson/show/'.$combo_slug.'/'.$request->series.'/'.$recordurl[0]->id;
           }else{
               $sendUrl = PREFIX;
           }



      }


       if(!isset($sendUrl)){
                $sendUrl = PREFIX;
            }

            $data['name'] = DB::table('lmscontents')
                ->select('bai')
                ->where('lmscontents.id',$slug)
                ->first();

      flash('Tải hoàn tất đề thi','Thông báo tự đóng sau 1s', 'success');

      $data['class']       = 'exams';
      $data['title']              = 'Khóa học Tổng hợp - Luyện thi N4';
      $data['series']             = $series;
      $data['slug']                =$slug;
      $data['combo_slug']                =$combo_slug;
      $data['back_url']            = $sendUrl;
      $data['records']            = $records;
      $data['layout']              = 'layouts.exercise.exerciselayout';
      $view_name = getTheme().'::student.exercise.student-audit';

      return view($view_name, $data);
    }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */

  public function storeResut(Request $request,$combo_slug ='',$series = '',$slug = ''){

      if($request->isMethod('post')) {


        try {

          flash('success','Thông báo tự đóng sau 1s', 'success');

              //$request->session()->flash('status', 'Task was successful!');

          $content_id = $request->content_id;
          $time = $request->time;
          $dataQuest = $request->all();
          unset($dataQuest['_token']);
          unset($dataQuest['content_id']);
          unset($dataQuest['time']);

          $records = DB::table('lmscontents')
          ->join('lms_test','lms_test.content_id','=','lmscontents.id')
          ->where('lmscontents.id',$slug)
          ->where('lmscontents.delete_status',0)
          ->where('lms_test.delete_status',0)
          ->select('lms_test.id','dang','cau','mota','dapan','diem','display',DB::raw("CONCAT_WS(',',luachon1,luachon2,luachon3,luachon4) AS answers") )
          ->orderBy('lms_test.id')
          ->get();

          $totalValue = 0;
          $point = 0;
          foreach ($records as $keyRecord => $valueRecord){
            $point = $point + $valueRecord->diem;
            $correct = 0;
            $check = 999;
            foreach ($dataQuest as $key => $value){
              $idKey = filter_var(str_replace('quest_','',$key),FILTER_SANITIZE_NUMBER_INT);
              if($valueRecord->id == $idKey){
                if ($valueRecord->dapan == $value){
                  $totalValue = (int) $totalValue + (int) $valueRecord->diem;
                  $correct = 1;
                }
                $check = $value;
                unset($dataQuest['$key']);
                break;

              }
            }
            $records[$keyRecord]->correct = $correct;
            $records[$keyRecord]->check = $check;
          }


          foreach ($records as $key => $value) {
            $records[$key]->mota = change_furigana( trim($value->mota),'return');
            $records[$key]->answers = explode(',', trim($value->answers));

          }


          foreach ($records as $key => $record) {
            $valueAnswers = array();
            foreach ($record->answers as $answer){
              $valueAnswers[] = change_furigana( $answer,'return');
            }
            $records[$key]->answers = $valueAnswers;

          }

             /* $result = array();

              foreach($records as $key => $value){
                  $result[$value->dang][] = (object) $value;
              }

              $records = $result;*/
             // dd($records);

              $passed = (int)$totalValue / (int)$point >= 0.6 ? 1 :0;
              $sendUrl = null;
              // $passed = 1;
              if ($passed >= 0.6){
                  if (Auth::user() != null) {

                      try {
                          DB::beginTransaction();

                          DB::table('lms_test_result')->insert([
                              'lmscontent_id' => $content_id,
                              'combo_slug' => $combo_slug,
                              'finish' => 1,
                              'total_point' => $point,
                              'users_id' => Auth::id(),
                              'point' => $totalValue,
                              'time_result' => $time,
                              'created_by' => Auth::id(),

                          ]);

                          DB::table('lms_student_view')
                              ->where('users_id', Auth::id())
                              ->where('lmscontent_id', $content_id)
                              ->update([
                                  'finish' => 1,
                              ]);
                          DB::commit();
                      } catch (Exception $e) {
                          DB::rollBack();
                      }

                  }
                $record =DB::table('lmscontents')
                ->where('id',(int) $request->slug)
                ->select('stt','lmsseries_id')
                ->get();


                if (!$record->isEmpty()){
                  $recordurl = DB::table('lmscontents')
                  ->where('stt','>=',((int)$record[0]->stt +1))
                  ->where('lmsseries_id',$record[0]->lmsseries_id)
                  ->whereNotIn('type',[0,8])
                  ->select('id','type')
                  ->first();


                  if (!empty($recordurl)){
                    switch ($recordurl->type) {
                      case 1:
                      case 2:
                      case 6:
                      $sendUrl = PREFIX.'learning-management/lesson/show/'.$combo_slug.'/'.$request->series.'/'.$recordurl->id;
                      break;
                      case 3:
                      case 4:
                      $sendUrl = PREFIX.'learning-management/lesson/exercise/'.$combo_slug.'/'.$request->series.'/'.$recordurl->id;
                      break;
                      case 5:
                      $sendUrl = PREFIX.'learning-management/lesson/audit/'.$combo_slug.'/'.$request->series.'/'.$recordurl->id;
                      break;
                      default:
                      $sendUrl = null;
                      break;
                    }

                  }

                }
              }

            $data['name'] = DB::table('lmscontents')
                ->select('bai')
                ->where('lmscontents.id',$slug)
                ->first();

              $data['class']              = 'exams';
              $data['title']              = 'Khóa học Tổng hợp - Luyện thi N4';
              $data['series']             = $series;
              $data['slug']                =$slug;
              $data['records']            = $records;
              $data['value']              = $totalValue;
              $data['point']              = $point;
            $data['combo_slug']                =$combo_slug;
                $data['back_url']           = $sendUrl;
              $data['passed']             = $passed;
              $data['layout']              = 'layouts.exercise.exerciselayout';
              $view_name = getTheme().'::student.exercise.student-audit';

              return view($view_name, $data);

            }catch (Exception $e){

              return back();
            }

          }
        }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */

        public function lms_test_log(Request $request){
          if($request->ajax()){



            $content_id = $request->content_id;
            $time = $request->time;
            $dataQuest = $request->all();
            unset($dataQuest['_token']);
            unset($dataQuest['content_id']);
            unset($dataQuest['time']);


            $dataResult= array();

            foreach ($dataQuest as $key => $value){
              $idKey = filter_var(str_replace('quest_','',$key),FILTER_SANITIZE_NUMBER_INT);

              $dataResult[$idKey] = (int)$value;
            }

              if (Auth::user() != null){
                  try
                  {
                      DB::beginTransaction();
                      DB::table('lms_test_log')
                          ->updateOrInsert(
                              [
                                  'lmscontent_id' => $content_id,
                                  'users_id'       => Auth::id(),
                              ],
                              [
                                  'lmscontent_id' => $content_id,
                                  'users_id'       => Auth::id(),
                                  'time'  => $time,
                                  'result' => json_encode($dataResult),
                              ]

                          );

                      DB::commit();
                  }catch (Exception $e) {
                      DB::rollBack();
                  }
              }

          }
        }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */

        public function isValidRecord($record){
          if ($record === null) {
            flash('Ooops...!', getPhrase("page_not_found"), 'error');
            return $this->getRedirectUrl();
          }
          return FALSE;
        }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */

        public function getReturnUrl(){
          return URL_LMS_CONTENT;
        }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */

        public function dequy_showLesson_selected($data){
          $records = $data['content_q'];
          $content_view = $data['content_view'];
          $lesson_id = $data['stt'];
          $slug = $data['slug'];
          $parent_id = $data['parent_id'];
          $result = [];
          $array_video = ['1','2','6'];
          $array_loop = ['1','2','3','4','6','7'];
          $is_loop = false;
          foreach ($records as $key => $r){
            if ($r->parent_id == $parent_id){
        # pre check
              $class_color = '#2a93e2' ;
              $class_i_color = '#2a93e2' ;

              $is_active = ($r->id == $lesson_id) ? 'lesson_active' : null;

              if(in_array($r->type, $array_video)){
                $i_tag = '<i class="fa fa-play" aria-hidden="true"></i>';
                $video_url = PREFIX."learning-management/lesson-selected/show/$slug/".$r->id;
              }else{
                $i_tag = '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
                $video_url = PREFIX."learning-management/lesson/exercise/$slug/".$r->id;
              }
              if($r->type == 5){
                $i_tag = '<i class="fa fa-star"></i>';
                $video_url = PREFIX.'learning-management/lesson/audit/'.$slug.'/'.$r->id;
              }
              $i_viewed = '<i class="fa fa-check" style="color: green !important"></i>';
              if($r->type == 8){
                $i_tag = '<i class="fa fa-angle-double-right" aria-hidden="true"></i>';
              }
              if($r->id == $lesson_id){
                if(!in_array($r->type,$array_video)){
                  return redirect(PREFIX."learning-management/lesson-selected/show/$slug/".$pre_lesson);
                }
              }
        # end pre check

              if($r->parent_id == null){
                $i_tag = ($r->type == 5) ? $i_tag : '<i class="fa fa-book" aria-hidden="true"></i>';
                $video_url = ($r->type == 5) ? $video_url : '#';
                $result[$r->id]['tag'] = '
                <li>
                <h3>
                <a href="'.$video_url.'">
                '.$i_tag.'
                '.$r->bai.' '.$r->title.'
                </a>
                </h3><ul>
                ';
                $result[$r->id]['level'] = '0';
                $result[$r->id]['type'] = $r->type;
              }else{
                if($r->type == '8'){
                  $result[$r->id]['tag'] = '<li>
                  <a href="#" style="color: '.$class_color.'">'.$i_tag.$r->bai.'</a><ul>
                  ';
                  $result[$r->id]['level'] = '1';
                  $result[$r->id]['type'] = $r->type;
                }else{
                  $result[$r->id]['tag'] = '
                  <li class="'.$is_active.'">
                  <a href="'.$video_url.'" style="color: '.$class_color.'">'
                  .$i_tag.$r->bai.'
                  </a>
                  </li>
                  ';
                  $result[$r->id]['level'] = '2';
                  $result[$r->id]['type'] = $r->type;
                }
              }

              unset($records[$key]);
              $child = $this->dequy_showLesson_selected(['content_q' => $records, 'content_view' => $content_view,'stt' => $lesson_id, 'slug' => $slug, 'parent_id' => $r->id]);
              $result = $result + $child;
            }
          }
          return $result;
        }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */

        public function showLessonSelected($slug = '',$lesson_id = ''){
          $data['active_class']       = 'exams';
          $data['title']              = 'Khóa luyện thi';
          $data['series']             = false;
          $data['layout']              = getLayout();
          $view_name = getTheme().'::student.lms.show-lesson-selected';
          $data['current_series'] = DB::table('lmsseries')
          ->select('title')
          ->where([
            ['slug',$slug],
          ])->get()->first();

          if($slug == ''){
            flash('Ooops...!', getPhrase("page_not_found"), 'error');
            return back();
          }

          $data['url_categories'] = PREFIX.'lms/exam-categories/list';

          $parent_q = DB::table('lms_class_series_data')
          ->select('lmscontents.*')
          ->join('lmscontents','lmscontents.id','=','lms_class_series_data.content_id')
          ->join('lms_class_series','lms_class_series.id','=','lms_class_series_data.class_series_id')
          ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
          ->join('classes','classes.id','=','lms_class_series.class_id')
          ->join('classes_user','classes.id','=','classes_user.classes_id')
          ->where([
            ['lmsseries.slug',$slug],
            ['lmscontents.delete_status',0],
            ['lmsseries.delete_status',0],
            ['classes_user.student_id',Auth::id()],
            // ['lms_class_series_data.show_status',1]
          ])
          ->get();

          $list_parent = [];
          $content_q = [];
          $array_video = ['1','2','6'];

       

          foreach($parent_q as $r){
            
            $list_parent[] = $r->id;
          }

          if($list_parent != []){
            $full_content = DB::table('lmscontents')
            ->select('lmscontents.*')
            ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
            ->where([
              ['lmsseries.slug',$slug],
              ['lmscontents.delete_status',0]
            ])
            ->orderBy('stt')
            ->get();
            
            foreach($full_content as $r){
              if(in_array($r->id, $list_parent) || in_array($r->parent_id, $list_parent)){
                $content_q[] = $r;
                $list_parent[] = $r->id;
                if(!isset($content_view[0]) && in_array($r->type, $array_video)){
                  $content_view[0] = $r;
                }
              }
            }
          }else{
            // dd('none list parent');
            flash('Ooops...!', getPhrase("page_not_found"), 'error');
            return back();
          }

# check empty content
          if($content_q == []){
            // dd('content empty');
            flash('Ooops...!', getPhrase("page_not_found"), 'error');
            return back();
          }
         
# check if come to page from series
          if($lesson_id == ''){
            $lesson_id = $content_view[0]->id;
// dump($lesson_id);
          }
          $data['viewed_video'] = true;
# get lesson
          $lesson = [];
          $i_parent = 0;
          $data['current_lesson'] = '';
          $array_loop = ['1','2','3','4','6','7'];
          foreach($content_q as $r){
            if($r->id < $lesson_id && in_array($r->type, $array_video)){
              $pre_lesson = $r->id;
            }

            if($r->id == $lesson_id){
              if(!in_array($r->type,$array_video)){
                return redirect(PREFIX."learning-management/lesson/show/$slug/".$pre_lesson);
              }
              $class_color = '#e62020';
              $data['current_lesson'] = $r->bai .': '. $r->title;
              $data['current_description'] = $r->description;
              $data['current_video'] = $r->file_path;
              $data['current_poster'] = IMAGE_PATH_UPLOAD_LMS_CONTENTS. $r->image;
              $data['contentslug'] = $r->id;
              $data['url_excer'] = PREFIX.'learning-management/lesson/exercise/'.$slug.'/'.$r->id;
            }
          }
          $new = $this->dequy_showLesson_selected(['content_q' => $content_q, 'content_view' => $content_view,'stt' => $lesson_id, 'slug' => $slug, 'parent_id' => null]);

          $lesson = '';
          $is_loop = false;

          $i = 0;
          foreach($new as $r){
            if(!in_array($r['type'], $array_loop) && $is_loop === true){
              $is_loop = 'end';
            }
            if($r['level'] == '0' && $i > 0){
              $lesson .= '</ul>';
            }
            if($is_loop === 'end'){
              $lesson .= '</ul></li>';
              $is_loop = false;
            }
            if($r['type'] != 8){
              $lesson .= $r['tag'];
            }elseif ($r['type'] == 8) {
              $lesson .= $r['tag'];
              $is_loop = true;
            }

            $i++;
          }

          $data['lesson_menu'] = $lesson;

          return view($view_name, $data);
        }

    /**
     * This method displays the list of series available
     * @return [type] [description]
     */

    public function ajaxcheckout(Request $request) {
        $data = null;
        $user = getUserRecord();
        $item = LmsSeries::where('id', '=', $request->item)->first();
        $user_record = User::find($user->id);

        if ($item->cost <= $user_record->point) {

            $data = array('error'=>0);
        } else {
            $data = array('error'=>1, 'message'=>'Bạn không đủ số Hi Koi, vui lòng nạp thêm.');
        }
        return json_encode($data);
    }


        public function razorpaySuccess(Request $request) {
            $data = $request->all();
            $user = getUserRecord();
            $item = LmsSeries::where('id', '=', $request->item)->first();
            $user_record = User::find($user->id);


            try {
                DB::beginTransaction();

                if ($item->cost <= $user_record->point) {

                    $payment                  = new Payment();
                    $payment->item_id         = $request->item;
                    $payment->item_name       = $item->title;
                    $payment->plan_type       = 'lms';
                    // $payment->payment_gateway = $payment_method;
                    // $payment->slug            = $payment::makeSlug(getHashCode());
                    $payment->cost            = $item->cost;
                    $payment->user_id         = $user->id;
                    // $payment->paid_by_parent  = $other_details['paid_by_parent'];
                    $payment->payment_status  = 'success';
                    // $payment->other_details   = json_encode($other_details);
                    $payment->save();

                    $user_record->point = $user->point - $item->cost ;
                    $user_record->save();
                    $data = array('error'=>0,'message' =>'');
                } else {
                    $data = array('error'=>1, 'message'=>'Bạn không đủ số Hi Koi, vui lòng nạp thêm.');
                }


                DB::commit();

                return json_encode($data);

            }catch (Exception $e) {
                DB::rollBack();
                $data = array('error'=>2, 'message'=>'Giao dịch thất bại');
                return json_encode($data);
            }
        }




    public function studentExercises(Request $request,$combo_slug = '',$slug = '',$stt = ''){

        $data['layout']              = getLayout();
        $view_name = getTheme().'::student.exercise.student-exercise';

        try {

            if($slug == ''){
                flash('Ooops...!', getPhrase("page_not_found"), 'error');
                return back();
            }

            // get Exercise
            if (Auth::check() && Auth::user()->role_id != 6){


                $check_hocthu  = DB::table('lmscontents')
                    ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                    ->where([
                        ['lmsseries.delete_status',0],
                        ['lmscontents.id',$stt],
                        ['lmscontents.delete_status',0],
                        ['lmscontents.el_try',1]
                    ])
                    ->distinct()
                    ->count();

                if ($check_hocthu == 0){
                    $check = DB::table('lms_student_view')
                        ->select('id')
                        ->where('lmscontent_id',$stt)
                        ->where('users_id',Auth::id())
                        ->get();

                    if ($check->isEmpty()){
                        return redirect('home');
                    }

                    $data['hi_combo'] = DB::table('lmsseries_combo')
                        ->where('slug',$combo_slug)
                        ->where('delete_status',0)
                        ->get()->first();

                    $data['checkpay'] = DB::table('payment_method')
                        ->select(DB::raw("(SELECT COUNT(id)  FROM payment_method WHERE payment_method.item_id 
                  = ".$data['hi_combo']->id."  AND payment_method.user_id = ".Auth::id()."     AND 
                  DATE_ADD(responseTime, INTERVAL IF(".$data['hi_combo']->time." = 0,90,IF(".$data['hi_combo']->time." = 1,180,365)) DAY) > NOW()) as payment"))
                        ->distinct()
                        ->get()->first();

                    if ($data['checkpay']->payment == 0){
                        return redirect('home');
                    }
                }


            }

            $records = DB::table('lmscontents')
                ->join('lms_exams','lms_exams.content_id','=','lmscontents.id')
                ->where('lmscontents.id',$stt)
                ->where('lmscontents.delete_status',0)
                ->where('lms_exams.delete_status',0)
                //->where('lms_exams.dang',5)
                ->whereNotNull('lms_exams.dang')
                ->select('lms_exams.id','label','dang','cau','mota','dapan',DB::raw("CONCAT_WS('-,-',luachon1,luachon2,luachon3,luachon4) AS answers") )
                ->get();

            if (!$records->isEmpty()){
                foreach ($records as $key => $value) {
                    //$records[$key]->answers_furigana = explode(',', change_furigana(trim($value->answers,'return')));
                    $records[$key]->mota = change_furigana( mb_convert_encoding(str_replace('＿＿','__',$value->mota),"UTF-8","auto"),'return');
                    $records[$key]->answers = explode('-,-', trim($value->answers));

                }


                foreach ($records as $key => $record) {
                    $valueAnswers = array();
                    foreach ($record->answers as $answer){
                        $valueAnswers[] = change_furigana( $answer,'return');
                    }
                    $records[$key]->answers = $valueAnswers;

                }


            }


            // get right menu


            $data['current_series'] = DB::table('lmsseries')
                ->select('title')
                ->where([
                    ['slug',$slug],
                ])->get()->first();



            if (Auth::check()){
                $data['total_course'] = DB::table('lmscontents')
                    ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                    ->where([
                        ['lmsseries.delete_status',0],
                        ['lmsseries.slug',$slug],
                        ['lmscontents.delete_status',0],
                    ])
                    ->whereNotIn('lmscontents.type', [0,8])
                    ->select('lmscontents.id')
                    ->distinct()
                    ->get()->count();




                $data['current_course'] = DB::table('lms_student_view')
                    ->join('lmscontents','lmscontents.id','=','lms_student_view.lmscontent_id')
                    ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                    ->where([
                        ['users_id',Auth::id()],
                        ['lmsseries.slug',$slug],
                        ['lmscontents.delete_status',0],
                        ['lmsseries.delete_status',0],
                    ])
                    ->whereNotIn('lmscontents.type', [0,8])
                    ->select('lms_student_view.lmscontent_id')
                    ->distinct('lms_student_view.lmscontent_id')
                    ->get()->count();


            }

            $data['hi_combo'] = DB::table('lmsseries_combo')
                ->where('slug',$combo_slug)
                ->where('delete_status',0)
                ->get()->first();


            if (!Auth::check()){

                $data['url_categories'] = PREFIX.'lms/exam-categories/list';

                $content_q = DB::table('lmscontents')
                    ->select('lmscontents.*')
                    ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                    ->where([
                        ['lmsseries.slug',$slug],
                        ['lmscontents.delete_status',0]
                    ])
                    ->orderBy('stt','asc')
                    ->get();

                // dd($content_q);

                # check empty content
                if($content_q->isEmpty()){
                    // dd('empty content');
                    flash('Ooops...!', getPhrase("page_not_found"), 'error');
                    return back();
                }


                $check_hocthu  = DB::table('lmscontents')
                    ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                    ->where([
                        ['lmsseries.delete_status',0],
                        ['lmsseries.slug',$slug],
                        ['lmscontents.delete_status',0],
                        ['lmscontents.el_try',1]
                    ])
                    ->distinct()
                    ->count();

                if ($check_hocthu == 0){
                    flash('Yêu cầu sở hữu khóa học', 'Vui lòng sở hữu khóa học để xem nội này', 'error');
                    return redirect('/home');
                }

                $data['hi_koi'] = DB::table('lmsseries')->select('lmsseries.*')  ->where([
                    ['lmsseries.slug',$slug],
                ]) ->get()->first();

                # check viewd lesson + time view
                $content_view = DB::table('lmscontents')
                    ->select(['lmscontents.*'])
                    ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                    ->where([
                        ['lmsseries.slug',$slug],
                        ['lmscontents.delete_status',0]
                    ])
                    ->whereNotIn('type',['0','8'])
                    ->orderBy('stt','asc')
                    ->get();



                if ($stt == ''){
                    $stt = $content_view[0]->id;
                }
                $cur_stt = $stt;
                $data['current_lesson'] = '';
                $array_video = ['1','2','6','9'];
                $array_loop = ['1','2','3','4','6','7'];
                foreach($content_q as $r){
                    if($r->id < $stt && in_array($r->type, $array_video)){
                        $pre_lesson = $r->id;
                    }

                    if($r->id == $cur_stt){
                        if(!in_array($r->type,$array_video)){
                            return redirect(PREFIX."learning-management/lesson/show/$slug/".$pre_lesson);
                        }
                        $class_color = '#e62020';
                        $data['current_lesson'] = ($r->title == null) ? $r->bai : $r->bai .': '. $r->title;
                        $data['current_description'] = $r->description;
                        $data['current_video'] = ($r->el_try == 1 ? $r->file_path : null );
                        $data['current_poster'] = IMAGE_PATH_UPLOAD_LMS_CONTENTS. $r->image;
                        $data['contentslug'] = $r->id;
                        $data['url_excer'] = PREFIX.'learning-management/lesson/exercise/'.$slug.'/'.$r->id;
                        //dump($r->el_try);
                    }
                }


                $new = $this->dequy_tryshowLesson(['content_q' => $content_q,
                    'content_view' => $content_view,'stt' => $stt, 'slug' => $slug, 'parent_id' => null,'combo_slug' => $combo_slug,]);

                $lesson = '';
                $is_loop = false;

                $i = 0;
                foreach($new as $r){
                    if(!in_array($r['type'], $array_loop) && $is_loop === true){
                        $is_loop = 'end';
                    }
                    if($r['level'] == '0' && $i > 0){
                        $lesson .= '</ul>';
                    }
                    if($is_loop === 'end'){
                        $lesson .= '</ul></li>';
                        $is_loop = false;
                    }
                    if($r['type'] != 8){
                        $lesson .= $r['tag'];
                    }elseif ($r['type'] == 8) {
                        $lesson .= $r['tag'];
                        $is_loop = true;
                    }

                    $i++;
                }

                $data['lesson_menu'] = $lesson;
                //die();
            }else{
                if(Auth::user()->role_id == 6){
                    $_00content = DB::table('lmscontents')
                        ->select('lmscontents.*')
                        ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                        ->where([
                            ['lmsseries.slug',$slug],
                        ])
                        ->whereNotIn('type',['0','8'])
                        ->get();

                    foreach($_00content as $r){
                        $x = DB::table('lms_student_view')
                            ->updateOrInsert(
                                [
                                    'lmscontent_id' => $r->id,
                                    'users_id'      => Auth::id(),
                                ],
                                [
                                    'finish'        => 1,
                                    'type'          => $r->type,
                                ]
                            );
                    }
                }

                $data['url_categories'] = PREFIX.'lms/exam-categories/list';

                $content_q = DB::table('lmscontents')
                    ->select('lmscontents.*')
                    ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                    ->where([
                        ['lmsseries.slug',$slug],
                        ['lmscontents.delete_status',0]
                    ])
                    ->get();


                # check empty content
                if($content_q->isEmpty()){
                    // dd('empty content');
                    flash('Ooops...!', getPhrase("page_not_found"), 'error');
                    return back();
                }

                $data['hi_koi'] = DB::table('lmsseries')->select('lmsseries.*')  ->where([
                    ['lmsseries.slug',$slug],
                ]) ->get()->first();


                $data['checkpay'] = DB::table('payment_method')
                    ->select(DB::raw("(SELECT COUNT(id)  FROM payment_method WHERE payment_method.item_id 
                  = ".$data['hi_combo']->id."  AND payment_method.user_id = ".Auth::id()."  AND payment_method.status = 1 
                     AND  DATE_ADD(responseTime, INTERVAL IF(".$data['hi_combo']->time." = 0,90,IF(".$data['hi_combo']->time." = 1,180,365)) DAY) > NOW()) as payment"))
                    ->distinct()
                    ->get()->first();


                if ($data['hi_combo']->cost == 0  || $data['checkpay']->payment > 0 || Auth::user()->role_id == 6){

                    # check viewd lesson + time view
                    $content_view = DB::table('lms_student_view')
                        ->select(['lms_student_view.*','lmscontents.stt','lmscontents.slug'])
                        ->join('lmscontents','lmscontents.id','=','lms_student_view.lmscontent_id')
                        ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                        ->where([
                            ['users_id',Auth::id()],
                            ['lmsseries.slug',$slug],
                            ['finish',0],
                            ['lmscontents.delete_status',0]
                        ])
                        ->orderBy('lmscontent_id','desc')
                        ->get();


                    # check first view or next lesson view
                    if($content_view->isEmpty()){
                        # next lesson view
                        $viewed_content = DB::table('lms_student_view')
                            ->select(['lms_student_view.*','lmscontents.stt','lmscontents.slug'])
                            ->join('lmscontents','lmscontents.id','=','lms_student_view.lmscontent_id')
                            ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                            ->where([
                                ['users_id',Auth::id()],
                                ['lmsseries.slug',$slug],
                                ['finish',1],
                                ['lmscontents.delete_status',0]
                            ])
                            ->orderBy('stt','desc')
                            ->get();
                        if($viewed_content->isEmpty()){
                            # check first view
                            $id_content = DB::table('lmscontents')
                                ->select('lmscontents.*')
                                ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                                ->where([
                                    ['lmsseries.slug',$slug],
                                    ['lmscontents.delete_status',0],
                                ])
                                ->whereNotIn('type',['0','8'])
                                ->get()->first();
                        }else{
                            # next lesson
                            $id_content = DB::table('lmscontents')
                                ->select('lmscontents.*')
                                ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                                ->where([
                                    ['lmsseries.slug',$slug],
                                    ['lmscontents.stt','>',$viewed_content[0]->stt],
                                    ['lmscontents.delete_status',0],
                                ])
                                ->whereNotIn('type',['0','8'])
                                ->get()->first();
                        }
                        if($id_content != null){
                            $new_id = DB::table('lms_student_view')
                                ->insertGetId([
                                    'lmscontent_id'       => $id_content->id,
                                    'users_id'            => Auth::id(),
                                    'view_time'           => 0,
                                    'finish'              => 0,
                                    'type'                => $id_content->type,
                                ]);
                            $content_view = DB::table('lms_student_view')
                                ->select(['lms_student_view.*','lmscontents.stt','lmscontents.slug'])
                                ->join('lmscontents','lmscontents.id','=','lms_student_view.lmscontent_id')
                                ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                                ->where([
                                    ['users_id',Auth::id()],
                                    ['lmsseries.slug',$slug],
                                    ['finish',0],
                                    ['lmscontents.delete_status',0]
                                ])
                                ->orderBy('lmscontent_id','desc')
                                ->get();
                        }else{
                            $content_view = DB::table('lms_student_view')
                                ->select(['lms_student_view.*','lmscontents.stt','lmscontents.slug'])
                                ->join('lmscontents','lmscontents.id','=','lms_student_view.lmscontent_id')
                                ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                                ->where([
                                    ['users_id',Auth::id()],
                                    ['lmsseries.slug',$slug],
                                    ['finish',1],
                                    ['lmscontents.delete_status',0]
                                ])
                                ->orderBy('lmscontent_id','desc')
                                ->get();
                        }

                    }else{
                        $data['current_time'] = $content_view[0]->view_time;
                    }
                    // dump($content_view);
                    # check if come to page from series
                    $pre_stt = $stt;
                    if($stt == ''){
                        $stt = $content_view[0]->lmscontent_id;
                        // dump($stt);
                    }else{


                        $check_view = DB::table('lms_student_view')
                            ->select('finish')
                            ->join('lmscontents','lmscontents.id','=','lms_student_view.lmscontent_id')
                            ->where([
                                ['users_id',Auth::id()],
                                ['lmscontents.id',$stt],
                                ['lmscontents.delete_status',0]
                            ])
                            ->get();

                        if($check_view->isEmpty()){

                            flash('Ooops...!', getPhrase("page_not_found"), 'error');
                            return back();
                        }else{
                            if($check_view[0]->finish == '1'){
                                $data['viewed_video'] = true;
                                unset($data['current_time']);
                            }
                        }
                    }
                    # get lesson
                    $check_end_view = DB::table('lmscontents')
                        ->select(['lmscontents.stt','lmscontents.id'])
                        ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                        ->where([
                            ['lmsseries.slug',$slug],
                            ['lmscontents.delete_status',0],
                        ])
                        ->whereNotIn('type',['0','8'])
                        ->orderBy('stt','desc')
                        ->get()->first();

                    $cur_stt = $stt;
                    if($check_end_view->id != null){
                        if($stt == $check_end_view->id && $pre_stt == ''){
                            $check_first_view = DB::table('lmscontents')
                                ->select(['lmscontents.stt','lmscontents.id'])
                                ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                                ->where([
                                    ['lmsseries.slug',$slug],
                                    ['lmscontents.delete_status',0],
                                ])
                                ->whereNotIn('type',['0','8'])
                                ->orderBy('stt','asc')
                                ->get()->first();
                            $cur_stt = $check_first_view->id;
                        }
                    }

                    $lesson = [];
                    $i_parent = 0;
                    $data['current_lesson'] = '';
                    $array_video = ['1','2','6','9'];
                    $array_loop = ['1','2','3','4','6','7'];
                    foreach($content_q as $r){
                        if($r->id < $stt && in_array($r->type, $array_video)){
                            $pre_lesson = $r->id;
                        }

                        if($r->id == $cur_stt){
                            /*if(!in_array($r->type,$array_video)){
                                return redirect(PREFIX."learning-management/lesson/show/$combo_slug/$slug/".$pre_lesson);
                            }*/
                            $class_color = '#e62020';
                            $data['current_lesson'] = ($r->title == null) ? $r->bai : $r->bai .': '. $r->title;
                            $data['current_description'] = $r->description;
                            $data['current_video'] = $r->file_path;
                            $data['current_poster'] = IMAGE_PATH_UPLOAD_LMS_CONTENTS. $r->image;
                            $data['contentslug'] = $r->id;
                            $data['url_excer'] = PREFIX.'learning-management/lesson/exercise/'.$slug.'/'.$r->id;
                        }
                    }
                    $new = $this->dequy_showLesson(['content_q' => $content_q, 'content_view' => $content_view,
                        'stt' => $cur_stt, 'slug' => $slug, 'parent_id' => null,'combo_slug' => $combo_slug,]);

                    $lesson = '';
                    $is_loop = false;

                    $i = 0;
                    foreach($new as $r){
                        if(!in_array($r['type'], $array_loop) && $is_loop === true){
                            $is_loop = 'end';
                        }
                        if($r['level'] == '0' && $i > 0){
                            $lesson .= '</ul>';
                        }
                        if($is_loop === 'end'){
                            $lesson .= '</ul></li>';
                            $is_loop = false;
                        }
                        if($r['type'] != 8){
                            $lesson .= $r['tag'];
                        }elseif ($r['type'] == 8) {
                            $lesson .= $r['tag'];
                            $is_loop = true;
                        }

                        $i++;
                    }

                }else{



                    $check_hocthu  = DB::table('lmscontents')
                        ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                        ->where([
                            ['lmsseries.delete_status',0],
                            ['lmsseries.slug',$slug],
                            ['lmscontents.delete_status',0],
                            ['lmscontents.el_try',1]
                        ])
                        ->distinct()
                        ->count();

                    if ($check_hocthu == 0){
                        flash('Yêu cầu sở hữu khóa học', 'Vui lòng sở hữu khóa học để xem nội này', 'error');
                        return redirect('/payments/lms/'.$combo_slug);
                    }


                    # check viewd lesson + time view
                    $content_view = DB::table('lmscontents')
                        ->select(['lmscontents.*'])
                        ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                        ->where([
                            ['lmsseries.slug',$slug],
                            ['lmscontents.delete_status',0]
                        ])
                        ->whereNotIn('type',['0','8'])
                        ->orderBy('stt','asc')
                        ->get();



                    if ($stt == ''){
                        $stt = $content_view[0]->id;
                    }
                    $cur_stt = $stt;
                    $data['current_lesson'] = '';
                    $array_video = ['1','2','6','9'];
                    $array_loop = ['1','2','3','4','6','7'];
                    foreach($content_q as $r){
                        if($r->id < $stt && in_array($r->type, $array_video)){
                            $pre_lesson = $r->id;
                        }

                        if($r->id == $cur_stt){
                            /*if(!in_array($r->type,$array_video)){
                                return redirect(PREFIX."learning-management/lesson/show/$combo_slug/$slug/".$pre_lesson);
                            }*/
                            $class_color = '#e62020';
                            $data['current_lesson'] = ($r->title == null) ? $r->bai : $r->bai .': '. $r->title;
                            $data['current_description'] = $r->description;
                            $data['current_video'] = ($r->el_try == 1 ? $r->file_path : null );
                            $data['current_poster'] = IMAGE_PATH_UPLOAD_LMS_CONTENTS. $r->image;
                            $data['contentslug'] = $r->id;
                            $data['url_excer'] = PREFIX.'learning-management/lesson/exercise/'.$slug.'/'.$r->id;
                        }
                    }


                    $new = $this->dequy_tryshowLesson(['content_q' => $content_q,
                        'content_view' => $content_view,'stt' => $stt, 'slug' => $slug, 'parent_id' => null,'combo_slug' => $combo_slug,]);

                    $lesson = '';
                    $is_loop = false;

                    $i = 0;
                    foreach($new as $r){
                        if(!in_array($r['type'], $array_loop) && $is_loop === true){
                            $is_loop = 'end';
                        }
                        if($r['level'] == '0' && $i > 0){
                            $lesson .= '</ul>';
                        }
                        if($is_loop === 'end'){
                            $lesson .= '</ul></li>';
                            $is_loop = false;
                        }
                        if($r['type'] != 8){
                            $lesson .= $r['tag'];
                        }elseif ($r['type'] == 8) {
                            $lesson .= $r['tag'];
                            $is_loop = true;
                        }

                        $i++;
                    }
                }


                $data['lesson_menu'] = $lesson;

            }


            // get commments
            $data['comment'] = DB::table('comments')
                ->where([
                    ['user_id',Auth::id()],
                    ['lmsseries_id',$data['hi_koi']->id],
                    ['lmscombo_id',$data['hi_combo']->id],
                    ['lmscontent_id',$stt],
                    ['parent_id',0],
                ])
                ->get();

            $data['comment_child'] = DB::table('comments')
                ->where([
                    ['user_id',Auth::id()],
                    ['lmsseries_id',$data['hi_koi']->id],
                    ['lmscombo_id',$data['hi_combo']->id],
                    ['lmscontent_id',$stt],
                    ['parent_id','!=',0],
                ])

                ->get();

            try {
                DB::table('comments')
                    ->where([
                        ['user_id',Auth::id()],
                        ['lmsseries_id',$data['hi_koi']->id],
                        ['lmscombo_id',$data['hi_combo']->id],
                        ['lmscontent_id',$stt],
                        ['parent_id',0],
                    ])
                    ->update(
                        [
                            'status' => 2,
                            'updated_at' =>date("Y-m-d H:i:s"),
                        ]
                    );
            }catch(Exception $e){
            }

        }catch (\Exception $e) {

            return $e->getMessage();
        }





        $data['class']       = 'exams';
        $data['title']              = 'Khóa học Tổng hợp - Luyện thi N4';
        $data['series']             = $slug;;
        $data['slug']                = $stt;
        $data['combo_slug']          = $combo_slug;
        $data['records']            = $records;
        $data['count_records']      = count($records);
       /* $data['back_url']            = $sendUrl;*/
        /*$data['sendUrl']        = $sendUrl;*/
        $data['active_class']       = 'exams';
        //$data['layout']              = 'layouts.exercise.exerciselayout';
        $data['layout']              = getLayout();
        //dd($data['layout']);
        $view_name = getTheme().'::student.exercise.student-exercise';

        return view($view_name, $data);
    }

    public function flashCard(Request $request,$combo_slug = '',$slug = '',$stt = ''){

        $data['layout']              = getLayout();
        $view_name = getTheme().'::student.exercise.student-exercise';

        try {

            if($slug == ''){
                flash('Ooops...!', getPhrase("page_not_found"), 'error');
                return back();
            }

            // get Exercise
            if (Auth::check() && Auth::user()->role_id != 6){


                $check_hocthu  = DB::table('lmscontents')
                    ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                    ->where([
                        ['lmsseries.delete_status',0],
                        ['lmscontents.id',$stt],
                        ['lmscontents.delete_status',0],
                        ['lmscontents.el_try',1]
                    ])
                    ->distinct()
                    ->count();

                if ($check_hocthu == 0){
                    $check = DB::table('lms_student_view')
                        ->select('id')
                        ->where('lmscontent_id',$stt)
                        ->where('users_id',Auth::id())
                        ->get();

                    if ($check->isEmpty()){
                        return redirect('home');
                    }

                    $data['hi_combo'] = DB::table('lmsseries_combo')
                        ->where('slug',$combo_slug)
                        ->where('delete_status',0)
                        ->get()->first();

                    $data['checkpay'] = DB::table('payment_method')
                        ->select(DB::raw("(SELECT COUNT(id)  FROM payment_method WHERE payment_method.item_id 
                  = ".$data['hi_combo']->id."  AND payment_method.user_id = ".Auth::id()."     AND 
                  DATE_ADD(responseTime, INTERVAL IF(".$data['hi_combo']->time." = 0,90,IF(".$data['hi_combo']->time." = 1,180,365)) DAY) > NOW()) as payment"))
                        ->distinct()
                        ->get()->first();

                    if ($data['checkpay']->payment == 0){
                        return redirect('home');
                    }
                }


            }

            $records = DB::table('lms_flashcard')
                ->join('lms_flashcard_detail','lms_flashcard.id','=','lms_flashcard_detail.flashcard_id')
                ->where('lms_flashcard.id',1)
                ->select('lms_flashcard_detail.*' )
                ->orderBy('lms_flashcard_detail.id')
                ->get();




            // get right menu


            $data['current_series'] = DB::table('lmsseries')
                ->select('title')
                ->where([
                    ['slug',$slug],
                ])->get()->first();



            if (Auth::check()){
                $data['total_course'] = DB::table('lmscontents')
                    ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                    ->where([
                        ['lmsseries.delete_status',0],
                        ['lmsseries.slug',$slug],
                        ['lmscontents.delete_status',0],
                    ])
                    ->whereNotIn('lmscontents.type', [0,8])
                    ->select('lmscontents.id')
                    ->distinct()
                    ->get()->count();




                $data['current_course'] = DB::table('lms_student_view')
                    ->join('lmscontents','lmscontents.id','=','lms_student_view.lmscontent_id')
                    ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                    ->where([
                        ['users_id',Auth::id()],
                        ['lmsseries.slug',$slug],
                        ['lmscontents.delete_status',0],
                        ['lmsseries.delete_status',0],
                    ])
                    ->whereNotIn('lmscontents.type', [0,8])
                    ->select('lms_student_view.lmscontent_id')
                    ->distinct('lms_student_view.lmscontent_id')
                    ->get()->count();


            }

            $data['hi_combo'] = DB::table('lmsseries_combo')
                ->where('slug',$combo_slug)
                ->where('delete_status',0)
                ->get()->first();


            if (!Auth::check()){

                $data['url_categories'] = PREFIX.'lms/exam-categories/list';

                $content_q = DB::table('lmscontents')
                    ->select('lmscontents.*')
                    ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                    ->where([
                        ['lmsseries.slug',$slug],
                        ['lmscontents.delete_status',0]
                    ])
                    ->orderBy('stt','asc')
                    ->get();

                // dd($content_q);

                # check empty content
                if($content_q->isEmpty()){
                    // dd('empty content');
                    flash('Ooops...!', getPhrase("page_not_found"), 'error');
                    return back();
                }


                $check_hocthu  = DB::table('lmscontents')
                    ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                    ->where([
                        ['lmsseries.delete_status',0],
                        ['lmsseries.slug',$slug],
                        ['lmscontents.delete_status',0],
                        ['lmscontents.el_try',1]
                    ])
                    ->distinct()
                    ->count();

                if ($check_hocthu == 0){
                    flash('Yêu cầu sở hữu khóa học', 'Vui lòng sở hữu khóa học để xem nội này', 'error');
                    return redirect('/home');
                }

                $data['hi_koi'] = DB::table('lmsseries')->select('lmsseries.*')  ->where([
                    ['lmsseries.slug',$slug],
                ]) ->get()->first();

                # check viewd lesson + time view
                $content_view = DB::table('lmscontents')
                    ->select(['lmscontents.*'])
                    ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                    ->where([
                        ['lmsseries.slug',$slug],
                        ['lmscontents.delete_status',0]
                    ])
                    ->whereNotIn('type',['0','8'])
                    ->orderBy('stt','asc')
                    ->get();



                if ($stt == ''){
                    $stt = $content_view[0]->id;
                }
                $cur_stt = $stt;
                $data['current_lesson'] = '';
                $array_video = ['1','2','6','9'];
                $array_loop = ['1','2','3','4','6','7'];
                foreach($content_q as $r){
                    if($r->id < $stt && in_array($r->type, $array_video)){
                        $pre_lesson = $r->id;
                    }

                    if($r->id == $cur_stt){
                        if(!in_array($r->type,$array_video)){
                            return redirect(PREFIX."learning-management/lesson/show/$slug/".$pre_lesson);
                        }
                        $class_color = '#e62020';
                        $data['current_lesson'] = ($r->title == null) ? $r->bai : $r->bai .': '. $r->title;
                        $data['current_description'] = $r->description;
                        $data['current_video'] = ($r->el_try == 1 ? $r->file_path : null );
                        $data['current_poster'] = IMAGE_PATH_UPLOAD_LMS_CONTENTS. $r->image;
                        $data['contentslug'] = $r->id;
                        $data['url_excer'] = PREFIX.'learning-management/lesson/exercise/'.$slug.'/'.$r->id;
                        //dump($r->el_try);
                    }
                }


                $new = $this->dequy_tryshowLesson(['content_q' => $content_q,
                    'content_view' => $content_view,'stt' => $stt, 'slug' => $slug, 'parent_id' => null,'combo_slug' => $combo_slug,]);

                $lesson = '';
                $is_loop = false;

                $i = 0;
                foreach($new as $r){
                    if(!in_array($r['type'], $array_loop) && $is_loop === true){
                        $is_loop = 'end';
                    }
                    if($r['level'] == '0' && $i > 0){
                        $lesson .= '</ul>';
                    }
                    if($is_loop === 'end'){
                        $lesson .= '</ul></li>';
                        $is_loop = false;
                    }
                    if($r['type'] != 8){
                        $lesson .= $r['tag'];
                    }elseif ($r['type'] == 8) {
                        $lesson .= $r['tag'];
                        $is_loop = true;
                    }

                    $i++;
                }

                $data['lesson_menu'] = $lesson;
                //die();
            }else{
                if(Auth::user()->role_id == 6){
                    $_00content = DB::table('lmscontents')
                        ->select('lmscontents.*')
                        ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                        ->where([
                            ['lmsseries.slug',$slug],
                        ])
                        ->whereNotIn('type',['0','8'])
                        ->get();

                    foreach($_00content as $r){
                        $x = DB::table('lms_student_view')
                            ->updateOrInsert(
                                [
                                    'lmscontent_id' => $r->id,
                                    'users_id'      => Auth::id(),
                                ],
                                [
                                    'finish'        => 1,
                                    'type'          => $r->type,
                                ]
                            );
                    }
                }

                $data['url_categories'] = PREFIX.'lms/exam-categories/list';

                $content_q = DB::table('lmscontents')
                    ->select('lmscontents.*')
                    ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                    ->where([
                        ['lmsseries.slug',$slug],
                        ['lmscontents.delete_status',0]
                    ])
                    ->get();


                # check empty content
                if($content_q->isEmpty()){
                    // dd('empty content');
                    flash('Ooops...!', getPhrase("page_not_found"), 'error');
                    return back();
                }

                $data['hi_koi'] = DB::table('lmsseries')->select('lmsseries.*')  ->where([
                    ['lmsseries.slug',$slug],
                ]) ->get()->first();


                $data['checkpay'] = DB::table('payment_method')
                    ->select(DB::raw("(SELECT COUNT(id)  FROM payment_method WHERE payment_method.item_id 
                  = ".$data['hi_combo']->id."  AND payment_method.user_id = ".Auth::id()."  AND payment_method.status = 1 
                     AND  DATE_ADD(responseTime, INTERVAL IF(".$data['hi_combo']->time." = 0,90,IF(".$data['hi_combo']->time." = 1,180,365)) DAY) > NOW()) as payment"))
                    ->distinct()
                    ->get()->first();


                if ($data['hi_combo']->cost == 0  || $data['checkpay']->payment > 0 || Auth::user()->role_id == 6){

                    # check viewd lesson + time view
                    $content_view = DB::table('lms_student_view')
                        ->select(['lms_student_view.*','lmscontents.stt','lmscontents.slug'])
                        ->join('lmscontents','lmscontents.id','=','lms_student_view.lmscontent_id')
                        ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                        ->where([
                            ['users_id',Auth::id()],
                            ['lmsseries.slug',$slug],
                            ['finish',0],
                            ['lmscontents.delete_status',0]
                        ])
                        ->orderBy('lmscontent_id','desc')
                        ->get();


                    # check first view or next lesson view
                    if($content_view->isEmpty()){
                        # next lesson view
                        $viewed_content = DB::table('lms_student_view')
                            ->select(['lms_student_view.*','lmscontents.stt','lmscontents.slug'])
                            ->join('lmscontents','lmscontents.id','=','lms_student_view.lmscontent_id')
                            ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                            ->where([
                                ['users_id',Auth::id()],
                                ['lmsseries.slug',$slug],
                                ['finish',1],
                                ['lmscontents.delete_status',0]
                            ])
                            ->orderBy('stt','desc')
                            ->get();
                        if($viewed_content->isEmpty()){
                            # check first view
                            $id_content = DB::table('lmscontents')
                                ->select('lmscontents.*')
                                ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                                ->where([
                                    ['lmsseries.slug',$slug],
                                    ['lmscontents.delete_status',0],
                                ])
                                ->whereNotIn('type',['0','8'])
                                ->get()->first();
                        }else{
                            # next lesson
                            $id_content = DB::table('lmscontents')
                                ->select('lmscontents.*')
                                ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                                ->where([
                                    ['lmsseries.slug',$slug],
                                    ['lmscontents.stt','>',$viewed_content[0]->stt],
                                    ['lmscontents.delete_status',0],
                                ])
                                ->whereNotIn('type',['0','8'])
                                ->get()->first();
                        }
                        if($id_content != null){
                            $new_id = DB::table('lms_student_view')
                                ->insertGetId([
                                    'lmscontent_id'       => $id_content->id,
                                    'users_id'            => Auth::id(),
                                    'view_time'           => 0,
                                    'finish'              => 0,
                                    'type'                => $id_content->type,
                                ]);
                            $content_view = DB::table('lms_student_view')
                                ->select(['lms_student_view.*','lmscontents.stt','lmscontents.slug'])
                                ->join('lmscontents','lmscontents.id','=','lms_student_view.lmscontent_id')
                                ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                                ->where([
                                    ['users_id',Auth::id()],
                                    ['lmsseries.slug',$slug],
                                    ['finish',0],
                                    ['lmscontents.delete_status',0]
                                ])
                                ->orderBy('lmscontent_id','desc')
                                ->get();
                        }else{
                            $content_view = DB::table('lms_student_view')
                                ->select(['lms_student_view.*','lmscontents.stt','lmscontents.slug'])
                                ->join('lmscontents','lmscontents.id','=','lms_student_view.lmscontent_id')
                                ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                                ->where([
                                    ['users_id',Auth::id()],
                                    ['lmsseries.slug',$slug],
                                    ['finish',1],
                                    ['lmscontents.delete_status',0]
                                ])
                                ->orderBy('lmscontent_id','desc')
                                ->get();
                        }

                    }else{
                        $data['current_time'] = $content_view[0]->view_time;
                    }
                    // dump($content_view);
                    # check if come to page from series
                    $pre_stt = $stt;
                    if($stt == ''){
                        $stt = $content_view[0]->lmscontent_id;
                        // dump($stt);
                    }else{


                        $check_view = DB::table('lms_student_view')
                            ->select('finish')
                            ->join('lmscontents','lmscontents.id','=','lms_student_view.lmscontent_id')
                            ->where([
                                ['users_id',Auth::id()],
                                ['lmscontents.id',$stt],
                                ['lmscontents.delete_status',0]
                            ])
                            ->get();

                        if($check_view->isEmpty()){

                            flash('Ooops...!', getPhrase("page_not_found"), 'error');
                            return back();
                        }else{
                            if($check_view[0]->finish == '1'){
                                $data['viewed_video'] = true;
                                unset($data['current_time']);
                            }
                        }
                    }
                    # get lesson
                    $check_end_view = DB::table('lmscontents')
                        ->select(['lmscontents.stt','lmscontents.id'])
                        ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                        ->where([
                            ['lmsseries.slug',$slug],
                            ['lmscontents.delete_status',0],
                        ])
                        ->whereNotIn('type',['0','8'])
                        ->orderBy('stt','desc')
                        ->get()->first();

                    $cur_stt = $stt;
                    if($check_end_view->id != null){
                        if($stt == $check_end_view->id && $pre_stt == ''){
                            $check_first_view = DB::table('lmscontents')
                                ->select(['lmscontents.stt','lmscontents.id'])
                                ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                                ->where([
                                    ['lmsseries.slug',$slug],
                                    ['lmscontents.delete_status',0],
                                ])
                                ->whereNotIn('type',['0','8'])
                                ->orderBy('stt','asc')
                                ->get()->first();
                            $cur_stt = $check_first_view->id;
                        }
                    }

                    $lesson = [];
                    $i_parent = 0;
                    $data['current_lesson'] = '';
                    $array_video = ['1','2','6','9'];
                    $array_loop = ['1','2','3','4','6','7'];
                    foreach($content_q as $r){
                        if($r->id < $stt && in_array($r->type, $array_video)){
                            $pre_lesson = $r->id;
                        }

                        if($r->id == $cur_stt){
                            /*if(!in_array($r->type,$array_video)){
                                return redirect(PREFIX."learning-management/lesson/show/$combo_slug/$slug/".$pre_lesson);
                            }*/
                            $class_color = '#e62020';
                            $data['current_lesson'] = ($r->title == null) ? $r->bai : $r->bai .': '. $r->title;
                            $data['current_description'] = $r->description;
                            $data['current_video'] = $r->file_path;
                            $data['current_poster'] = IMAGE_PATH_UPLOAD_LMS_CONTENTS. $r->image;
                            $data['contentslug'] = $r->id;
                            $data['url_excer'] = PREFIX.'learning-management/lesson/exercise/'.$slug.'/'.$r->id;
                        }
                    }
                    $new = $this->dequy_showLesson(['content_q' => $content_q, 'content_view' => $content_view,
                        'stt' => $cur_stt, 'slug' => $slug, 'parent_id' => null,'combo_slug' => $combo_slug,]);

                    $lesson = '';
                    $is_loop = false;

                    $i = 0;
                    foreach($new as $r){
                        if(!in_array($r['type'], $array_loop) && $is_loop === true){
                            $is_loop = 'end';
                        }
                        if($r['level'] == '0' && $i > 0){
                            $lesson .= '</ul>';
                        }
                        if($is_loop === 'end'){
                            $lesson .= '</ul></li>';
                            $is_loop = false;
                        }
                        if($r['type'] != 8){
                            $lesson .= $r['tag'];
                        }elseif ($r['type'] == 8) {
                            $lesson .= $r['tag'];
                            $is_loop = true;
                        }

                        $i++;
                    }

                }else{



                    $check_hocthu  = DB::table('lmscontents')
                        ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                        ->where([
                            ['lmsseries.delete_status',0],
                            ['lmsseries.slug',$slug],
                            ['lmscontents.delete_status',0],
                            ['lmscontents.el_try',1]
                        ])
                        ->distinct()
                        ->count();

                    if ($check_hocthu == 0){
                        flash('Yêu cầu sở hữu khóa học', 'Vui lòng sở hữu khóa học để xem nội này', 'error');
                        return redirect('/payments/lms/'.$combo_slug);
                    }


                    # check viewd lesson + time view
                    $content_view = DB::table('lmscontents')
                        ->select(['lmscontents.*'])
                        ->join('lmsseries','lmsseries.id','=','lmscontents.lmsseries_id')
                        ->where([
                            ['lmsseries.slug',$slug],
                            ['lmscontents.delete_status',0]
                        ])
                        ->whereNotIn('type',['0','8'])
                        ->orderBy('stt','asc')
                        ->get();



                    if ($stt == ''){
                        $stt = $content_view[0]->id;
                    }
                    $cur_stt = $stt;
                    $data['current_lesson'] = '';
                    $array_video = ['1','2','6','9'];
                    $array_loop = ['1','2','3','4','6','7'];
                    foreach($content_q as $r){
                        if($r->id < $stt && in_array($r->type, $array_video)){
                            $pre_lesson = $r->id;
                        }

                        if($r->id == $cur_stt){
                            /*if(!in_array($r->type,$array_video)){
                                return redirect(PREFIX."learning-management/lesson/show/$combo_slug/$slug/".$pre_lesson);
                            }*/
                            $class_color = '#e62020';
                            $data['current_lesson'] = ($r->title == null) ? $r->bai : $r->bai .': '. $r->title;
                            $data['current_description'] = $r->description;
                            $data['current_video'] = ($r->el_try == 1 ? $r->file_path : null );
                            $data['current_poster'] = IMAGE_PATH_UPLOAD_LMS_CONTENTS. $r->image;
                            $data['contentslug'] = $r->id;
                            $data['url_excer'] = PREFIX.'learning-management/lesson/exercise/'.$slug.'/'.$r->id;
                        }
                    }


                    $new = $this->dequy_tryshowLesson(['content_q' => $content_q,
                        'content_view' => $content_view,'stt' => $stt, 'slug' => $slug, 'parent_id' => null,'combo_slug' => $combo_slug,]);

                    $lesson = '';
                    $is_loop = false;

                    $i = 0;
                    foreach($new as $r){
                        if(!in_array($r['type'], $array_loop) && $is_loop === true){
                            $is_loop = 'end';
                        }
                        if($r['level'] == '0' && $i > 0){
                            $lesson .= '</ul>';
                        }
                        if($is_loop === 'end'){
                            $lesson .= '</ul></li>';
                            $is_loop = false;
                        }
                        if($r['type'] != 8){
                            $lesson .= $r['tag'];
                        }elseif ($r['type'] == 8) {
                            $lesson .= $r['tag'];
                            $is_loop = true;
                        }

                        $i++;
                    }
                }


                $data['lesson_menu'] = $lesson;

            }


            // get commments
            $data['comment'] = DB::table('comments')
                ->where([
                    ['user_id',Auth::id()],
                    ['lmsseries_id',$data['hi_koi']->id],
                    ['lmscombo_id',$data['hi_combo']->id],
                    ['lmscontent_id',$stt],
                    ['parent_id',0],
                ])
                ->get();

            $data['comment_child'] = DB::table('comments')
                ->where([
                    ['user_id',Auth::id()],
                    ['lmsseries_id',$data['hi_koi']->id],
                    ['lmscombo_id',$data['hi_combo']->id],
                    ['lmscontent_id',$stt],
                    ['parent_id','!=',0],
                ])

                ->get();

            try {
                DB::table('comments')
                    ->where([
                        ['user_id',Auth::id()],
                        ['lmsseries_id',$data['hi_koi']->id],
                        ['lmscombo_id',$data['hi_combo']->id],
                        ['lmscontent_id',$stt],
                        ['parent_id',0],
                    ])
                    ->update(
                        [
                            'status' => 2,
                            'updated_at' =>date("Y-m-d H:i:s"),
                        ]
                    );
            }catch(Exception $e){
            }

        }catch (\Exception $e) {

            return $e->getMessage();
        }





        $data['class']       = 'exams';
        $data['title']              = 'Khóa học Tổng hợp - Luyện thi N4';
        $data['series']             = $slug;;
        $data['slug']                = $stt;
        $data['combo_slug']          = $combo_slug;
        $data['records']            = $records;
        $data['count_records']      = count($records);
        /* $data['back_url']            = $sendUrl;*/
        /*$data['sendUrl']        = $sendUrl;*/
        $data['active_class']       = 'exams';
        //$data['layout']              = 'layouts.exercise.exerciselayout';
        $data['layout']              = getLayout();
        //dd($data['layout']);
        $view_name = getTheme().'::student.flashcard.content-flashcard';

        return view($view_name, $data);
    }


}
