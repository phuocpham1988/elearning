<?php
namespace App\Http\Controllers;
use \App;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Classes;
use App\ClassesUser;
use App\User;
use Yajra\Datatables\Datatables;
use DB;
use Image;
use ImageSettings;
use File;
use Illuminate\Support\Facades\Validator;
use Input;
use Excel;
use Exception;
class ClassesController extends Controller
{
 public $excel_data = array();
  public $columns = '';
   public function __construct()
    {
      $this->middleware('auth');
    }
    /**
     * Course listing method
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        if(!checkRole(getUserGrade(2)))
      {
        prepareBlockUserMessage();
        return back();
      }
        $data['active_class']       = 'Lớp học';
        $data['title']              = 'Lớp học';
        $view_name = getTheme().'::classes.list';
        return view($view_name, $data);
    }
    /**
     * This method returns the datatables data to view
     * @return [type] [description]
     */
    public function getDatatable(Request $request)
    {
      if(!checkRole(getUserGrade(2)))
      {
        prepareBlockUserMessage();
        return back();
      }
         $records = Classes::join(
            'users', 'users.id', '=', 'classes.teacher_id')->select(['classes.name', 'users.name as giaovien', 'classes.id', 'classes.updated_at'])
            ->orderBy('updated_at', 'desc');
        $table = Datatables::of($records)
        ->addColumn('action', function ($records) {
            return '<div class="dropdown more">
                        <a id="dLabel" type="button" class="more-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dLabel">
                          <li><a href="#"><i class="fa fa-eye"></i>Xem</a></li>
                          <li><a href="#"><i class="fa fa-eye"></i>Chỉnh sửa</a></li>
                          <li><a href="/classes/classes-details/'.$records->id.'"><i class="fa fa-eye"></i>Thêm học viên</a></li>
                          <li><a href="#"><i class="fa fa-plus-circle"></i>Xóa</a></li>
                        </ul>
                    </div>';
            })
        ->editColumn('name', function($records) {
            return $records->name;
        })
        ->editColumn('giaovien', function($records) {
            return $records->giaovien;
        })
        ->removeColumn('id')
        ->removeColumn('updated_at');
        return $table->make();
    }
      /**
     * Questions listing method
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function show($slug)
    {
      if(!checkRole(getUserGrade(2)))
      {
        prepareBlockUserMessage();
        return back();
      }
      $subject = Subject::getRecordWithSlug($slug);
      if($isValid = $this->isValidRecord($subject))
        return redirect($isValid);
      $data['active_class'] = 'exams';
        $data['title']        = $subject->subject_title.' '.getPhrase('questions');
        $data['subject']      = $subject;
      // return view('exams.questionbank.questions', $data);
         $view_name = getTheme().'::exams.questionbank.questions';
        return view($view_name, $data);
    }
    /**
     * This method returns the datatables data to view
     * @return [type] [description]
     */
    public function getQuestions($slug)
    {
      if(!checkRole(getUserGrade(2)))
      {
        prepareBlockUserMessage();
        return back();
      }
        $subject = Subject::getRecordWithSlug($slug);
        $isValid = $this->isValidRecord($subject);
      if($isValid)
        return redirect($isValid);
        $records = QuestionBank::join(
            'subjects', 'questionbank.subject_id', '=', 'subjects.id')
        ->join(
            'topics', 'questionbank.topic_id', '=', 'topics.id'
            )
        ->select(['subject_title', 'topic_name', 'questionbank.question', 'questionbank.marks', 'questionbank.answers', 'book', 'page', 'correct_answers',
            'questionbank.id', 'questionbank.slug',
            'questionbank.updated_at'])
        ->where('questionbank.subject_id','=', $subject->id)
        ->orderBy('updated_at','desc');
        $table = Datatables::of($records)->removeColumn('slug')
        ->addColumn('action', function ($records) {
            $answer_question = json_decode( $records->answers );
            $i = 1;
            $answers = "";
            foreach ($answer_question as $key => $value) {
               // $option = htmlentities( change_furigana($value->option_value, 'return'));
               $option = $value->option_value;
               $answers .= 'data-answer'.$i.'="' . $option .'" data-answerfile'.$i.'="'.$value->file_name.'"';
              // $answers .= "";
              $i++;
            }
            //$question = htmlentities( change_furigana($records->question, 'return'));
            $question = $records->question;
            return '<div class="dropdown more">
                        <a id="dLabel" type="button" class="more-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dLabel">
                       <li><a href="javascript:void(0);" class="hikari-view-question" '. $answers .' data-question="'. $question .'" data-marks="'.$records->marks.'" data-difficulty_level="'.$records->difficulty_level.'" data-topic_name="'.$records->topic_name    .'" data-book="'.$records->book.'" data-page="'.$records->page.'" data-correct_answers="'.$records->correct_answers.'"><i class="fa fa-pencil"></i>View</a></li>
                       <li><a href="'.URL_QUESTIONBANK_EDIT_QUESTION.$records->slug.'"><i class="fa fa-pencil"></i>'.getPhrase("edit").'</a></li>
                       <li><a href="javascript:void(0);" onclick="deleteRecord(\''.$records->slug.'\');"><i class="fa fa-trash"></i>'. getPhrase("delete").'</a></li>
                        </ul>
                    </div>';
            })
        ->removeColumn('id')
        // ->removeColumn('slug')
        ->removeColumn('updated_at')
        ->removeColumn('answers')
        ->removeColumn('book')
        ->removeColumn('page')
        ->removeColumn('correct_answers')
        ->editColumn('question_type', function($results){
          return ucfirst($results->question_type);
         })
        ->editColumn('question', function($results){
          return '<span title="'.$results->question.'">'. str_limit( $results->question, 40).'</span>';
        })
        ->editColumn('difficulty_level',function($results){
            return ucfirst($results->difficulty_level);
        });
        return $table->make();
    }
    /**
     * This method loads the create view
     * @return void
     */
    public function create()
    {
      if(!checkRole(getUserGrade(2)))
      {
        prepareBlockUserMessage();
        return back();
      }
      $teacher_all = User::where('role_id', '=', 4)->get();
      $teacher = array_pluck($teacher_all, 'name', 'id');
      // echo "<pre>";
      // print_r ($teacher);
      // echo "</pre>";
      $data['teacher_id']         = $teacher;
      $data['record']             = true;
      $data['active_class']       = 'classes';
      $data['title']              = 'Thêm lớp';
      $data['layout']             = getLayout();
      $view_name = getTheme().'::classes.add-edit-classes';
      return view($view_name, $data);
    }
    /**
     * This method loads the edit view based on unique slug provided by user
     * @param  [string] $slug [unique slug of the record]
     * @return [view with record]
     */
    public function edit($slug)
    {
       if(!checkRole(getUserGrade(2)))
      {
        prepareBlockUserMessage();
        return back();
      }
      $record = QuestionBank::getRecordWithSlug($slug);
      if($isValid = $this->isValidRecord($record))
        return redirect($isValid);
      $subject = $record->subject()->first();
      //$topics = $subject->topics()->where('parent_id','=','0')->get();
        $topics = $subject->topics()->get();
      if(!$topics->count()) {
      /**
       * If no topics available in selected subject,
       * redirect back with message to update topics
       */
        $message =
        $subject->subject_title.'  have no topics, please add topics to upload questions';
        flash('Ooops...!', $message, 'overlay');
        return back();
      }
      // dd($record);
       $settings['total_answers']         = $record->total_answers;
       $settings['total_correct_answers'] = $record->total_correct_answers;
       $settings['correct_answers']       = json_decode($record->correct_answers);
       $settings['question_type']         = $record->question_type;
       $settings['answers']               = json_decode($record->answers);
       // dd($settings);
      $data['topics']           = array_pluck($topics, 'topic_name', 'id');
      $data['record']           = $record;
      $data['active_class']     = 'master_settings';
      $data['title']            = getPhrase('edit_question');
      $data['subject']          = $subject;
      $settings                 = (object)$settings;
      $data['settings']         = json_encode($settings);
      // return view('exams.questionbank.add-edit', $data);
         $view_name = getTheme().'::exams.questionbank.add-edit';
        return view($view_name, $data);
    }
    /**
     * Update record based on slug and reuqest
     * @param  Request $request [Request Object]
     * @param  [type]  $slug    [Unique Slug]
     * @return void
     */
    public function update(Request $request, $slug)
    {
        if(!checkRole(getUserGrade(2)))
      {
        prepareBlockUserMessage();
        return back();
      }
        $record                 = QuestionBank::where('slug', $slug)->get()->first();
        $rules['topic_id']         = 'bail|required|integer';
        $rules['question']          = 'bail|required';
        $rules['marks']             = 'bail|required|integer';
         DB::beginTransaction();
      try{
        /**
         * As we are disableing the question type in edit,
         * we need to get the type of the question for existing record
         * Assign the question type to a varable $current_question_type
         */
        $current_question_type = $record->question_type;
       if($current_question_type == 'radio') {
            $rules['total_answers']     = 'bail|required|integer';
            $rules = $this->validateRadioQuestions($request, $rules);
        }
        if($current_question_type == 'checkbox') {
            $rules['total_answers']     = 'bail|required|integer';
            $rules = $this->validateCheckboxQuestions($request, $rules);
        }
         if($current_question_type == 'match') {
            $rules['total_answers']     = 'bail|required|integer';
            $rules = $this->validateMatchQuestions($request, $rules);
        }
        /**
         * As it is fill in the blanks type of question
         * there are no no. of options fields, it only contains the answers
         * so ignore the validation for total_answers
         */
        if($current_question_type == 'blanks') {
            $rules = $this->validateBlankQuestions($request, $rules);
        }
        $this->validate($request, $rules);
        $name             = $request->question;
       /**
        * As we are maintaining unique slug for each question,
        * if the question is changed no need worry,
        * we can continue with the existing old slug
        */
        $request->question_type = $record->question_type;
        $record->question               = $name;
        $record->subject_id             = $request->subject_id;
        $record->topic_id               = $request->topic_id;
        $record->question               = $request->question;
        $record->book                   = $request->book;
        $record->page                   = $request->page;
        $record->difficulty_level       = $request->difficulty_level;
        $record->hint                   = $request->hint;
        $record->explanation            = $request->explanation;
        $record->marks                  = $request->marks;
        $record->time_to_spend          = $request->time_to_spend;
        if($request->has('question_l2'))
        $record->question_l2            = $request->question_l2;
        if($request->has('explanation_l2'))
        $record->explanation_l2         = $request->explanation_l2;
         if($current_question_type == 'radio'){
             $record->total_answers          = $request->total_answers;
            $record->correct_answers        = $request->correct_answers;
            $record->total_correct_answers  = $request->total_correct_answers;
         }
        if($current_question_type == 'checkbox'){
            $record->total_answers          = $request->total_answers;
            $record->correct_answers        = $this->prepareMultiAnswers($request);
            $record->total_correct_answers  = $request->total_correct_answers;
        }
        if($current_question_type == 'blanks'){
            $record->total_answers          = $request->total_correct_answers;
            $record->correct_answers        = $this->prepareMultiAnswers($request);
            $record->total_correct_answers  = $request->total_correct_answers;
        }
         if($current_question_type == 'match'){
            $record->total_answers          = $request->total_answers;;
            $record->total_correct_answers  = $request->total_answers;
            $record->correct_answers        = $this->prepareMatchAnswers($request);
       }
       if($current_question_type == 'para'   ||
          $current_question_type == 'video'  ||
          $current_question_type == 'audio'
        ){
            $record->total_answers          = $request->total_answers;;
            $record->total_correct_answers  = $request->total_answers;
            $record->correct_answers        = $this->prepareParaAnswers($request);
       }
        // Save data with no images
        $record->save();
          // Update data with images
        if($request->hasFile('question_file')) {
          $record->question_file          = $this->processUpload($request, $record, 'question_file', 'question');
        }
       if($request->hasFile($record->explanation_file))
        $record->explanation_file          = $this->processUpload($request, $record, 'explanation_file', 'explanation');
        if($current_question_type == 'match'){
            $record->answers     = $this->prepareMatchQuestionOptions($request, $record);
        }
        else if($current_question_type == 'para' || $current_question_type == 'video'|| $current_question_type == 'audio' )
            $record->answers     = $this->prepareParaQuestionOptions($request, $record);
        else
          $record->answers              = $this->prepareOptions($request, $record);
        $record->save();
        flash('success','record_added_successfully', 'success');
         DB::commit();
      }
     catch(Exception $e)
     {
      DB::rollBack();
       if(getSetting('show_foreign_key_constraint','module'))
       {
          flash('oops...!',$e->errorInfo, 'error');
       }
       else {
          flash('oops...!','improper_data_in_the_question', 'error');
       }
     }
       return redirect(URL_QUIZ_QUESTIONBANK);
    }
    /**
     * This method adds record to DB with the following steps
     * 1 Validate Request
     * 2 Save Data and get ID of that record
     * 3 Process question image and upload if exists
     * 4 Process Option images and upload if exists
     * 5 Update the same record as files got uploaded uploaded
     * @param  Request $request [Request Object]
     * @return void
     */
    public function store(Request $request)
    {
      // echo "<pre>";
      // print_r ($_POST);
      // echo "</pre>";
      // exit;
     // dd($request);
    /**
     * Validation for the Master Data of a question
     */
      if(!checkRole(getUserGrade(2)))
      {
        prepareBlockUserMessage();
        return back();
      }
      DB::beginTransaction();
      try{
        // $rules['name']      = 'bail|required|integer';
        // $rules['teacher_id']   = 'bail|required';
        // $this->validate($request, $rules);
        $record = new Classes();
        $record->name           = $request->name;
        $record->teacher_id     = $request->teacher_id;
        // Save data with no images
        $record->save();
         flash('success','record_added_successfully', 'success');
         DB::commit();
      }
     catch(Exception $e)
     {
        dd($e->getMessage());
        DB::rollBack();
       if(getSetting('show_foreign_key_constraint','module'))
       {
          flash('oops...!',$e->errorInfo, 'error');
       }
       else {
          flash('oops...!','improper_data_in_the_question', 'error');
       }
     }
       return redirect('/classes');
    }
    /**
     * This method prepares the json data to be inserted in place of options
     * by processing the image information and other attributes
     * @param  [type] $request [request sent by the user]
     * @param  [type] $record  [the record which was saved to DB]
     * @return [type]          [description]
     */
    public function prepareOptions($request, $record)
    {
      $options    = $request->options;
      $optionsl2  = $request->optionsl2;
      $list       = array();
        /**
         * Get the image path from ImageSettings class
         * This destinationPath variable will be used
         * to delete an image if user edits any question and changes an image
         */
         $imageObject = new App\ImageSettings();
         $destinationPath      = $imageObject->getExamImagePath();
         /**
          * Loop the total options selected by user
          * and process each option by checking wether the image
          * has been uploaded or not
          * After this loop multiple objects will be created based on
          * the no. of options(total_answers) selected by user
          * Each object contains 3 properties
          * 1) option_value : stores the text submitted as option
          * 2) has_file     : stores if this particular option has any file
          * 3) file_name    : stores the name of file uploaded
          */
      for($index = 0; $index < $request->total_answers; $index++)
      {
            /**
             * The $answers variable is used when user edit any question
             * It will contain the previous option values
             * As it is under for loop, every option property will be checked
             * by comparing wether the file is submitted for this particular object
             * If submitted it will delete the old file and overwrite with new file
             * @var [type]
             */
            $answers = json_decode($record->answers);
            $old_has_file = isset($answers[$index]->has_file) ? $answers[$index]->has_file : 0;
            $old_file_name = isset($answers[$index]->file_name) ? $answers[$index]->file_name : '';
         $spl_char   = ['\t','\n','\b','\c','\r','\'','\\','\$','\"',"'"];
        $list[$index]['option_value']   = str_replace($spl_char,'',$options[$index]);
        $list[$index]['optionl2_value']   = str_replace($spl_char,'',$optionsl2[$index]);
        // $list[$index]['option_value']  = $options[$index];
        $list[$index]['has_file']     = $old_has_file;
        $list[$index]['file_name']    = $old_file_name;
        $file_name            = 'upload_'.$index;
        if ($request->hasFile($file_name))
        {
          $rules = array($file_name => 'mimes:jpeg,jpg,png,gif|max:10000');
          $validator = Validator::make($request->options, $rules);
          if($validator->fails())
              return '';
                  //Delete Old Files
                if($old_file_name)
                    $this->deleteExamFile($old_file_name, $destinationPath);
          // This option has the image to be uploaded,
          // so process image and update the fields
          $list[$index]['has_file']     = 1;
          $list[$index]['file_name']    = $this
                          ->processUpload($request, $record,$file_name, 'option');
        }
      }
      return json_encode($list);
    }
    /**
     * The Multi Answers will be prepared in this method
     * The format will be multiple objects with property answer
     * Need to reference this as $CorrectAnswers->answer in foreach loop
     * @param  [type] $request [description]
     * @return [type] json     [description]
     */
    public function prepareMultiAnswers($request)
    {
        $correct_answers = $request->correct_answers;
        $list = array();
        for($index = 0; $index < $request->total_correct_answers; $index++)
        {
            $list[$index]['answer']   = $correct_answers[$index];
        }
        return json_encode($list);
    }
    /**
     * In this method, the options are divided to multi dimentional array
     * Each object will have left and right as root
     * Each will have the title and options as properties
     * @param  [type] $request [description]
     * @param  [type] $record  [description]
     * @return [type]          [description]
     */
    public function prepareMatchQuestionOptions($request, $record)
    {
        $options_left   = $request->options_left;
        $options_right  = $request->options_right;
        $optionsl2_left   = $request->optionsl2_left;
        $optionsl2_right  = $request->optionsl2_right;
        $list = array();
        $list['left']['title']       = $request->title_left;
        $list['right']['title']      = $request->title_right;
        $list['left']['titlel2']     = $request->title_left_l2;
        $list['right']['titlel2']    = $request->title_right_l2;
        $list['left']['options']     = array();
        $list['right']['options']    = array();
        $list['left']['optionsl2']   = array();
        $list['right']['optionsl2']  = array();
        for($index = 0; $index < $request->total_answers; $index++)
        {
            $list['left']['options'][$index]     = $options_left[$index];
            $list['right']['options'][$index]    = $options_right[$index];
            $list['left']['optionsl2'][$index]   = $optionsl2_left[$index];
            $list['right']['optionsl2'][$index]  = $optionsl2_right[$index];
        }
        return json_encode($list);
    }
    /**
     * This method will prepare the list of answers provided with
     * match the following questions
     * These answers will directly point to the options object in the above method
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    public function prepareMatchAnswers($request)
    {
        $correct_answers = $request->correct_answers;
        $list = array();
        for($index = 0; $index < $request->total_answers; $index++)
        {
            $list[$index]['answer']   = $correct_answers[$index];
        }
        return json_encode($list);
    }
     /**
     * In this method, the options are divided to multi dimentional array
     * Each object will have index number as the question block
     * Each index will have the question, total_options,
     * array of options associated with that
     * Each will have the title and options as properties
     * @param  [type] $request [description]
     * @param  [type] $record  [description]
     * @return [type]          [description]
     */
    public function prepareParaQuestionOptions($request, $record)
    {
        $total_options  = $request->total_para_options;
        $questions      = $request->questions_list;
        // $questionsl2    = $request->questions_listl2;
        $list = array();
        for($index = 0; $index < $request->total_answers; $index++)
        {
            $options      = $request->options[$index];
            // $optionsl2    = $request->optionsl2[$index];
            $list_options = array();
            $list_optionsl2 = array();
            $list[$index]['question']       = $questions[$index];
            // $list[$index]['questionl2']     = $questionsl2[$index];
            $list[$index]['total_options']  = $total_options;
            for($option_number = 0; $option_number < $total_options; $option_number++){
                $list_options[$index][$option_number] = $options[$option_number];
                // $list_optionsl2[$index][$option_number] = $optionsl2[$option_number];
            }
            $list[$index]['options'] = $list_options;
            // $list[$index]['optionsl2'] = $list_optionsl2;
        }
        // dd($list);
        return json_encode($list);
    }
    /**
     * This method will prepare the list of answers provided with
     * Paragraph or video type of questions
     * These answers will directly associated with the question number order
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    public function prepareParaAnswers($request)
    {
        $correct_answers = $request->correct_answers;
        $list = array();
        for($index = 0; $index < $request->total_answers; $index++)
        {
            $list[$index]['answer']   = $correct_answers[$index];
            // dd($list);
        }
        return json_encode($list);
    }
    /**
     * This method process the image is being refferred
     * by getting the settings from ImageSettings Class
     * @param  Request $request   [Request object from user]
     * @param  [type]  $record    [The saved record which contains the ID]
     * @param  [type]  $file_name [The Name of the file which need to upload]
     * @param  string  $type      [Identify if it is question or an option image]
     * @return [type]             [description]
     */
     public function processUpload(Request $request, $record, $file_name, $type = 'option')
     {
       // if(env('DEMO_MODE')) {
       //  return ;
       // }
        // echo "<pre>";
        // print_r ($request->question_file);
        // echo "</pre>";
        // echo $file_name;
        // echo $request->question_file->guessClientExtension();
        // exit;
         if ($request->hasFile($file_name)) {
          $imageObject = new App\ImageSettings();
          $destinationPath      = $imageObject->getExamImagePath();
          $fileName = $record->id.'-'.$file_name.'.'.$request->$file_name->getClientOriginalExtension();
          if($type!='option')
          {
          $fileName = $record->id.$type.'.'.$request->$file_name->getClientOriginalExtension();
          }
          // echo $request->$file_name->getClientOriginalExtension();
          // echo $fileName; exit;
          // $fileName = $record->id.'question.mp3';
          $request->file($file_name)->move($destinationPath, $fileName);
         return $fileName;
        }
     }
    /**
     * Validates the single answer type of questions and returs a validation rules
     * @param  [type] $request [Object of Request class]
     * @param  [type] $rules   [array of rules]
     * @return [array] $rules  [array of extended rules]
     */
    public function validateRadioQuestions($request, $rules)
    {
      $fileSize = (new ImageSettings())->getExamMaxFilesize();
      for($i=0; $i<$request->total_answers; $i++)
      {
        $file_name = 'upload_'.$i;
        if($request->hasFile($file_name))
        {
          $rules[$file_name] = 'mimes:jpeg,jpg,png,gif|max:'.$fileSize;
        }
      }
      $rules['correct_answers'] = 'bail|required|integer';
      return $rules;
    }
    public function validateCheckboxQuestions($request, $rules)
    {
        return $rules;
    }
    public function validateMatchQuestions($request, $rules)
    {
        $rules['title_left']    = 'required|max:30';
        $rules['title_right']   = 'required|max:30';
        return $rules;
    }
    public function validateBlankQuestions($request, $rules)
    {
        for($i=0; $i < $request->total_correct_answers; $i++)
        {
             $rules['correct_answers.'.$i] = 'required|max:30';
        }
        return $rules;
    }
    /**
     * Delete Record based on the provided slug
     * @param  [string] $slug [unique slug]
     * @return Boolean
     */
    public function delete($slug)
    {
      if(!checkRole(getUserGrade(2)))
      {
        prepareBlockUserMessage();
        return back();
      }
        $qbObject = new QuestionBank();
        $record = $qbObject->getRecordWithSlug( $slug);
     try {
        if(!env('DEMO_MODE')) {
            $path   = (new App\ImageSettings())->getExamImagePath();
            $options = json_decode($record->answers);
          $this->deleteExamFile($options, $path, TRUE);
          $this->deleteExamFile($record->question_file, $path, FALSE);
            $record->delete();
        }
            $response['status'] = 1;
            $response['message'] = getPhrase('record_deleted_successfully');
            return json_encode($response);
        } catch ( \Illuminate\Database\QueryException $e) {
                 $response['status'] = 0;
           if(getSetting('show_foreign_key_constraint','module'))
            $response['message'] =  $e->errorInfo;
           else
            $response['message'] =  getPhrase('this_record_is_in_use_in_other_modules');
       }
       return json_encode($response);
    }
    public function deleteUserClasses($slug)
    {
      if(!checkRole(getUserGrade(1)))
            {
              prepareBlockUserMessage();
              return back();
            }
            /**
             * Delete the questions associated with this ClassesExam first
             * Delete the ClassesExam
             * @var [id]
             */
            $record = ClassesUser::where('id', '=', $slug)->get()->first();
            try{
              $record->delete();
              $response['status'] = 1;
              $response['message'] = 'Bạn đã xóa học viên thành công';
            } catch (Exception $e) {
              $response['status'] = 0;
              if(getSetting('show_foreign_key_constraint','module'))
                $response['message'] =  $e->getMessage();
              else
                $response['message'] =  getPhrase('this_record_is_in_use_in_other_modules');
            }
            return json_encode($response);
    }
    public function deleteExamFile($record, $path, $is_array = FALSE)
    {
       if(env('DEMO_MODE')) {
        return ;
       }
      $files = array();
       $has_files = FALSE;
       if($is_array) {
         foreach($record as $option) {
          if(isset($option->has_file)){
          if($option->has_file)
          {
            $has_files = TRUE;
            $files[] = $path.$option->file_name;
          }
        }
           }
      }
      else {
        $has_files = TRUE;
          $files[] = $path.$record;
      }
       if($has_files)
        {
           File::delete($files);
        }
    }
    public function isValidRecord($record)
    {
      if ($record === null) {
        flash('Ooops...!', getPhrase("page_not_found"), 'error');
        return $this->getRedirectUrl();
    }
    return FALSE;
    }
    public function getReturnUrl()
    {
      return URL_QUIZ_QUESTIONBANK;
    }
      /**
    * Display a Import Questions page
    *
    * @return Response
    */
     public function import()
     {
        if(!checkRole(getUserGrade(2)))
        {
          prepareBlockUserMessage();
          return back();
        }
        $data['records']      = FALSE;
        $data['active_class'] = 'exams';
        $data['heading']      = getPhrase('import_questions');
        $data['title']        = getPhrase('import_questions');
        $data['layout']        = getLayout();
        // return view('exams.questionbank.import.import', $data);
         $view_name = getTheme().'::exams.questionbank.import.import';
        return view($view_name, $data);
     }
    /**
     * Validates the subject
     * @param  [type]  $subject_id [description]
     * @return boolean             [description]
     */
    public function isValidSubject($subject_id)
    {
      $subject_id = (int) $subject_id;
       return Subject::where('id','=',$subject_id)->get()->count();
    }
      public function readExcel(Request $request)
     {
       if(!checkRole(getUserGrade(2)))
        {
          prepareBlockUserMessage();
          return back();
        }
       $success_list = [];
       $failed_list = [];
        $summary = [];
         try{
        if(Input::hasFile('excel')){
          $path = Input::file('excel')->getRealPath();
          $data = Excel::load($path, function($reader) {
          })->get();
          $all_records  = array();
          $excel_record = array();
          $final_records =array();
          $isHavingDuplicate = 0;
          if(!empty($data) && $data->count()){
            foreach ($data as $key => $value) {
              if(array_has($value,'subject_id'))
              {
                $all_records[] = $value;
              }
              else {
              foreach($value as $record)
              {
                $all_records[] = $record;
              }
            }
            }
             $questionbank = new QuestionBank();
            $summary = (object)$this->processExcelQuestions($request, $all_records);
        }
      }
       if(isset($summary->failed_list) || isset($summary->success_list)) {
       $data['failed_list']   =   $summary->failed_list;
       $data['success_list']  =    $summary->success_list;
       $this->excel_data['failed'] = $summary->failed_list;
       $this->excel_data['success'] = $summary->success_list;
       $this->excel_data['columns'] = $summary->columns_list;
         $this->downloadExcel();
       }
       else {
        flash('oops...!','improper_sheet_uploaded', 'error');
       }
      }
     catch(Exception $e)
     {
      // \Illuminate\Database\QueryException
       if(getSetting('show_foreign_key_constraint','module'))
       {
          flash('oops...!',$e->errorInfo, 'error');
       }
       else {
          flash('oops...!','improper_sheet_uploaded', 'error');
       }
       return back();
     }
        // URL_USERS_IMPORT_REPORT
       $data['failed_list']   =   $failed_list;
       $data['success_list']  =    $success_list;
       $data['records']      = FALSE;
       $data['layout']       = getLayout();
       $data['active_class'] = 'exams';
       $data['heading']      = getPhrase('upload_questions');
       $data['title']        = getPhrase('report');
       // return view('exams.questionbank.import.import-result', $data);
        $view_name = getTheme().'::exams.questionbank.import.import-result';
        return view($view_name, $data);
     }
public function getFailedData()
{
  return $this->excel_data;
}
public function downloadExcel()
{
    Excel::create('questions_report', function($excel) {
      $excel->sheet('Failed', function($sheet) {
        $data = $this->getFailedData();
        array_unshift($data['columns'], "Reason");
      $sheet->row(1, $data['columns']);
      $data = $this->getFailedData();
      $cnt = 2;
      foreach ($data['failed'] as $data_item) {
        $item = $data_item->record;
        $record_data = [];
        $record_data[] = $data_item->type;
        foreach($data['columns'] as $key=>$value)
         $record_data[] = $item->$value;
        $sheet->appendRow($cnt++, $record_data);
      }
    });
     $excel->sheet('Success', function($sheet) {
        $data = $this->getFailedData();
      $sheet->row(1, $data['columns']);
      $cnt = 2;
      foreach ($data['success'] as $data_item) {
        $item = (object)collect($data_item)->all();
        $record_data = [];
        foreach($data['columns'] as $key=>$value)
        {
          $item_value = $item->$value;
          if(isset($item_value)) {
            if($item_value!=NULL)
            $record_data[] = $item_value;
          }
        }
        $sheet->appendRow($cnt++, $record_data);
      }
    });
    })->download('xlsx');
}
     public function processExcelQuestions(Request $request, $data)
     {
       if(!count($data))
        return false;
     $questionbank = new QuestionBank();
      switch($request->question_type)
      {
        case 'radio':
                   $questionbank->uploadRadioQuestions($data);
                   return array(
                                'failed_list'   => $questionbank->failed_list,
                                'success_list'  => $questionbank->success_list,
                                'columns_list'  => $questionbank->getAllColumnsList($data)
                                );
          break;
        case 'checkbox':
                   $questionbank->uploadCheckboxQuestions($data);
                   return array(
                                'failed_list'   => $questionbank->failed_list,
                                'success_list'  => $questionbank->success_list,
                                 'columns_list'  => $questionbank->getAllColumnsList($data)
                                );
          break;
        case 'blanks':
                   $questionbank->uploadBlankQuestions($data);
                   return array(
                                'failed_list'   => $questionbank->failed_list,
                                'success_list'  => $questionbank->success_list,
                                 'columns_list'  => $questionbank->getAllColumnsList($data)
                                );
          break;
      }
     }
  public function viewClassesDetails($slug)
  {
     if(!checkRole(getUserGrade(4)))
        {
          prepareBlockUserMessage();
          return back();
        }
       // $record = User::where('slug', '=', $slug)->first();
       // if($isValid = $this->isValidRecord($record))
       //   return redirect($isValid);
       $data['id']  = $slug;
       $data['layout']       = getLayout();
       $data['active_class'] = 'users';
       // $data['record']       = $record;
       $data['heading']      = 'Cập nhật lớp';
       $data['title']        = 'Cập nhật lớp';
       // return view('users.parent-details', $data);
      $view_name = getTheme().'::classes.classes-details';
      return view($view_name, $data);
  }
  public function getParentsOnSearch(Request $request)
  {
        $term = $request->search_text;
        $role_id = getRoleData('student');
        $records = App\User::
            where('name','LIKE', '%'.$term.'%')
            ->orWhere('username', 'LIKE', '%'.$term.'%')
            ->orWhere('phone', 'LIKE', '%'.$term.'%')
            ->groupBy('id')
            ->havingRaw('role_id='.$role_id)
            ->select(['id','role_id','name', 'username', 'email', 'phone'])
            ->get();
            return json_encode($records);
  }
  public function updateClassesDetails(Request $request, $slug)
  {

        if(!checkRole(getUserGrade(4)))
        {
          prepareBlockUserMessage();
          return back();
        }
        $message = '';
        $hasError = 0;
        DB::beginTransaction();
            $classes_user = new ClassesUser();
            $classes_user->classes_id = $slug;
            $classes_user->student_id = $request->parent_user_id;
        try{
            $classes_user->save();
            DB::commit();
            $message = 'record_updated_successfully';
        }
        catch(Exception $ex){
            DB::rollBack();
            $hasError = 1;
            $message = $ex->getMessage();
        }
        if(!$hasError)
            flash('success',$message, 'success');
        else
            flash('Ooops',$message, 'error');
        return back();
  }
  /**
     * This method returns the datatables data to view
     * @return [type] [description]
     */
    public function getClassesUserDatatable($slug = '')
    {
      if(!checkRole(getUserGrade(2)))
      {
        prepareBlockUserMessage();
        return back();
      }
        $records = User::join(
            'classes_user', 'users.id', '=', 'classes_user.student_id')->select([ 'hid','users.id','users.name', 'users.email', 'users.phone', 'classes_user.updated_at', 'classes_user.id as classes_user_id'])->where('classes_user.classes_id','=',$slug)
            ->orderBy('classes_user.updated_at', 'desc');
        $table = Datatables::of($records)
        ->addColumn('action', function ($records) {
        return '<div class="dropdown more">
                    <a id="dLabel" type="button" class="more-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="mdi mdi-dots-vertical"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dLabel">
                      <li><a href="#"><i class="fa fa-eye"></i>Xem</a></li>
                      <li><a href="#"><i class="fa fa-eye"></i>Chỉnh sửa</a></li>
                      <li><a href="http://elearning.hikariacademy.edu.vn/classes/classes-details/'.$records->id.'"><i class="fa fa-eye"></i>Thêm học viên</a></li>
                      <li><a href="javascript:void(0);" onclick="deleteRecord(\''.$records->classes_user_id.'\');"><i class="fa fa-trash"></i>Xóa</a></li>
                    </ul>
                </div>';
        })
        ->editColumn('name', function($records) {
            return $records->name ;
        })
        ->editColumn('email', function($records) {
            return $records->email;
        })
        ->editColumn('phone', function($records) {
            return $records->phone;
        })
        ->removeColumn('id')
        ->removeColumn('classes_user_id')
        ->removeColumn('updated_at');
        return $table->make();
    }
}
