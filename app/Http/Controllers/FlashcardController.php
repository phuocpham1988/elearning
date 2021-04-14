<?php
namespace App\Http\Controllers;

use App\Flashcard;
use App\FlashcardDetail;
use DB;
use Exception;
use File;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use \App;
use App\LmsContent;

class FlashcardController extends Controller
{
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

        // echo $lms_content_after_after = LmsContent::find(16137)->parent_id;
        // dd($lms_content_after_after);
        // exit;
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }

        $data['active_class'] = 'flashcard';
        $data['title']        = 'Flashcard';
        $view_name            = getTheme() . '::lms.flashcard.list';
        return view($view_name, $data);
    }
    /**
     * This method returns the datatables data to view
     * @return [type] [description]
     */
    public function getDatatable(Request $request)
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }
        $records = Flashcard::select([
            'id', 'name', 'slug'])
            ->orderBy('updated_at', 'desc');
        $table = Datatables::of($records)
            ->addColumn('action', function ($records) {
                return '<div class="dropdown more">
                        <a id="dLabel" type="button" class="more-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dLabel">
                        <li><a href="' . '/lms/flashcard/view/' . $records->id . '"><i class="fa fa-eye"></i>Xem chi tiết</a></li>
                            <li><a href="' . '/lms/flashcard/edit/' . $records->id . '"><i class="fa fa-plus-circle"></i>Chỉnh sửa</a></li>
                        </ul>
                    </div>';
            })
            ->editColumn('subject_title', function ($records) {
                return '<a href="' . URL_QUESTIONBANK_VIEW . $records->slug . '">' . $records->subject_title . '</a>';
            })
            ->removeColumn('id')
            ->removeColumn('slug')
        ;
        return $table->make();
    }
    /**
     * Questions listing method
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function show($slug)
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }
        $flashcard = Flashcard::find($slug);

        // dd($flashcard);
        $data['active_class'] = 'flashcard';
        $data['title']        = $flashcard->name;
        $data['flashcard']    = $flashcard;
        $view_name            = getTheme() . '::lms.flashcard.show';
        return view($view_name, $data);
    }
    /**
     * This method returns the datatables data to view
     * @return [type] [description]
     */
    public function getFlashcard($slug)
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }
        $subject = Flashcard::find($slug);

        $records = FlashcardDetail::select(['id', 'flashcard_id', 'm1tuvung', 'm1vidu', 'm2cachdoc', 'm2amhanviet', 'm2ynghia', 'm2vidu', 'stt', 'mp3'])
            ->where('flashcard_id', $slug)
            ->orderBy('id');
        $table = Datatables::of($records)
            ->addColumn('action', function ($records) {
                return '<div class="dropdown more">
                        <a id="dLabel" type="button" class="more-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dLabel">
                       <li><a href="/lms/flashcard-detail/edit/' . $records->id . '"><i class="fa fa-pencil"></i>' . getPhrase("edit") . '</a></li>
                       <li><a href="javascript:void(0);" onclick="deleteRecord(\'' . $records->id . '\');"><i class="fa fa-trash"></i>' . getPhrase("delete") . '</a></li>
                        </ul>
                    </div>';
            })
            ->removeColumn('id')
            ->removeColumn('flashcard_id')
        ;
        return $table->make();
    }
    /**
     * This method loads the create view
     * @return void
     */

    public function createdetail($slug)
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }
        $flashcard = Flashcard::find($slug);

        $data['record']       = false;
        $data['active_class'] = 'flashcard';
        $data['title']        = 'Thêm Flashcard';
        $data['flashcard']    = $flashcard;

        $view_name = getTheme() . '::lms.flashcard.detail.add-edit';
        return view($view_name, $data);
    }

    public function editdetail($slug)
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }
        $record    = FlashcardDetail::find($slug);
        $flashcard = FlashcardDetail::find($record->flashcard_id);

        $data['record']       = $record;
        $data['active_class'] = 'flashcard';
        $data['title']        = 'Thêm Flashcard';
        $data['flashcard']    = $flashcard;
        // $data['settings']      = json_encode($settings);
        // return view('exams.questionbank.add-edit', $data);
        $view_name = getTheme() . '::lms.flashcard.detail.add-edit';
        return view($view_name, $data);
    }

    public function updatedetail(Request $request, $slug)
    {

        $nameTrans   = time() . '.mp3';
        $path_upload = public_path() . '/uploads/flashcard/';

        $record              = FlashcardDetail::find($slug);
        $record->m1tuvung    = $request->m1tuvung;
        $record->m1vidu      = $request->m1vidu;
        $record->m2cachdoc   = $request->m2cachdoc;
        $record->m2amhanviet = $request->m2amhanviet;
        $record->m2ynghia    = $request->m2ynghia;
        $record->m2vidu      = $request->m2vidu;
        $record->mp3         = $nameTrans;
        $record->save();

        $textToSpeechClient = new TextToSpeechClient();
        $input              = new SynthesisInput();
        $input->setText($request->m1tuvung);
        $voice = new VoiceSelectionParams();
        $voice->setLanguageCode('ja-JP-Wavenet-A');
        $audioConfig = new AudioConfig();
        $audioConfig->setAudioEncoding(AudioEncoding::MP3);
        $resp = $textToSpeechClient->synthesizeSpeech($input, $voice, $audioConfig);
        file_put_contents($path_upload . $nameTrans, $resp->getAudioContent());

        flash('success', 'Cập nhật thành công', 'success');
        return redirect('/lms/flashcard/view/' . $request->flashcard_id);

        //dd($request);
    }

    public function storedetail(Request $request)
    {

        $nameTrans   = time() . '.mp3';
        $path_upload = public_path() . '/uploads/flashcard/';

        $record               = new FlashcardDetail();
        $record->flashcard_id = $request->flashcard_id;
        $record->m1tuvung     = $request->m1tuvung;
        $record->m1vidu       = $request->m1vidu;
        $record->m2cachdoc    = $request->m2cachdoc;
        $record->m2amhanviet  = $request->m2amhanviet;
        $record->m2ynghia     = $request->m2ynghia;
        $record->m2vidu       = $request->m2vidu;
        $record->mp3          = $nameTrans;
        $record->save();

        $textToSpeechClient = new TextToSpeechClient();
        $input              = new SynthesisInput();
        $input->setText($request->m1tuvung);
        $voice = new VoiceSelectionParams();
        $voice->setLanguageCode('ja-JP-Wavenet-A');
        $audioConfig = new AudioConfig();
        $audioConfig->setAudioEncoding(AudioEncoding::MP3);
        $resp = $textToSpeechClient->synthesizeSpeech($input, $voice, $audioConfig);
        file_put_contents($path_upload . $nameTrans, $resp->getAudioContent());

        flash('success', 'Thêm Flashcard thành công', 'success');
        return redirect('/lms/flashcard/view/' . $request->flashcard_id);

        //dd($request);
    }

    public function create()
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }
        //$flashcard = Flashcard::find($slug);
        $data['record']       = false;
        $data['active_class'] = 'flashcard';
        $data['title']        = 'Thêm Flashcard';
        $data['flashcard']    = '';
        $view_name            = getTheme() . '::lms.flashcard.add-edit';
        return view($view_name, $data);
    }
    
    public function edit($slug)
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }

        $record    = Flashcard::find($slug);
        $data['record']       = $record;
        $data['active_class'] = 'flashcard';
        $data['title']        = 'Chỉnh sửa Flashcard';
        $view_name = getTheme() . '::lms.flashcard.add-edit';
        return view($view_name, $data);
        
    }
    
    public function update(Request $request, $slug)
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }

        $record              = Flashcard::find($slug);
        $record->name    = $request->name;
        $record->save();

        flash('success', 'Cập nhật thành công', 'success');
        return redirect('/lms/flashcard/');


        
    }


    public function store(Request $request)
    {
        
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }

        $record = new Flashcard();
        $record->name = $request->name;
        $record->save();

        flash('', 'Thêm Flashcard thành công', 'success');
        return redirect('/lms/flashcard');
        
    }
    /**
     * Delete Record based on the provided slug
     * @param  [string] $slug [unique slug]
     * @return Boolean
     */
    public function delete($slug)
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }
        $qbObject = new QuestionBank();
        $record   = $qbObject->getRecordWithSlug($slug);
        try {
            if (!env('DEMO_MODE')) {
                $path    = (new App\ImageSettings())->getExamImagePath();
                $options = json_decode($record->answers);
                $this->deleteExamFile($options, $path, true);
                $this->deleteExamFile($record->question_file, $path, false);
                $record->delete();
            }
            $response['status']  = 1;
            $response['message'] = getPhrase('record_deleted_successfully');
            return json_encode($response);
        } catch (\Illuminate\Database\QueryException $e) {
            $response['status'] = 0;
            if (getSetting('show_foreign_key_constraint', 'module')) {
                $response['message'] = $e->errorInfo;
            } else {
                $response['message'] = getPhrase('this_record_is_in_use_in_other_modules');
            }

        }
        return json_encode($response);
    }
    public function isValidRecord($record)
    {
        if ($record === null) {
            flash('Ooops...!', getPhrase("page_not_found"), 'error');
            return $this->getRedirectUrl();
        }
        return false;
    }
}
