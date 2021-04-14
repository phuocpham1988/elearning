<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CURLFile;
use App\LmsContent;
use Auth;
use DB;
use Excel;
use Exception;
use File;
use Illuminate\Http\Request;
use Image;
use ImageSettings;
use Yajra\Datatables\Datatables;
use \App;

class LmsContentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected $examSettings;

    public function setSettings()
    {
        $this->examSettings = getSettings('lms');
    }

    public function getSettings()
    {
        return $this->examSettings;
    }

    /**
     * Course listing method
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function index($series)
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }

        $data['series_slug']         = $series;
        $data['URL_LMS_CONTENT_ADD'] = PREFIX . "lms/$series/content/add";
        $data['URL_IMPORT_MUCLUC']   = PREFIX . "lms/$series/content/import-mucluc";
        $data['URL_IMPORT_EXAMS']    = PREFIX . "lms/$series/content/import-excel";
        $data['datatbl_url']         = PREFIX . "lms/$series/content/getList/";
        $data['active_class']        = 'lms';
        $data['title']               = 'LMS' . ' ' . getPhrase('content');
        $data['layout']              = getLayout();
        // return view('lms.lmscontents.list', $data);

        $view_name = getTheme() . '::lms.lmscontents.list';
        return view($view_name, $data);
    }

    /**
     * This method returns the datatables data to view
     * @return [type] [description]
     */
    public function getDatatable($slug)
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }

        $records = DB::table('lmscontents')
            ->select(['lmscontents.stt', 'lmscontents.bai', 'lmscontents.title',
                'lmscontents.id', 'lmscontents.type', 'lmscontents.import', 'lmscontents.file_path', 'lmscontents.el_try'])
            ->join('lmsseries', 'lmsseries.id', '=', 'lmscontents.lmsseries_id')
            ->where([
                ['lmsseries.slug', $slug],
                ['lmscontents.delete_status', 0],
            ])
            ->orderBy('stt')
            ->get();
        // dd($records);
        $this->setSettings();
        return Datatables::of($records)
            ->addColumn('hocthu', function ($records) {
                if (in_array($records->type, ['1', '2', '6', '9', '3', '4', '5'])) {
                    $extra = '<div class="form-check text-center">
                      <input ' . ($records->el_try == 1 ? 'checked' : '') . ' class="form-check-input" onclick="update_try(' . $records->id . ',' . $records->el_try . ')" type="checkbox" style="display: inline-block; width: 20px;height: 20px;" value="" id="flexCheckDefault">

                    </div>';

                } else {
                    $extra = '';
                }
                return $extra;
            })
            ->addColumn('action', function ($records) {
                $extra = '<div class="dropdown more">
                            <a id="dLabel" type="button" class="more-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <i class="mdi mdi-dots-vertical"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dLabel">';

                                if (in_array($records->type, ['3', '4', '5'])) {
                                    $extra .= '<li><a href="' . 'content/view/' . $records->id . '"><i class="fa fa-eye"></i>' . getPhrase("view") . '</a></li>';
                                }

                $extra .= '<li><a href="' . 'content/edit/' . $records->id . '"><i class="fa fa-pencil"></i>' . getPhrase("edit") . '</a></li>';
                $extra .= '<li><a href="' . 'content/add/after/' . $records->id . '"><i class="fa fa-plus"></i>Thêm vào sau</a></li>';
                if (checkRole(getUserGrade(2))) {
                    $extra .= '<li><a href="javascript:void(0);" onclick="deleteRecord(\'' . $records->id . '\');"><i class="fa fa-trash"></i>' . getPhrase("delete") . '</a></li>';
                }

                $extra .= '</ul></div>';
                return $extra;
            })
            ->editColumn('title', function ($records) {
                return "<a href='" . 'content/edit/' . $records->id . "'>" . $records->bai . ' ' . $records->title . "</a>";
            })
            ->editColumn('import', function ($records) {
                if (in_array($records->type, ['1', '2', '6', '9'])) {
                    return ($records->file_path != null) ? '<span class="label label-success">Đã import video</span> ' : '<span class="label label-warning">Chưa import video</span>';
                }

                if (in_array($records->type, ['2', '3', '4'])) {
                    $check = DB::table('lms_exams')
                        ->select('id')
                        ->where('content_id', $records->id)
                        ->get();
                    return (!$check->isEmpty()) ? '<span class="label label-success">Đã có bài tâp</span> ' : '<span class="label label-warning">Chưa có bài tập</span>';
                }
                if (in_array($records->type, ['5'])) {
                    $check = DB::table('lms_test')
                        ->select('id')
                        ->where('content_id', $records->id)
                        ->get();
                    return (!$check->isEmpty()) ? '<span class="label label-success">Đã có bài test</span> ' : '<span class="label label-warning">Chưa có bài test</span>';
                }
                return null;
                // if(in_array($records->type,['8']))
                // return
                // (in_array($records->type,['8']) ?($records->import == 1 ? '<span class="label label-success">Đã import bài tâp</span> ' : '<span class="label label-warning">Chưa import bài tâp</span>') : null);
            })
            ->editColumn('type', function ($records) {
                $dr_loai = ['0' => 'Menu', '1' => 'Từ vựng', '2' => 'Bài học', '3' => 'Bài tập', '4' => 'Bài tập toàn bài', '5' => 'Bài test', '6' => 'Hán tự', '7' => 'Bài ôn tập', '8' => 'Sub menu', '9' => 'Giới thiệu'];
                return $dr_loai[$records->type];
            })

            ->removeColumn('bai')
            ->removeColumn('file_path')
            ->removeColumn('id')
            ->removeColumn('slug')
            ->removeColumn('series_slug')
            ->removeColumn('el_try')
        // ->editColumn('image', function($records){
        //   $image_path = IMAGE_PATH_UPLOAD_LMS_DEFAULT;
        //
        //   if($records->image)
        //   $image_path = IMAGE_PATH_UPLOAD_LMS_CONTENTS.$records->image;
        //
        //   return '<img src="'.$image_path.'" height="100" width="100" />';
        // })
            ->make();
    }

    /**
     * This method loads the create view
     * @return void
     */

    public function createAfter($series, $slug)
    { 
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }

        $lsmContentAfter = LmsContent::find($slug);
        $data['lms_content_after']         = $lsmContentAfter;
        //$data['lms_content_after_bai']         = $lsmContentAfter->bai;


        //dd($lsmContent);

        $list             = DB::table('lms_flashcard')->get();
        $flashcard         = array_pluck($list, 'name', 'id');
        // dd($flashcard);
        $data['flashcard'] = array(''=>'-- Chọn Flashcard --') + $flashcard;

        $data['URL_LMS_CONTENT_ADD'] = PREFIX . "lms/$series/content/add";
        $data['URL_LMS_CONTENT']     = PREFIX . "lms/$series/content";
        $data['series_slug']         = $series;
        $data['record']              = false;
        $data['active_class']        = 'lms';



        $data['title']               = getPhrase('add_content');
        $data['layout']              = getLayout();

        // return view('lms.lmscontents.add-edit', $data);
        $view_name = getTheme() . '::lms.lmscontents.add-edit-after';
        return view($view_name, $data);
    }

    public function storeAfter(Request $request)
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }

        $file_path = '';

        
        //die();

        DB::beginTransaction();
        try {
            $lmsseries_id_q = DB::table('lmsseries')
                ->select('id')
                ->where('slug', $request->series_slug)
                ->get()->first();

            $lms_content_after_id = $request->lms_content_after_id;

            $lms_content_after = LmsContent::find($lms_content_after_id);

            //update stt + 1
            LmsContent::where('lmsseries_id', $lmsseries_id_q->id)
              ->where('stt','>', $lms_content_after->stt)
              ->increment('stt');

            // dd($lms_content_after);
            $getStt =   $lms_content_after->stt + 1;

            switch ($request->loai) {
              case 0:
                $parent_id = null; //Menu
                break;
              case 1:
                if( $lms_content_after->type == 8) {
                  $parent_id = $lms_content_after_id;
                } else {
                  $parent_id = $lms_content_after->parent_id;
                }
                break;
              case 2:
                if( $lms_content_after->type == 8) {
                  $parent_id = $lms_content_after_id;
                } else {
                  $parent_id = $lms_content_after->parent_id;
                }
                break;
              case 3:
                if( $lms_content_after->type == 8) {
                  $parent_id = $lms_content_after_id;
                } else {
                  $parent_id = $lms_content_after->parent_id;
                }
                break;
              case 4:
                if( $lms_content_after->type == 8) {
                  $parent_id = $lms_content_after_id;
                } else {
                  $parent_id = $lms_content_after->parent_id;
                }
                break;
              case 5:
                $parent_id = null;
                break;
              case 6:
                if( $lms_content_after->type == 8) {
                  $parent_id = $lms_content_after_id;
                } else {
                  $parent_id = $lms_content_after->parent_id;
                }
                break;
              case 7:
                $parent_id = null;
                break;
              case 8:
                if ($lms_content_after->type == 0 || $lms_content_after->type == 8) {
                  $parent_id = $lms_content_after_id;
                } else {
                  $lms_content_after_after = LmsContent::find($lms_content_after->parent_id)->parent_id;
                  $parent_id = $lms_content_after_after;
                } 
                break;
              case 9:
                $parent_id = null;
                break;
              case 10:
                if( $lms_content_after->type == 8) {
                  $parent_id = $lms_content_after_id;
                } else {
                  $parent_id = $lms_content_after->parent_id;
                }
                break;
              default:
                $parent_id = null; //Submenu
                break;
            }

            //dd ($request); exit;

            //insert bai hoc
            $record               = new LmsContent();
            $name                 = $request->title;
            $slug_insert          = $record->makeSlug($name, true);
            $record->title        = $name;
            $record->slug         = $slug_insert;
            $record->parent_id    = $parent_id;
            $record->flashcard_id = $request->flashcard_id;
            $record->lmsseries_id = $lmsseries_id_q->id;
            $record->bai          = $request->bai;
            $record->type         = $request->loai;
            $record->maucau       = $request->maucau;
            $record->content_type = 'url';
            $record->stt          = $getStt;
            $record->file_path    = $request->file_path;

            $record->description  = '';
            $record->created_by   = Auth::user()->id;
            $record->save();
            DB::table('lmsseries_data')->insert([
                [
                    'lmsseries_id'  => $lmsseries_id_q->id,
                    'lmscontent_id' => $record->id,
                ],
            ]);

            # import video
            $file_name = 'lms_file';
            if ($request->hasFile($file_name)) {
                // $slug_inser = folder name ( id + random string )
                $slug_insert = $record->id . '-' . rand(100000000, 9999999999);
                $this->setSettings();
                $examSettings = $this->getSettings();
                $path         = $examSettings->contentImagepath;
                $this->deleteFile($record->file_path, $path);
                $file_video_name = $this->processUpload($request, $record, $file_name, false);
                $realpath_save   = public_path() . '/uploads/lms/content/' . $file_video_name;

                if (!file_exists(public_path() . '/uploads/lms/content/' . $slug_insert)) {
                    mkdir(public_path() . '/uploads/lms/content/' . $slug_insert);
                }
                // send stream video
                $data = array(
                    'upload'        => 1,
                    'file_contents' => new \CURLFile($realpath_save),
                    'path'          => $slug_insert,
                );
                try {
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        // curlopt url of dev.hikariacademy.edu.vn api end point
                        CURLOPT_URL            => "http://dev.hikariacademy.edu.vn/stream-video/api.php/hls",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_CUSTOMREQUEST  => "POST",
                        CURLOPT_HTTPHEADER     => array('Content-Type: multipart/form-data'),
                        CURLOPT_POSTFIELDS     => $data,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_SSL_VERIFYPEER => false,
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);
                    if ($response === false) {
                        throw new Exception(curl_error($curl), curl_errno($curl));
                    } else {
                        // url of zip file in dev.hikariacademy.edu.vn
                        $url     = 'http://dev.hikariacademy.edu.vn/stream-video/zip/' . $slug_insert . '/' . $slug_insert . '.zip';
                        $zipFile = public_path() . '/uploads/lms/content/' . $slug_insert . '/video.zip';
                        file_put_contents($zipFile, fopen($url, 'r'));
                        $zip         = new \ZipArchive;
                        $extractPath = public_path() . '/uploads/lms/content/' . $slug_insert;
                        if ($zip->open($zipFile) != "true") {
                            flash('error', 'record_added_successfully', 'error');
                        }
                        $zip->extractTo($extractPath);
                        $zip->close();
                    }
                } catch (Exception $e) {
                    dd($e);
                }

                $record->file_path = '/public/uploads/lms/content/' . $slug_insert . '/video.m3u8';
                $record->import    = '1';
                $record->save();
                @unlink($zipFile);
                @unlink($realpath_save);
            }
            # end import video

            # import bai tap mau cau
            if ($request->hasFile('lms_excel')) {
                $path = $request->file('lms_excel')->getRealPath();
                config(['excel.import.startRow' => 2]);
                $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
                    $reader->noHeading();
                })->get();
                // dd($data);
                if (!empty($data) && $data->count()) {
                    $content_update = DB::table('lmscontents')
                        ->select(['id', 'maucau'])
                        ->where([
                            ['parent_id', $record->id],
                            ['maucau', '<>', null],
                        ])->get();
                    $dr_update = [];
                    foreach ($content_update as $r) {
                        $dr_update[$r->maucau] = $r->id;
                        DB::table('lms_exams')
                            ->where([
                                ['content_id', $r->id],
                            ])
                            ->update(['delete_status' => 1]);
                    }
                    $ignoreHeading = 'label';
                    DB::beginTransaction();
                    foreach ($dr_update as $r) {
                        DB::table('lms_exams')
                            ->where('content_id', $r)
                            ->update([
                                'delete_status' => '1',
                            ]);
                    }
                    try {
                        foreach ($data as $r) {
                            $bai    = (int) filter_var($r[0], FILTER_SANITIZE_NUMBER_INT);
                            $maucau = (int) filter_var($r[1], FILTER_SANITIZE_NUMBER_INT);
                            if (0 === 1) {
                                DB::rollBack();
                                flash('oops...!', 'Import bài tập sai bài', 'error');
                            }
                            if (isset($dr_update[$maucau])) {
                                if ($r[2] != $ignoreHeading) {
                                    $check = DB::table('lms_exams')->insertGetId([
                                        'content_id' => $dr_update[$maucau],
                                        'label'      => $r[2],
                                        'dang'       => $r[3],
                                        'cau'        => $r[4],
                                        'mota'       => (string) $r[5],
                                        'luachon1'   => $r[6],
                                        'luachon2'   => $r[7],
                                        'luachon3'   => $r[8],
                                        'luachon4'   => $r[9],
                                        'dapan'      => $r[10],
                                        'created_by' => Auth::id(),
                                    ]);
                                }
                            }
                        }
                        DB::commit();
                    } catch (Exception $e) {
                        DB::rollBack();
                        flash('oops...!', 'opps..........!!!', 'error');
                        // dd($e);
                    }
                    $record->import = '1';
                    $record->save();
                }
            }
            # end import bai tap mau cau

            # import bai test
            if ($request->hasFile('lms_test')) {
                $path = $request->file('lms_test')->getRealPath();
                config(['excel.import.startRow' => 2]);
                $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
                    $reader->noHeading();
                })->get();
                if (!empty($data) && $data->count()) {
                    $ignoreHeading = 'label';
                    DB::beginTransaction();
                    DB::table('lms_test')
                        ->where('content_id', $record->id)
                        ->update([
                            'delete_status' => 1,
                        ]);
                    try {
                        foreach ($data as $r) {
                            if ($r[0] != $ignoreHeading && $r[1] != null && $r[2] != null) {
                                $check = DB::table('lms_test')->insertGetId([
                                    'content_id'   => $record->id,
                                    'dang'         => $r[1],
                                    'cau'          => $r[2],
                                    'mota'         => $r[3],
                                    'luachon1'     => $r[4],
                                    'luachon2'     => $r[5],
                                    'luachon3'     => $r[6],
                                    'luachon4'     => $r[7],
                                    'dapan'        => $r[8],
                                    'diem'         => $r[9],
                                    'content_type' => '5',
                                    'created_by'   => Auth::id(),
                                ]);
                            }
                        }
                        DB::commit();
                    } catch (Exception $e) {
                        DB::rollBack();
                        // dd($e);
                    }
                    $record->import = '1';
                    $record->save();
                }
            }
            # end import bai test

            # import bai tap loai 4
            if ($request->hasFile('lms_type_4')) {
                $path = $request->file('lms_type_4')->getRealPath();
                config(['excel.import.startRow' => 2]);
                $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
                    $reader->noHeading();
                })->get();
                // dd($data);
                if (!empty($data) && $data->count()) {
                    try {
                        DB::beginTransaction();
                        DB::table('lms_exams')
                            ->where('content_id', $record->id)
                            ->update([
                                'delete_status' => '1',
                            ]);
                        foreach ($data as $r) {
                            if ($r[1] != null && $r[1] != '') {
                                $check = DB::table('lms_exams')->insertGetId([
                                    'content_id' => $record->id,
                                    'dang'       => $r[1],
                                    'cau'        => $r[2],
                                    'mota'       => $r[3],
                                    'luachon1'   => $r[4],
                                    'luachon2'   => $r[5],
                                    'luachon3'   => $r[6],
                                    'luachon4'   => $r[7],
                                    'dapan'      => $r[8],
                                    'created_by' => Auth::id(),
                                ]);
                            }
                        }
                        DB::commit();
                    } catch (Exception $e) {
                        DB::rollBack();
                        flash('oops...!', 'opps..........!!!', 'error');
                        dd($e);
                    }
                    $record->import = '1';
                    $record->save();
                }
            }
            # end import bai tap loai 4

            DB::commit();
            flash('Thêm bài học thành công', '', 'success');
        } catch (Exception $e) {
            dd($e);
            DB::rollBack();
            if (getSetting('show_foreign_key_constraint', 'module')) {
                flash('Lỗi', $e->errorInfo, 'error');
            } else {
                flash('Lỗi', 'improper_data_file_submitted', 'error');
            }
        }
        return redirect(PREFIX . "lms/" . $request->series_slug . '/content');
    }

    public function create($series)
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }

        $data['URL_LMS_CONTENT_ADD'] = PREFIX . "lms/$series/content/add";
        $data['URL_LMS_CONTENT']     = PREFIX . "lms/$series/content";
        $data['series_slug']         = $series;
        $data['record']              = false;
        $data['active_class']        = 'lms';
        $data['title']               = getPhrase('add_content');
        $data['layout']              = getLayout();

        // return view('lms.lmscontents.add-edit', $data);
        $view_name = getTheme() . '::lms.lmscontents.add-edit';
        return view($view_name, $data);
    }

    /**
     * This method loads the edit view based on unique slug provided by user
     * @param  [string] $slug [unique slug of the record]
     * @return [view with record]
     */
    public function edit($series, $slug)
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }
        $record                       = LmsContent::getRecordWithId($slug);
        $data['URL_LMS_CONTENT_EDIT'] = PREFIX . "lms/$series/content/edit/" . $slug;
        $data['URL_LMS_CONTENT']      = PREFIX . "lms/$series/content";
        $data['series_slug']          = $series;
        $data['record']               = $record;
        $data['title']                = 'Cập nhật ' . $record->bai;
        $data['active_class']         = 'lms';
        $data['settings']             = json_encode($record);
        $data['layout']               = getLayout();
        // return view('lms.lmscontents.add-edit', $data);
        $view_name = getTheme() . '::lms.lmscontents.add-edit';
        return view($view_name, $data);
    }

    /**
     * Update record based on slug and reuqest
     * @param  Request $request [Request Object]
     * @param  [type]  $slug    [Unique Slug]
     * @return void
     */
    public function update(Request $request, $series, $slug)
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }
        $record    = LmsContent::getRecordWithId($slug);
        $file_path = $record->file_path;
        DB::beginTransaction();
        try {
            $name = $request->title;
            if ($name != $record->title) {
                $slug_insert  = $record->makeSlug($name, true);
                $record->slug = $slug_insert;
            } else {
                $slug_insert = $slug;
            }
            $name                      = $request->title;
            $record->title             = $name;
            $record->bai               = $request->bai;
            $record->type              = $request->loai;
            $record->maucau            = $request->maucau;
            $record->video_duration    = $request->video_duration;
            $record->stt               = $request->stt;
            $record->file_path         = $file_path;
            $record->description       = $request->description;
            $record->record_updated_by = Auth::user()->id;
            $record->save();

            $file_name = 'image';
            if ($request->hasFile($file_name)) {
                $rules = array($file_name => 'mimes:jpeg,jpg,png,gif|max:10000');
                $this->validate($request, $rules);
                $this->setSettings();
                $examSettings = $this->getSettings();
                $path         = $examSettings->contentImagepath;
                $this->deleteFile($record->image, $path);
                $record->image = $this->processUpload($request, $record, $file_name);
                $record->save();
            }

            # import video
            $file_name = 'lms_file';
            if ($request->hasFile($file_name)) {
                // $slug_inser = folder name ( id + random string )
                $slug_insert = $record->id . '-' . rand(100000000, 9999999999);
                $this->setSettings();
                $examSettings = $this->getSettings();
                $path         = $examSettings->contentImagepath;
                $this->deleteFile($record->file_path, $path);
                $file_video_name = $this->processUpload($request, $record, $file_name, false);
                $realpath_save   = public_path() . '/uploads/lms/content/' . $file_video_name;

                if (!file_exists(public_path() . '/uploads/lms/content/' . $slug_insert)) {
                    mkdir(public_path() . '/uploads/lms/content/' . $slug_insert);
                }
                // send stream video
                $data = array(
                    'upload'        => 1,
                    'file_contents' => new \CURLFile($realpath_save),
                    'path'          => $slug_insert,
                );
                try {
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        // curlopt url of dev.hikariacademy.edu.vn api end point
                        CURLOPT_URL            => "http://dev.hikariacademy.edu.vn/stream-video/api.php/hls",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_CUSTOMREQUEST  => "POST",
                        CURLOPT_HTTPHEADER     => array('Content-Type: multipart/form-data'),
                        CURLOPT_POSTFIELDS     => $data,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_SSL_VERIFYPEER => false,
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);
                    if ($response === false) {
                        throw new Exception(curl_error($curl), curl_errno($curl));
                    } else {
                        // url of zip file in dev.hikariacademy.edu.vn
                        $url     = 'http://dev.hikariacademy.edu.vn/stream-video/zip/' . $slug_insert . '/' . $slug_insert . '.zip';
                        $zipFile = public_path() . '/uploads/lms/content/' . $slug_insert . '/video.zip';
                        file_put_contents($zipFile, fopen($url, 'r'));
                        $zip         = new \ZipArchive;
                        $extractPath = public_path() . '/uploads/lms/content/' . $slug_insert;
                        if ($zip->open($zipFile) != "true") {
                            flash('error', 'record_added_successfully', 'error');
                        }
                        $zip->extractTo($extractPath);
                        $zip->close();
                    }
                } catch (Exception $e) {
                    dd($e);
                }

                $record->file_path = '/public/uploads/lms/content/' . $slug_insert . '/video.m3u8';
                $record->import    = '1';
                $record->save();
                @unlink($zipFile);
                @unlink($realpath_save);
            }
            # end import video

            # import bai tap mau cau
            if ($request->hasFile('lms_excel')) {
                $path = $request->file('lms_excel')->getRealPath();
                config(['excel.import.startRow' => 2]);
                $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
                    $reader->noHeading();
                })->get();
                // dd($data);
                if (!empty($data) && $data->count()) {
                    $content_update = DB::table('lmscontents')
                        ->select(['id', 'maucau'])
                        ->where([
                            ['parent_id', $record->id],
                            ['maucau', '<>', null],
                        ])->get();
                    $dr_update = [];
                    foreach ($content_update as $r) {
                        $dr_update[$r->maucau] = $r->id;
                        DB::table('lms_exams')
                            ->where([
                                ['content_id', $r->id],
                            ])
                            ->update(['delete_status' => 1]);
                    }
                    $ignoreHeading = 'label';
                    DB::beginTransaction();
                    foreach ($dr_update as $r) {
                        DB::table('lms_exams')
                            ->where('content_id', $r)
                            ->update([
                                'delete_status' => '1',
                            ]);
                    }
                    try {
                        foreach ($data as $r) {
                            $bai    = (int) filter_var($r[0], FILTER_SANITIZE_NUMBER_INT);
                            $maucau = (int) filter_var($r[1], FILTER_SANITIZE_NUMBER_INT);
                            if (0 === 1) {
                                DB::rollBack();
                                flash('oops...!', 'Import bài tập sai bài', 'error');
                            }
                            if (isset($dr_update[$maucau])) {
                                if ($r[2] != $ignoreHeading) {
                                    $check = DB::table('lms_exams')->insertGetId([
                                        'content_id' => $dr_update[$maucau],
                                        'label'      => $r[2],
                                        'dang'       => $r[3],
                                        'cau'        => $r[4],
                                        'mota'       => (string) $r[5],
                                        'luachon1'   => $r[6],
                                        'luachon2'   => $r[7],
                                        'luachon3'   => $r[8],
                                        'luachon4'   => $r[9],
                                        'dapan'      => $r[10],
                                        'created_by' => Auth::id(),
                                    ]);
                                }
                            }
                        }
                        DB::commit();
                    } catch (Exception $e) {
                        DB::rollBack();
                        flash('oops...!', 'opps..........!!!', 'error');
                        // dd($e);
                    }
                    $record->import = '1';
                    $record->save();
                }
            }
            # end import bai tap mau cau

            # import bai test
            if ($request->hasFile('lms_test')) {
                $path = $request->file('lms_test')->getRealPath();
                config(['excel.import.startRow' => 2]);
                $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
                    $reader->noHeading();
                })->get();
                if (!empty($data) && $data->count()) {
                    $ignoreHeading = 'label';
                    DB::beginTransaction();
                    DB::table('lms_test')
                        ->where('content_id', $record->id)
                        ->update([
                            'delete_status' => 1,
                        ]);
                    try {
                        foreach ($data as $r) {
                            if ($r[0] != $ignoreHeading && $r[1] != null && $r[2] != null) {
                                $check = DB::table('lms_test')->insertGetId([
                                    'content_id'   => $record->id,
                                    'dang'         => $r[1],
                                    'cau'          => $r[2],
                                    'mota'         => $r[3],
                                    'luachon1'     => $r[4],
                                    'luachon2'     => $r[5],
                                    'luachon3'     => $r[6],
                                    'luachon4'     => $r[7],
                                    'dapan'        => $r[8],
                                    'diem'         => $r[9],
                                    'content_type' => '5',
                                    'created_by'   => Auth::id(),
                                ]);
                            }
                        }
                        DB::commit();
                    } catch (Exception $e) {
                        DB::rollBack();
                        // dd($e);
                    }
                    $record->import = '1';
                    $record->save();
                }
            }
            # end import bai test

            # import bai tap loai 4
            if ($request->hasFile('lms_type_4')) {
                $path = $request->file('lms_type_4')->getRealPath();
                config(['excel.import.startRow' => 2]);
                $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
                    $reader->noHeading();
                })->get();
                // dd($data);
                if (!empty($data) && $data->count()) {
                    try {
                        DB::beginTransaction();
                        DB::table('lms_exams')
                            ->where('content_id', $record->id)
                            ->update([
                                'delete_status' => '1',
                            ]);
                        foreach ($data as $r) {
                            if ($r[1] != null && $r[1] != '') {
                                $check = DB::table('lms_exams')->insertGetId([
                                    'content_id' => $record->id,
                                    'dang'       => $r[1],
                                    'cau'        => $r[2],
                                    'mota'       => $r[3],
                                    'luachon1'   => $r[4],
                                    'luachon2'   => $r[5],
                                    'luachon3'   => $r[6],
                                    'luachon4'   => $r[7],
                                    'dapan'      => $r[8],
                                    'created_by' => Auth::id(),
                                ]);
                            }
                        }
                        DB::commit();
                    } catch (Exception $e) {
                        DB::rollBack();
                        flash('oops...!', 'opps..........!!!', 'error');
                        dd($e);
                    }
                    $record->import = '1';
                    $record->save();
                }
            }
            # end import bai tap loai 4

            DB::commit();
            flash('success', 'record_added_successfully', 'success');
        } catch (Exception $e) {
            // dd($e);
            DB::rollBack();
            if (getSetting('show_foreign_key_constraint', 'module')) {

                flash('oops...!', $e->errorInfo, 'error');
            } else {
                flash('oops...!', 'improper_data_file_submitted', 'error');
            }
        }
        // die;
        return redirect(PREFIX . "lms/" . $request->series . '/content');
    }

    /**
     * This method adds record to DB
     * @param  Request $request [Request Object]
     * @return void
     */
    public function store(Request $request)
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }

        $file_path = '';

        DB::beginTransaction();
        try {
            $lmsseries_id_q = DB::table('lmsseries')
                ->select('id')
                ->where('slug', $request->series_slug)
                ->get()->first();

            $record               = new LmsContent();
            $name                 = $request->title;
            $slug_insert          = $record->makeSlug($name, true);
            $record->title        = $name;
            $record->slug         = $slug_insert;
            $record->lmsseries_id = $lmsseries_id_q->id;
            $record->bai          = $request->bai;
            $record->type         = $request->loai;
            $record->maucau       = $request->maucau;
            // $record->code               = $request->code;
            $record->content_type      = 'url';
            $record->file_path         = $request->file_path;
            $record->stt               = $request->stt;
            $record->file_path         = $request->file_path;
            $record->description       = $request->description;
            $record->record_updated_by = Auth::user()->id;
            $record->save();
            DB::table('lmsseries_data')->insert([
                [
                    'lmsseries_id'  => $lmsseries_id_q->id,
                    'lmscontent_id' => $record->id,
                ],
            ]);

            $file_name = 'image';
            if ($request->hasFile($file_name)) {
                $rules = array($file_name => 'mimes:jpeg,jpg,png,gif|max:10000');
                $this->validate($request, $rules);
                $this->setSettings();
                $examSettings = $this->getSettings();
                $path         = $examSettings->contentImagepath;
                $this->deleteFile($record->image, $path);
                $record->image = $this->processUpload($request, $record, $file_name);
                $record->save();
            }

            # import video
            $file_name = 'lms_file';
            if ($request->hasFile($file_name)) {
                // $slug_inser = folder name ( id + random string )
                $slug_insert = $record->id . '-' . rand(100000000, 9999999999);
                $this->setSettings();
                $examSettings = $this->getSettings();
                $path         = $examSettings->contentImagepath;
                $this->deleteFile($record->file_path, $path);
                $file_video_name = $this->processUpload($request, $record, $file_name, false);
                $realpath_save   = public_path() . '/uploads/lms/content/' . $file_video_name;

                if (!file_exists(public_path() . '/uploads/lms/content/' . $slug_insert)) {
                    mkdir(public_path() . '/uploads/lms/content/' . $slug_insert);
                }
                // send stream video
                $data = array(
                    'upload'        => 1,
                    'file_contents' => new \CURLFile($realpath_save),
                    'path'          => $slug_insert,
                );
                try {
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        // curlopt url of dev.hikariacademy.edu.vn api end point
                        CURLOPT_URL            => "http://dev.hikariacademy.edu.vn/stream-video/api.php/hls",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_CUSTOMREQUEST  => "POST",
                        CURLOPT_HTTPHEADER     => array('Content-Type: multipart/form-data'),
                        CURLOPT_POSTFIELDS     => $data,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_SSL_VERIFYPEER => false,
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);
                    if ($response === false) {
                        throw new Exception(curl_error($curl), curl_errno($curl));
                    } else {
                        // url of zip file in dev.hikariacademy.edu.vn
                        $url     = 'http://dev.hikariacademy.edu.vn/stream-video/zip/' . $slug_insert . '/' . $slug_insert . '.zip';
                        $zipFile = public_path() . '/uploads/lms/content/' . $slug_insert . '/video.zip';
                        file_put_contents($zipFile, fopen($url, 'r'));
                        $zip         = new \ZipArchive;
                        $extractPath = public_path() . '/uploads/lms/content/' . $slug_insert;
                        if ($zip->open($zipFile) != "true") {
                            flash('error', 'record_added_successfully', 'error');
                        }
                        $zip->extractTo($extractPath);
                        $zip->close();
                    }
                } catch (Exception $e) {
                    dd($e);
                }

                $record->file_path = '/public/uploads/lms/content/' . $slug_insert . '/video.m3u8';
                $record->import    = '1';
                $record->save();
                @unlink($zipFile);
                @unlink($realpath_save);
            }
            # end import video

            # import bai tap mau cau
            if ($request->hasFile('lms_excel')) {
                $path = $request->file('lms_excel')->getRealPath();
                config(['excel.import.startRow' => 2]);
                $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
                    $reader->noHeading();
                })->get();
                // dd($data);
                if (!empty($data) && $data->count()) {
                    $content_update = DB::table('lmscontents')
                        ->select(['id', 'maucau'])
                        ->where([
                            ['parent_id', $record->id],
                            ['maucau', '<>', null],
                        ])->get();
                    $dr_update = [];
                    foreach ($content_update as $r) {
                        $dr_update[$r->maucau] = $r->id;
                        DB::table('lms_exams')
                            ->where([
                                ['content_id', $r->id],
                            ])
                            ->update(['delete_status' => 1]);
                    }
                    $ignoreHeading = 'label';
                    DB::beginTransaction();
                    foreach ($dr_update as $r) {
                        DB::table('lms_exams')
                            ->where('content_id', $r)
                            ->update([
                                'delete_status' => '1',
                            ]);
                    }
                    try {
                        foreach ($data as $r) {
                            $bai    = (int) filter_var($r[0], FILTER_SANITIZE_NUMBER_INT);
                            $maucau = (int) filter_var($r[1], FILTER_SANITIZE_NUMBER_INT);
                            if (0 === 1) {
                                DB::rollBack();
                                flash('oops...!', 'Import bài tập sai bài', 'error');
                            }
                            if (isset($dr_update[$maucau])) {
                                if ($r[2] != $ignoreHeading) {
                                    $check = DB::table('lms_exams')->insertGetId([
                                        'content_id' => $dr_update[$maucau],
                                        'label'      => $r[2],
                                        'dang'       => $r[3],
                                        'cau'        => $r[4],
                                        'mota'       => (string) $r[5],
                                        'luachon1'   => $r[6],
                                        'luachon2'   => $r[7],
                                        'luachon3'   => $r[8],
                                        'luachon4'   => $r[9],
                                        'dapan'      => $r[10],
                                        'created_by' => Auth::id(),
                                    ]);
                                }
                            }
                        }
                        DB::commit();
                    } catch (Exception $e) {
                        DB::rollBack();
                        flash('oops...!', 'opps..........!!!', 'error');
                        // dd($e);
                    }
                    $record->import = '1';
                    $record->save();
                }
            }
            # end import bai tap mau cau

            # import bai test
            if ($request->hasFile('lms_test')) {
                $path = $request->file('lms_test')->getRealPath();
                config(['excel.import.startRow' => 2]);
                $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
                    $reader->noHeading();
                })->get();
                if (!empty($data) && $data->count()) {
                    $ignoreHeading = 'label';
                    DB::beginTransaction();
                    DB::table('lms_test')
                        ->where('content_id', $record->id)
                        ->update([
                            'delete_status' => 1,
                        ]);
                    try {
                        foreach ($data as $r) {
                            if ($r[0] != $ignoreHeading && $r[1] != null && $r[2] != null) {
                                $check = DB::table('lms_test')->insertGetId([
                                    'content_id'   => $record->id,
                                    'dang'         => $r[1],
                                    'cau'          => $r[2],
                                    'mota'         => $r[3],
                                    'luachon1'     => $r[4],
                                    'luachon2'     => $r[5],
                                    'luachon3'     => $r[6],
                                    'luachon4'     => $r[7],
                                    'dapan'        => $r[8],
                                    'diem'         => $r[9],
                                    'content_type' => '5',
                                    'created_by'   => Auth::id(),
                                ]);
                            }
                        }
                        DB::commit();
                    } catch (Exception $e) {
                        DB::rollBack();
                        // dd($e);
                    }
                    $record->import = '1';
                    $record->save();
                }
            }
            # end import bai test

            # import bai tap loai 4
            if ($request->hasFile('lms_type_4')) {
                $path = $request->file('lms_type_4')->getRealPath();
                config(['excel.import.startRow' => 2]);
                $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
                    $reader->noHeading();
                })->get();
                // dd($data);
                if (!empty($data) && $data->count()) {
                    try {
                        DB::beginTransaction();
                        DB::table('lms_exams')
                            ->where('content_id', $record->id)
                            ->update([
                                'delete_status' => '1',
                            ]);
                        foreach ($data as $r) {
                            if ($r[1] != null && $r[1] != '') {
                                $check = DB::table('lms_exams')->insertGetId([
                                    'content_id' => $record->id,
                                    'dang'       => $r[1],
                                    'cau'        => $r[2],
                                    'mota'       => $r[3],
                                    'luachon1'   => $r[4],
                                    'luachon2'   => $r[5],
                                    'luachon3'   => $r[6],
                                    'luachon4'   => $r[7],
                                    'dapan'      => $r[8],
                                    'created_by' => Auth::id(),
                                ]);
                            }
                        }
                        DB::commit();
                    } catch (Exception $e) {
                        DB::rollBack();
                        flash('oops...!', 'opps..........!!!', 'error');
                        dd($e);
                    }
                    $record->import = '1';
                    $record->save();
                }
            }
            # end import bai tap loai 4

            DB::commit();
            flash('success', 'record_added_successfully', 'success');
        } catch (Exception $e) {
            dd($e);
            DB::rollBack();
            if (getSetting('show_foreign_key_constraint', 'module')) {
                flash('oops...!', $e->errorInfo, 'error');
            } else {
                flash('oops...!', 'improper_data_file_submitted', 'error');
            }
        }
        return redirect(PREFIX . "lms/" . $request->series_slug . '/content');
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
        $record = LmsContent::where('slug', $slug)->first();
        $this->setSettings();
        try {
            if (!env('DEMO_MODE')) {
                $examSettings = $this->getSettings();
                $path         = $examSettings->contentImagepath;
                $this->deleteFile($record->image, $path);
                if ($record->file_path != '') {
                    $this->deleteFile($record->file_path, $path);
                }

                $record->delete();
            }

            $response['status']  = 1;
            $response['message'] = getPhrase('category_deleted_successfully');
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

    public function getReturnUrl()
    {
        return URL_LMS_CONTENT;
    }

    public function deleteFile($record, $path, $is_array = false)
    {
        if (env('DEMO_MODE')) {
            return;
        }
        $files   = array();
        $files[] = $path . $record;
        File::delete($files);
    }

    /**
     * This method process the image is being refferred
     * by getting the settings from ImageSettings Class
     * @param  Request $request   [Request object from user]
     * @param  [type]  $record    [The saved record which contains the ID]
     * @param  [type]  $file_name [The Name of the file which need to upload]
     * @return [type]             [description]
     */
    public function processUpload(Request $request, $record, $file_name, $is_image = true)
    {

        if (env('DEMO_MODE')) {
            return 'demo';
        }

        if ($request->hasFile($file_name)) {
            $settings        = $this->getSettings();
            $destinationPath = $settings->contentImagepath;
            $path            = $_FILES[$file_name]['name'];
            $ext             = pathinfo($path, PATHINFO_EXTENSION);

            $fileName = $record->id . '-' . $file_name . '.' . $ext;

            $request->file($file_name)->move($destinationPath, $fileName);
            if ($is_image) {
                //Save Normal Image with 300x300
                Image::make($destinationPath . $fileName)->fit($settings->imageSize)->save($destinationPath . $fileName);
            }
            return $fileName;
        }

    }

    public function importExams(Request $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->getRealPath();
            config(['excel.import.startRow' => 1]);
            $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
                $reader->noHeading();
            })->get();
            if (!empty($data) && $data->count()) {

                $list_content_q = DB::table('lmscontents')
                    ->select(['lmsseries_data.lmscontent_id', 'lmscontents.stt'])
                    ->join('lmsseries_data', 'lmsseries_data.lmscontent_id', '=', 'lmscontents.id')
                    ->join('lmsseries', 'lmsseries_data.lmsseries_id', '=', 'lmsseries.id')
                    ->where([
                        ['lmsseries.slug', $request->series_slug],
                        ['lmscontents.parent_id', '<>', 0],
                    ])
                    ->orderBy('stt', 'asc')
                    ->get();

                $content = [];
                $i       = 1;
                foreach ($list_content_q as $r) {
                    $content[$i] = $r->lmscontent_id;
                    $i++;
                }
                if ($content == []) {
                    flash('error', 'record_import_error', 'error');
                    return back();
                }
                $ignoreHeading = 'label';
                DB::beginTransaction();
                try {
                    foreach ($data as $r) {
                        if ($r[2] != $ignoreHeading) {
                            $check = DB::table('lms_exams')->insertGetId([
                                'content_id' => $content[$r[1]],
                                'label'      => $r[2],
                                'dang'       => $r[3],
                                'cau'        => $r[4],
                                'mota'       => $r[5],
                                'luachon1'   => $r[6],
                                'luachon2'   => $r[7],
                                'luachon3'   => $r[8],
                                'luachon4'   => $r[9],
                                'dapan'      => $r[10],
                                'created_by' => Auth::id(),
                            ]);
                        }
                    }
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    dd($e);
                }
            }
        }
        flash('success', 'record_import_successfully', 'success');
        return back();
    }

    public function importMucLuc(Request $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->getRealPath();
            config(['excel.import.startRow' => 2]);
            $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
                $reader->noHeading();
            })->get();
            // dd($data);
            // DB::beginTransaction();

            if (!empty($data) && $data->count()) {
                $series_id = DB::table('lmsseries')
                    ->select(['lmsseries.id'])
                    ->where([
                        ['lmsseries.slug', $request->series_slug],
                        ['lmsseries.delete_status', 0],
                    ])
                    ->get()->first();

                if ($series_id == null) {
                    flash('error', 'record_import_error', 'error');
                    return back();
                }

                DB::table('lmscontents')
                    ->where('lmsseries_id', $series_id->id)
                    ->update([
                        'delete_status' => 1,
                    ]);

                $ignoreHeading = 'Type';

                try {
                    $stt = 1;
                    foreach ($data as $r) {
                        if ($r[0] != null) {
                            $type = '0';
                        } elseif ($r[1] != null) {
                            $type = '8';
                        } else {
                            $type = $r[4];
                        }

                        $parent_id  = (isset($parent_id) && $type != '0') ? $parent_id : null;
                        $parent_id  = ($type == '0') ? null : $parent_id;
                        $sub_parent = (isset($sub_parent) && $type != '8') ? $sub_parent : null;
                        $sub_parent = ($type == '0') ? null : $sub_parent;
                        if ($sub_parent == null) {
                            $insert_parent = $parent_id;
                        } else {
                            $insert_parent = $sub_parent;
                        }
                        $check = DB::table('lmscontents')->insertGetId([
                            'lmsseries_id' => $series_id->id,
                            'stt'          => $stt,
                            'maucau'       => (isset($r[5])) ? $r[5] : null,
                            'type'         => $type,
                            'bai'          => $r[0] . $r[1] . $r[2],
                            'title'        => $r[3],
                            'parent_id'    => $insert_parent,
                            'created_by'   => Auth::id(),
                        ]);
                        if ($type == '0') {
                            $parent_id  = $check;
                            $sub_parent = null;
                        }
                        if ($type == '8') {
                            $sub_parent = $check;
                        }
                        $stt++;
                        // $x= '' ;
                        // if($type == '8'){
                        //   $x = '--- ';
                        // }
                        // if(in_array($type, ['1','2','3','4','6','7'])){
                        //   $x = '------- ';
                        // }
                        // echo $x . $r[0].$r[1].$r[2]." - [ $stt ]<br>";
                        // echo "$check - $insert_parent <br>";
                    }

                } catch (Exception $e) {
                    // DB::rollBack();
                    dd($e);
                }
                // DB::commit();
                // dd($data);
            }
        }
        flash('success', 'record_import_successfully', 'success');
        return back();
    }

    public function importMucLuc_old(Request $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->getRealPath();
            config(['excel.import.startRow' => 2]);
            $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
                $reader->noHeading();
            })->get();
            if (!empty($data) && $data->count()) {
                $series_id = DB::table('lmsseries')
                    ->select(['lmsseries.id'])
                    ->where([
                        ['lmsseries.slug', $request->series_slug],
                        ['lmsseries.delete_status', 0],
                    ])
                    ->get()->first();
                if ($series_id == null) {
                    flash('error', 'record_import_error', 'error');
                    return back();
                }
                $ignoreHeading = 'Type';
                // dd($data);
                DB::beginTransaction();
                try {
                    $stt = 1;
                    foreach ($data as $r) {
                        $par       = ($r[1] == '0' || $r[1] == null) ? '0' : null;
                        $parent_id = (isset($parent_id) && $par != '0') ? $parent_id : null;
                        $check     = DB::table('lmscontents')->insertGetId([
                            'lmsseries_id' => $series_id->id,
                            'stt'          => $stt,
                            'maucau'       => $r[0],
                            'type'         => $r[1],
                            'bai'          => $r[2],
                            'title'        => $r[3],
                            'parent_id'    => $parent_id,
                            'created_by'   => Auth::id(),
                        ]);
                        if ($par == '0') {
                            $parent_id = $check;
                        }
                        $stt++;
                    }
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    dd($e);
                }
                // dd($data);
            }
        }
        flash('success', 'record_import_successfully', 'success');
        return back();
    }

    public function saveStreamVideo()
    {
        die('cúttttttttttttttttttttttt');
        $ar = DB::table('lmscontents')
            ->select('id')
            ->where([
                ['delete_status', 0],
                ['lmsseries_id', '43'],
            ])
            ->orderBy('id', 'asc')
            ->get();
        $stt = 1;
        foreach ($ar as $r) {
            DB::table('lmscontents')
                ->where('id', $r->id)
                ->update([
                    'stt' => $stt,
                ]);
            $stt++;
        }
    }

    public function view($series, $slug)
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }

        $data['series_slug']  = $series;
        $data['content_id']   = $slug;
        $data['back_content'] = PREFIX . "lms/$series/content";
        $data['active_class'] = 'lms';
        $data['title']        = 'LMS' . ' ' . getPhrase('content');
        $data['layout']       = getLayout();

        $check = DB::table('lmscontents')
            ->select('type')
            ->where([
                ['id', $slug],
                ['delete_status', 0],
            ])
            ->get();

        if ($check->isEmpty()) {
            flash('error', 'error info', 'error');
            return back();
        } else {
            if ($check[0]->type == '5') {
                $record = DB::table('lms_test')
                    ->select(['dang', 'cau', 'mota', 'luachon1', 'luachon2', 'luachon3', 'luachon4', 'dapan'])
                    ->where([
                        ['content_id', $slug],
                        ['delete_status', 0],
                    ])
                    ->orderBy('dang')
                    ->get();
            } else {
                $record = DB::table('lms_exams')
                    ->select(['dang', 'cau', 'mota', 'luachon1', 'luachon2', 'luachon3', 'luachon4', 'dapan'])
                    ->where([
                        ['content_id', $slug],
                        ['delete_status', 0],
                    ])
                    ->orderBy('dang')
                    ->get();
            }
        }

        $data['tr'] = [];

        foreach ($record as $r) {
            $data['tr'][] = "<tr>
        <td>" . $r->dang . "</td>
        <td>" . $r->cau . "</td>
        <td>" . $r->mota . "</td>
        <td>" . $r->luachon1 . "</td>
        <td>" . $r->luachon2 . "</td>
        <td>" . $r->luachon3 . "</td>
        <td>" . $r->luachon4 . "</td>
        <td>" . $r->dapan . "</td>
      </tr>";
        }

        $view_name = getTheme() . '::lms.lmscontents.view';
        return view($view_name, $data);
    }
    public function update_try(Request $request)
    {

        try {
            $show_status = DB::table('lmscontents')
                ->select('lmscontents.el_try')
                ->where('id', $request->id)
                ->get()->first();

            $up = ($show_status->el_try == '1') ? '0' : '1';

            DB::table('lmscontents')
                ->where('id', $request->id)
                ->update([
                    'el_try' => $up,
                ]);
            return 'success';
        } catch (Exception $e) {
            return $e;
        }

    }
}
