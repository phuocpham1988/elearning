<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\EmailTemplate;
use Yajra\Datatables\Datatables;
use DB;
use Auth;
class EmailTemplatesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
     /**
     * Course listing method
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function thongbaothithu(Request $request)
    {   

    	$now_date = \Carbon\Carbon::today()->toDateString();

    	
        $users = DB::table('test')
        					->whereDate('sendmail', '<', date('Y-m-d'))
        					->orWhereNull('sendmail')
        					->skip(0)->take(4)->orderBy('id','desc')->get();

        foreach ($users as $key => $value) {
        	DB::table('test')->where('id', '=', $value->id)->update([
           			'sendmail' => $now_date
        	]);
        }

        sendEmail('thongbaothithu', array('name'=>'Phuoc', 'to_email' => 'phuocpham1988@gmail.com'));

        echo "<pre>";
        print_r ($users);
        echo "</pre>";

        ///home/hael/
        //sendEmail('thongbaothithu', array('name'=>'Phuoc', 'to_email' => 'phuocpham1988@gmail.com'));

       /* $skip = 0;
        if ($request->lan) {
            if ($request->lan <= 1) {
                $skip = 0;    
            } else {
                $lan =  $request->lan;
                $skip = (($lan-1) * 40) + ($lan);
            }


            $users = DB::table('users')->where('is_register', '=', 1)->skip($skip)->take(40)->orderBy('id','desc')->get();
            foreach ( $users as $key => $value) {
	            try{
	               sendEmail('thongbaothithu', array('name'=>$value->name, 'to_email' => $value->email));
	                
	            }
	            catch(Exception $ex)
	            {
	                  $message = $ex->getMessage();
	                  exit;
	            }
      		}
            echo 'Gửi 40 email thành công từ ' . $skip;
        }*/
        exit;
        //sendEmail('thongbaothithu', array('name'=>'Phuoc', 'to_email' => 'vampirephp1@gmail.com'));

      //$users = DB::table('users')->where('is_register', '=', 1)->skip(370)->take(30)->orderBy('id','desc')->get();

      /*foreach ( $users as $key => $value) {
            try{
               sendEmail('thongbaothithu', array('name'=>$value->name, 'to_email' => $value->email));
                
            }
            catch(Exception $ex)
            {
                  $message = $ex->getMessage();
                  exit;
            }
      }
      echo 'Gửi 50 email thành công';*/
      
    }
    public function index()
    {
        if(!checkRole(getUserGrade(2)))
      {
        prepareBlockUserMessage();
        return back();
      }

      // echo "<pre>";
      //   print_r ($_SERVER);
      //   echo "</pre>"; exit;

        
        $data['active_class']       = 'master_settings';
        $data['title']              = getPhrase('email_templates');
        // return view('emails.templates.list', $data);
         $view_name = getTheme().'::emails.templates.list';
        return view($view_name, $data);
    }
    /**
     * This method returns the datatables data to view
     * @return [type] [description]
     */
    public function getDatatable()
    {
        if(!checkRole(getUserGrade(2)))
      {
        prepareBlockUserMessage();
        return back();
      }
         $records = EmailTemplate::select([   
            'title', 'subject', 'type', 'from_email', 'from_name', 'id','slug'])
         ->orderBy('updated_at', 'DESC');
        return Datatables::of($records)
        ->addColumn('action', function ($records) {
            return '<div class="dropdown more">
                        <a id="dLabel" type="button" class="more-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dLabel">
                            <li><a href="'.URL_EMAIL_TEMPLATES_EDIT.'/'.$records->slug.'"><i class="fa fa-pencil"></i>'.getPhrase("edit").'</a></li>
                        </ul>
                    </div>';
            })
        ->removeColumn('id')
        ->removeColumn('slug')
        ->make();
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
        $data['record']             = FALSE;
        $data['active_class']       = 'master_settings';
        $data['title']              = getPhrase('create_template');
        // return view('emails.templates.add-edit', $data);
         $view_name = getTheme().'::emails.templates.add-edit';
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
        $record = EmailTemplate::getRecordWithSlug($slug);
        if($isValid = $this->isValidRecord($record))
            return redirect($isValid);
        $data['record']             = $record;
        $data['active_class']       = 'master_settings';
        $data['title']              = getPhrase('edit_template');
        // return view('emails.templates.add-edit', $data);
           $view_name = getTheme().'::emails.templates.add-edit';
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
        $record = EmailTemplate::getRecordWithSlug($slug);
         $rules = [
         'title'               => 'bail|required|max:130' ,
         'subject'             => 'bail|required|max:130' ,
         'from_email'          => 'bail|email|required|max:130' ,
         'from_name'           => 'bail|required|max:130' ,
            ];
         /**
        * Check if the title of the record is changed, 
        * if changed update the slug value based on the new title
        */
       $name = $request->title;
        if($name != $record->title)
            $record->slug = $record->makeSlug($name);
       //Validate the overall request
        $this->validate($request, $rules);
        $record->title              = $name;
        $record->content            = $request->content;
        $record->subject            = $request->subject;
        $record->from_email         = $request->from_email;
        $record->from_name          = $request->from_name;
        $record->record_updated_by  = Auth::user()->id;
        $record->save();
        flash('success','record_updated_successfully', 'success');
        return redirect(URL_EMAIL_TEMPLATES);
    }
    /**
     * This method adds record to DB
     * @param  Request $request [Request Object]
     * @return void
     */
    public function store(Request $request)
    {
        if(!checkRole(getUserGrade(2)))
      {
        prepareBlockUserMessage();
        return back();
      }
        $rules = [
         'title'               => 'bail|required|max:130' ,
         'subject'             => 'bail|required|max:130' ,
         'from_email'          => 'bail|email|required|max:130' ,
         'from_name'           => 'bail|required|max:130' ,
         'content'             => 'bail|required' ,
            ];
        $this->validate($request, $rules);
        $record = new EmailTemplate();
        $name                       =  $request->title;
        $record->title              = $name;
        $record->slug               = $record->makeSlug($name);
        $record->content            = $request->content;
        $record->type               = $request->type;
        $record->subject            = $request->subject;
        $record->from_email         = $request->from_email;
        $record->from_name          = $request->from_name;
        $record->record_updated_by  = Auth::user()->id;
        $record->save();
        flash('success','record_added_successfully', 'success');
        return redirect(URL_EMAIL_TEMPLATES);
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
        return URL_EMAIL_TEMPLATES;
    }
}
