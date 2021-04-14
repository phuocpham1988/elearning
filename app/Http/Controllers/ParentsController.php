<?php
namespace App\Http\Controllers;
use \App;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\Classes;
use App\ClassesExam;
use Yajra\Datatables\Datatables;
use DB;
class ParentsController extends Controller
{
 public function __construct()
 {
   $currentUser = \Auth::user();
   $this->middleware('auth');
 }
    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function index($slug)
    {
     $user = getUserWithSlug();
     if(!checkRole(getUserGrade(4)))
     {
      prepareBlockUserMessage();
      return back();
    }
    if(!isEligible($user->slug))
      return back();
    $data['records']      = FALSE;
    $data['user']       = $user;
    $data['title']        = 'Học viên';
    $data['active_class'] = 'children';
    $data['layout']       = getLayout();
    $data['slug'] = $slug;
       // return view('parent.list-users', $data);
    $view_name = getTheme().'::parent.list-users';
    return view($view_name, $data);     
  }
  public function class()
  {
   $user = getUserWithSlug();
   if(!checkRole(getUserGrade(4)))
   {
    prepareBlockUserMessage();
    return back();
  }
  if(!isEligible($user->slug))
    return back();
  $data['records']      = FALSE;
  $data['user']       = $user;
  $data['title']        = 'Lớp học';
  $data['active_class'] = 'children';
  $data['layout']       = getLayout();
       // return view('parent.list-users', $data);
  $view_name = getTheme().'::parent.list-class';
  return view($view_name, $data);     
}
public function classmark($slug, $slug_exam, $slug_category)
{
 $user = getUserWithSlug();
 if(!checkRole(getUserGrade(4)))
 {
  prepareBlockUserMessage();
  return back();
}
if(!isEligible($user->slug))
  return back();
$classes = DB::table('classes')
->select('classes.*' )
->where('id', '=', $slug)
->first();
$exam = DB::table('examseries')
->select('examseries.*' )
->where('id', '=', $slug_exam)
->first();
      // /exit;
$data['slug'] = $slug;
$data['slug_exam'] = $slug_exam;
$data['slug_category'] = $slug_category;
$data['classname'] = $classes->name;
$data['exam_title'] = $exam->title;
$data['records']      = FALSE;
$data['user']       = $user;
$data['title']        = 'Điểm thi lớp: ' . $classes->name . ' - Đề thi: ' . $exam->title;
$data['active_class'] = 'children';
$data['layout']       = getLayout();
       // return view('parent.list-users', $data);
$view_name = getTheme().'::parent.list-classmark';
return view($view_name, $data);     
}
public function getClassmarkDatatable($slug, $slug_exam, $slug_category)
{
 $records = array();
    
     $records = DB::table('quizresultfinish')
     ->join('classes_user', 'classes_user.student_id','=','quizresultfinish.user_id')
     ->join('examseries', 'examseries.id','=','quizresultfinish.examseri_id')
     ->join('users','users.id','=','quizresultfinish.user_id')
     ->select(['users.name','quizresultfinish.quiz_1_total','quizresultfinish.quiz_2_total','quizresultfinish.quiz_3_total','quizresultfinish.total_marks','quizresultfinish.finish','examseries.category_id'])
     ->where('classes_user.classes_id','=',$slug)
     ->where('examseri_id', '=', $slug_exam)
     ->where('examseries.category_id', '=', $slug_category)
     ->get();
     return Datatables::of($records)
     /*->addColumn('action', function ($records) {
      return '<div class="dropdown more">
      <a id="dLabel" type="button" class="more-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <i class="mdi mdi-dots-vertical"></i>
      </a>
      <ul class="dropdown-menu" aria-labelledby="dLabel">
      <li><a href="#"><i class="fa fa-pencil"></i>Xem</a></li>
      </ul>
      </div>';
    })*/
     ->editColumn('name', function($records)
     {
      return ucfirst($records->name);
    }) 
     ->editColumn('total_marks', function($records)
     {
      if ($records->finish == 3) {
          $ketqua = '<span class="label label-info">' . $records->total_marks .'</span>';
       } else {
          $ketqua = '<span class="label label-warning">Chưa hoàn thành</span>';
       }
      return $ketqua;
    }) 
      ->editColumn('finish', function($records)
      {
       if ($records->finish == 3) {
           $ketqua = $records->total_marks;
           if ($records->category_id == 3) {
             if ($ketqua >= 95) {
               $ketqua = '<span class="label label-success">Đạt</span>';
             } else {
               $ketqua = '<span class="label label-warning">Chưa đạt</span>';
             }
           } else {
             if ($ketqua >= 90) {
               $ketqua = '<span class="label label-success">Đạt</span>';
             } else {
               $ketqua = '<span class="label label-warning">Chưa đạt</span>';
             }
           }
        } else {
           $ketqua = '<span class="label label-warning">Chưa hoàn thành</span>';
        }
       return $ketqua;
     }) 
     ->editColumn('quiz_1_total', function($records)
     {
      if ($records->finish == 3) {
        if ($records->category_id == 3) {
          $detail = 'KTNN: <span class="label label-info">'.$records->quiz_1_total.'</span> -Đọc: <span class="label label-info">'.$records->quiz_2_total.'</span> -Nghe: <span class="label label-info">'.$records->quiz_3_total.'</span>';
        } else {
          $detail = 'KTNN: <span class="label label-info">'.$records->quiz_1_total.'</span> -Nghe: <span class="label label-info">'.$records->quiz_3_total.'</span>';
        }
      }
      else {
        
            $detail = '<span class="label label-warning">Chưa hoàn thành</span>';
          }
      return $detail;
    })      

     ->removeColumn('category_id')
     ->removeColumn('quiz_2_total')
     ->removeColumn('quiz_3_total')
     
     ->make();
   }
   public function examList($slug)
   {
     $user = getUserWithSlug();
     if(!checkRole(getUserGrade(4)))
     {
      prepareBlockUserMessage();
      return back();
    }

    //Get đề chỉ định
    $exam_chidinh = DB::table('examseries')
    ->select('examseries.*' )
    ->where('is_paid', '=', 2)
    ->get();
    $data['option_exam_chidinh'] = array_pluck($exam_chidinh, 'title', 'id');      
    $classes = DB::table('classes')
    ->select('classes.*' )
    ->where('id', '=', $slug)
    ->first();
    $data['categories']   = array_pluck(DB::table('quizcategories')->get(), 'category', 'id');
    $data['class_name']   = $classes->name;
    $data['records']      = FALSE;
    $data['slug']         = $slug;
    $data['user']         = $user;
    $data['title']        = 'Danh sách đề thi lớp: ' . $classes->name;
    $data['active_class'] = 'exam-list';
    $data['layout']       = getLayout();
    $view_name = getTheme().'::parent.exam-list';
    return view($view_name, $data);
  }
  public function editExamClass($slug, $slug_exam)
  {
   $user = getUserWithSlug();
   if(!checkRole(getUserGrade(4)))
   {
    prepareBlockUserMessage();
    return back();
  }
  if(!isEligible($user->slug))
    return back();
      //Get đề chỉ định
  $exam_chidinh = DB::table('examseries')
  ->select('examseries.*' )
  ->where('is_paid', '=', 2)
  ->get();
  $data['option_exam_chidinh'] = array_pluck($exam_chidinh, 'title', 'id');      
  $classes = DB::table('classes')
  ->select('classes.*' )
  ->where('id', '=', $slug)
  ->first();
  $class_exam = ClassesExam::where('id','=',$slug_exam)->first();
    // echo "<pre>";
    // print_r ($class_exam);
    // echo "</pre>"; exit;
  $data['class_exam']      = $class_exam;
  $data['class_name']      = $classes->name;
  $data['records']      = FALSE;
  $data['slug']         = $slug;
  $data['user']         = $user;
  $data['title']        = 'Chỉnh sửa đề thi lớp: ' . $classes->name;
  $data['active_class'] = 'exam-list';
  $data['layout']       = getLayout();
  $view_name = getTheme().'::parent.exam-list-edit';
  return view($view_name, $data);
}
public function examListUpdate(Request $request, $slug)
{
 $user = getUserWithSlug();
 if(!checkRole(getUserGrade(4)))
 {
  prepareBlockUserMessage();
  return back();
}
$message = '';
$hasError = 0;
DB::beginTransaction();
$classes_exam = new ClassesExam();
$classes_exam->classes_id = $slug;
$classes_exam->exam_id = $request->exam_id;
$classes_exam->start_date = date('Y-m-d H:i',(strtotime($request->start_date)));
$classes_exam->end_date = date('Y-m-d H:i',(strtotime($request->end_date)));

if (!empty($request->exam_id) && !empty($request->exam_id) && !empty($request->exam_id)) {
  try{
    $classes_exam->save();
    DB::commit();
    $message = 'Bài thi đã được lưu';
  }
  catch(Exception $ex){
    DB::rollBack();
    $hasError = 1;
    $message = $ex->getMessage();
  }
}
if(!$hasError)
  flash('Thêm bài thi thành công', $message, 'success');
else 
  flash('Ooops',$message, 'error');
return back();
}
public function editExamClassUpdate(Request $request, $slug, $slug_exam)
{
 if(!checkRole(getUserGrade(4)))
 {
  prepareBlockUserMessage();
  return back();
}
$message = '';
$hasError = 0;
DB::beginTransaction();
$classes_exam = ClassesExam::where('id','=',$slug_exam)->get()->first();
$classes_exam->exam_id = $request->exam_id;
$classes_exam->start_date = date('Y-m-d H:i',(strtotime($request->start_date)));
$classes_exam->end_date = date('Y-m-d H:i',(strtotime($request->end_date)));
if (!empty($request->exam_id) && !empty($request->exam_id) && !empty($request->exam_id)) {
  try{
    $classes_exam->save();
    DB::commit();
    $message = 'Bài thi đã được lưu';
  }
  catch(Exception $ex){
    DB::rollBack();
    $hasError = 1;
    $message = $ex->getMessage();
  }
}
if(!$hasError)
  flash('Sửa đề thi thành công', $message, 'success');
else 
  flash('Ooops',$message, 'error');
return back();
}
     /**
     * This method returns the datatables data to view
     * @return [type] [description]
     */
     public function getClassDatatable($slug)
     {
      $records = array();
      $user = getUserWithSlug($slug);
        // $records = User::select(['name', 'email', 'image', 'slug', 'id'])->where('parent_id', '=', $user->id)->get();
      $records = Classes::select(['name','created_at', 'id'])->where('teacher_id', '=', $user->id)->orderby('created_at','desc')->get();
      return Datatables::of($records)
      ->addColumn('action', function ($records) {
        return '<div class="dropdown more">
        <a id="dLabel" type="button" class="more-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="mdi mdi-dots-vertical"></i>
        </a>
        <ul class="dropdown-menu" aria-labelledby="dLabel">
        <li><a href="'.PREFIX.'lms/class/'.$records->id.'"><i class="fa fa-pencil"></i>Xem danh sách khóa học</a></li>
        <li><a href="'.URL_PARENT_EXAM_LIST.$records->id.'"><i class="fa fa-pencil"></i>Xem danh sách đề</a></li>
        <li><a href="'.URL_PARENT_CHILDREN.$records->id.'"><i class="fa fa-eye"></i>Xem học viên</a></li>
        </ul>
        </div>';
      })
      ->editColumn('name', function($records)
      {
        return $records->name;
      })
      ->editColumn('created_at', function($records){
        return $records->created_at;
      })
      ->removeColumn('id')
      ->make();
    }
     /**
     * This method returns the datatables data to view
     * @return [type] [description]
     */
     public function getDatatable($slug)
     {
      $records = array();
      $user = getUserWithSlug($slug);
      $records = User::join('classes_user', 'classes_user.student_id', '=', 'users.id')
      ->select(['name', 'email', 'image', 'slug', 'users.id'])->where('classes_user.classes_id', '=', $slug)->get();
      return Datatables::of($records)
      ->addColumn('action', function ($records) {
       $buy_package = '';
       if(!isSubscribed('main',$records->slug)==1)
           // $buy_package =    '<li><a href="'.URL_SUBSCRIBE.$records->slug.'"><i class="fa fa-credit-card"></i>'.getPhrase("buy_package").'</a></li>';
        return '<div class="dropdown more">
      <a id="dLabel" type="button" class="more-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <i class="mdi mdi-dots-vertical"></i>
      </a>
      <ul class="dropdown-menu" aria-labelledby="dLabel">
      <li><a href="'.URL_USERS_EDIT.$records->slug.'"><i class="fa fa-pencil"></i>'.getPhrase("edit").'</a></li>
      </ul>
      </div>';
    })
      ->editColumn('name', function($records)
      {
        return '<a href="'.URL_USER_DETAILS.$records->slug.'" title="'.$records->name.'">'.ucfirst($records->name).'</a>';
      })       
      ->editColumn('image', function($records){
        return '<img src="'.getProfilePath($records->image).'"  />';
      })
      ->removeColumn('slug')
      ->removeColumn('id')
      ->make();
    }
    public function getExamDatatable($slug)
    {
      $records = array();
        //$user = getUserWithSlug($slug);
        /*$records = ExamSeries::join('classes_user', 'classes_user.student_id', '=', 'users.id')
        ->select(['name', 'email', 'image', 'slug', 'users.id'])->where('classes_user.classes_id', '=', $slug)->get();
            */
        $records = ClassesExam::join('examseries', 'examseries.id', '=', 'classes_exam.exam_id')
        ->select(['classes_exam.id', 'examseries.title', 'examseries.slug as examseries_slug','classes_exam.start_date', 'classes_exam.end_date', 'classes_exam.classes_id', 'examseries.category_id', 'examseries.id as examseries_id'])->where('classes_id','=',$slug)->get();
        return Datatables::of($records)
        ->addColumn('action', function ($records) {
         $buy_package = '';
         if(!isSubscribed('main',$records->slug)==1)
           // $buy_package =    '<li><a href="'.URL_SUBSCRIBE.$records->slug.'"><i class="fa fa-credit-card"></i>'.getPhrase("buy_package").'</a></li>';
          return '<div class="dropdown more">
        <a id="dLabel" type="button" class="more-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="mdi mdi-dots-vertical"></i>
        </a>
        <ul class="dropdown-menu" aria-labelledby="dLabel">
        <li><a href="/parent/classmark/'.$records->classes_id.'/'.$records->examseries_id.'/'.$records->category_id.'"><i class="fa fa-pencil"></i>Xem điểm</a></li>
        <li><a href="/exams/view-exam-series/'.$records->examseries_slug.'"><i class="fa fa-pencil"></i>Xem đề thi</a></li>
        <li><a href="/parent/exam_list/edit/'.$records->classes_id.'/'.$records->id.'"><i class="fa fa-pencil"></i>Sửa</a></li>
        <li><a href="javascript:void(0);" onclick="deleteRecord(\''.$records->id.'\');"><i class="fa fa-trash"></i>Xóa</a></li>
        </ul>
        </div>';
      })
        ->editColumn('title', function($records)
        {
          return $records->title;
        })       
        //  ->editColumn('classes_id', function($records){
        //     return 2;
        // })
        //  ->editColumn('exam_id', function($records){
        //     return 2;
        // })
        ->editColumn('start_date', function($records){
          return $records->start_date;
        })
        ->removeColumn('id')
        ->removeColumn('classes_id')
        ->removeColumn('category_id')
        ->removeColumn('examseries_id')
        ->removeColumn('examseries_slug')
        ->make();
      }

      

      public function childrenAnalysis()
      {
       $user = getUserWithSlug();
       if(!checkRole(getUserGrade(4)))
       {
        prepareBlockUserMessage();
        return back();
      }
      if(!isEligible($user->slug))
        return back();
      $data['records']      = FALSE;
      $data['user']       = $user;
      $data['title']        = 'Phân tích học viên';
      $data['active_class'] = 'analysis';
      $data['layout']       = getLayout();
       // return view('parent.list-users', $data);
      $view_name = getTheme().'::parent.list-users';
      return view($view_name, $data);     
    }
    public function delete($slug)
    {
      if(!checkRole(getUserGrade(4)))
      {
        prepareBlockUserMessage();
        return back();
      }
      /**
       * Delete the questions associated with this ClassesExam first
       * Delete the ClassesExam
       * @var [id]
       */
      $record = ClassesExam::where('id', '=', $slug)->get()->first();
      try{
        $record->delete();
        $response['status'] = 1;
        $response['message'] = 'Bạn đã xóa thành công';
      } catch (Exception $e) {
        $response['status'] = 0;
        if(getSetting('show_foreign_key_constraint','module'))
          $response['message'] =  $e->getMessage();
        else
          $response['message'] =  getPhrase('this_record_is_in_use_in_other_modules');
      }
      return json_encode($response);
    }

    public function ajaxn($subject_id) {

        //$list = Topic::getTopics($slug, 0);

        $list = DB::table('examseries')->where('category_id', $subject_id)->get();
           
        $parents =  array();
        array_push($parents, array('id'=>'', 'text' => '--Chọn bộ đề thi--'));

        foreach ($list as $key => $value) {
          $r = array('id'=>$value->id, 'text' => $value->title);
              array_push($parents, $r);
        }
        return json_encode($parents);
     
    }

    public function lmsClass($slug)
   {
     $user = getUserWithSlug();
     if(!checkRole(getUserGrade(4)))
     {
      prepareBlockUserMessage();
      return back();
    }

    //Get đề chỉ định
    $exam_chidinh = DB::table('examseries')
    ->select('examseries.*' )
    ->where('is_paid', '=', 2)
    ->get();
    $data['option_exam_chidinh'] = array_pluck($exam_chidinh, 'title', 'id');      
    $classes = DB::table('classes')
    ->select('classes.*' )
    ->where('id', '=', $slug)
    ->first();
    $data['categories']   = array_pluck(DB::table('quizcategories')->get(), 'category', 'id');
    $data['class_name']   = $classes->name;
    $data['records']      = FALSE;
    $data['slug']         = $slug;
    $data['user']         = $user;
    $data['title']        = 'Danh sách đề thi lớp: ' . $classes->name;
    $data['active_class'] = 'exam-list';
    $data['layout']       = getLayout();
    $view_name = getTheme().'::parent.exam-list';
    return view($view_name, $data);
  }
  }