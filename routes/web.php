<?php
Route::get('/', function () {
    if(Auth::check())
    {
        return redirect('dashboard');
    }
    // dd('here');
    //return redirect(URL_USERS_LOGIN);
    return redirect('/home');
});
/*if(env('DEMO_MODE')) {
    Event::listen('eloquent.saving: *', function ($model) {
        if(urlHasString('finish-exam') || urlHasString('start-exam'))
          return true;
      return false;
    });
}*/
 // Route::get('install/reg', 'InstallatationController@reg');
 // Route::post('install/register', 'InstallatationController@registerUser');
// if(env('DB_DATABASE')==''){
//   Route::get('/', 'SiteController@login');
// }
  // Route::get('home', 'Auth\LoginController@getLogin');
  Route::get('home', 'SiteController@index');
// Route::get('/', function () {
//     if(Auth::check())
//     {
//         return redirect('dashboard');
//     }
//  return redirect(URL_USERS_LOGIN);
// });
Route::get('dashboard','DashboardController@index');
Route::get('dashboard/testlang','DashboardController@testLanguage');
Route::get('auth/{slug}','Auth\LoginController@redirectToProvider');
Route::get('auth/{slug}/callback','Auth\LoginController@handleProviderCallback');
// Authentication Routes...
Route::get('login/{layout_type?}', 'Auth\LoginController@getLogin');
Route::post('login', 'Auth\LoginController@postLogin');
Route::get('logout', function(){
  if(Auth::check())
    flash('Bạn đã đăng xuất','','success');
  Auth::logout();
  return redirect(URL_USERS_LOGIN);
});
Route::get('parent-logout', function(){
    if(Auth::check())
        flash('Oops..!',getPhrase('parents_module_is_not_available'),'error');
    Auth::logout();
    return redirect(URL_USERS_LOGIN);
});
Route::get('confirm/{slug?}', 'SiteController@confirmRegister');
// Route::get('auth/logout', 'Auth\LoginController@getLogout');
// Registration Routes...
Route::get('register', 'Auth\RegisterController@getRegister');
Route::post('register', 'Auth\RegisterController@postRegister');
// Forgot Password Routes...
// Route::get('forgot-password', 'PasswordController@postEmail');
Route::get('password/reset/{slug?}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');
Route::post('users/forgot-password', 'Auth\AuthController@resetUsersPassword');
Route::get('languages/list', 'NativeController@index');
Route::get('languages/getList', [ 'as'   => 'languages.dataTable',
     'uses' => 'NativeController@getDatatable']);
Route::get('languages/add', 'NativeController@create');
Route::post('languages/add', 'NativeController@store');
Route::get('languages/edit/{slug}', 'NativeController@edit');
Route::patch('languages/edit/{slug}', 'NativeController@update');
Route::delete('languages/delete/{slug}', 'NativeController@delete');
Route::get('languages/make-default/{slug}', 'NativeController@changeDefaultLanguage');
Route::get('languages/update-strings/{slug}', 'NativeController@updateLanguageStrings');
Route::patch('languages/update-strings/{slug}', 'NativeController@saveLanguageStrings');
//Users
Route::get('users/staff/{role?}', 'UsersController@index');
Route::get('users/create', 'UsersController@create');
Route::delete('users/delete/{slug}', 'UsersController@delete');
Route::post('users/create/{role?}', 'UsersController@store');
Route::get('users/edit/{slug}', 'UsersController@edit');
Route::patch('users/edit/{slug}', 'UsersController@update');
// Route::get('users/profile/{slug}', 'UsersController@show');
Route::get('users', 'UsersController@index');
Route::get('users/register', 'UsersController@register');
Route::get('users/profile/{slug}', 'UsersController@profile');
Route::patch('users/profile/{slug}', 'UsersController@updateProfile');
Route::get('users/details/{slug}', 'UsersController@details');
Route::get('users/registerjp', 'UsersController@registerjp');
Route::get('users/list/getregisterjpList/{role_name?}', [ 'as'   => 'users.registerjpdataTable',
    'uses' => 'UsersController@getregisterjpDatatable']);
Route::get('users/settings/{slug}', 'UsersController@settings');
Route::patch('users/settings/{slug}', 'UsersController@updateSettings');
Route::get('users/change-password/{slug}', 'UsersController@changePassword');
Route::patch('users/change-password/{slug}', 'UsersController@updatePassword');
Route::get('users/import','UsersController@importUsers');
Route::post('users/import','UsersController@readExcel');
Route::get('users/import-report','UsersController@importResult');
Route::get('users/list/getregisterList/{role_name?}', [ 'as'   => 'users.registerdataTable',
    'uses' => 'UsersController@getregisterDatatable']);
Route::get('users/list/getList/{role_name?}', [ 'as'   => 'users.dataTable',
    'uses' => 'UsersController@getDatatable']);
Route::get('users/parent-details/{slug}', 'UsersController@viewParentDetails');
Route::patch('users/parent-details/{slug}', 'UsersController@updateParentDetails');
Route::post('users/search/parent', 'UsersController@getParentsOnSearch');
// Route::get('users/list/getList/{role_name?}', 'UsersController@getDatatable');
            //////////////////////
            //Parent Controller //
            //////////////////////
Route::get('parent/children/{slug}', 'ParentsController@index');
Route::get('parent/class', 'ParentsController@class');
Route::get('parent/class/getList/{slug}', 'ParentsController@getClassDatatable');
Route::get('parent/children/list', 'ParentsController@index');
Route::get('parent/children/getList/{slug}', 'ParentsController@getDatatable');
Route::get('parent/exam/getList/{slug}', 'ParentsController@getExamDatatable');
Route::get('children/analysis', 'ParentsController@childrenAnalysis');
Route::get('parent/exam_list/{slug}', 'ParentsController@examList');
Route::post('parent/exam_list/{slug}', 'ParentsController@examListUpdate');
Route::get('parent/classmark/{slug}/{slug_exam}/{slug_category}', 'ParentsController@classmark');
Route::get('parent/getclassmark/{slug}/{slug_exam}/{slug_category}', 'ParentsController@getClassmarkDatatable');
Route::get('parent/exam_list/edit/{slug}/{slug_exam}', 'ParentsController@editExamClass');
Route::post('parent/exam_list/edit/{slug}/{slug_exam}', 'ParentsController@editExamClassUpdate');
Route::delete('parent/delete/{slug}', 'ParentsController@delete');
Route::get('parent/ajaxn/{subject_id}', 'ParentsController@ajaxn');
                    /////////////////////
                    // Master Settings //
                    /////////////////////
//subjects
Route::get('mastersettings/subjects', 'SubjectsController@index');
Route::get('mastersettings/subjects/add', 'SubjectsController@create');
Route::post('mastersettings/subjects/add', 'SubjectsController@store');
Route::get('mastersettings/subjects/edit/{slug}', 'SubjectsController@edit');
Route::patch('mastersettings/subjects/edit/{slug}', 'SubjectsController@update');
Route::delete('mastersettings/subjects/delete/{id}', 'SubjectsController@delete');
Route::get('mastersettings/subjects/getList', [ 'as'   => 'subjects.dataTable',
    'uses' => 'SubjectsController@getDatatable']);
Route::get('mastersettings/subjects/import', 'SubjectsController@import');
Route::post('mastersettings/subjects/import', 'SubjectsController@readExcel');
//Topics
Route::get('mastersettings/topics', 'TopicsController@index');
Route::get('mastersettings/topics/add', 'TopicsController@create');
Route::post('mastersettings/topics/add', 'TopicsController@store');
Route::get('mastersettings/topics/edit/{slug}', 'TopicsController@edit');
Route::patch('mastersettings/topics/edit/{slug}', 'TopicsController@update');
Route::delete('mastersettings/topics/delete/{id}', 'TopicsController@delete');
Route::get('mastersettings/topics/getList', [ 'as'   => 'topics.dataTable',
    'uses' => 'TopicsController@getDatatable']);
Route::get('mastersettings/topics/get-parents-topics/{subject_id}', 'TopicsController@getParentTopics');
Route::get('mastersettings/topics/import', 'TopicsController@import');
Route::post('mastersettings/topics/import', 'TopicsController@readExcel');
                    ////////////////////////
                    // EXAMINATION SYSTEM //
                    ////////////////////////
//Question bank
Route::get('exams/questionbank', 'QuestionBankController@index');
Route::post('exams/ajax_furigana', 'QuestionBankController@ajax_return_furigana');
Route::get('exams/questionbank/add-question/{slug}', 'QuestionBankController@create');
Route::get('exams/questionbank/view/{slug}', 'QuestionBankController@show');
Route::post('exams/questionbank/add', 'QuestionBankController@store');
Route::get('exams/questionbank/edit-question/{slug}', 'QuestionBankController@edit');
Route::patch('exams/questionbank/edit/{slug}', 'QuestionBankController@update');
Route::delete('exams/questionbank/delete/{id}', 'QuestionBankController@delete');
Route::get('exams/questionbank/getList',  'QuestionBankController@getDatatable');
Route::get('exams/questionbank/getquestionslist/{slug}',
     'QuestionBankController@getQuestions');
Route::get('exams/questionbank/import',  'QuestionBankController@import');
Route::post('exams/questionbank/import',  'QuestionBankController@readExcel');
//Quiz Categories
Route::get('exams/categories', 'QuizCategoryController@index');
Route::get('exams/categories/add', 'QuizCategoryController@create');
Route::post('exams/categories/add', 'QuizCategoryController@store');
Route::get('exams/categories/edit/{slug}', 'QuizCategoryController@edit');
Route::patch('exams/categories/edit/{slug}', 'QuizCategoryController@update');
Route::delete('exams/categories/delete/{slug}', 'QuizCategoryController@delete');
Route::get('exams/categories/getList', [ 'as'   => 'quizcategories.dataTable',
    'uses' => 'QuizCategoryController@getDatatable']);
// Quiz Student Categories
Route::get('exams/student/categories', 'StudentQuizController@index');
Route::get('exams/student/exams/{slug?}', 'StudentQuizController@exams');
Route::get('exams/student/quiz/getList/{slug?}', 'StudentQuizController@getDatatable');
Route::get('exams/student/quiz/take-exam/{slug?}', 'StudentQuizController@instructions');
Route::post('exams/student/start-exam/{slug}', 'StudentQuizController@startExam');
Route::get('exams/student/start-exam/{slug}', 'ExamSeriesController@listSeries');
Route::get('exams/check/start-exam/{slug}', 'StudentQuizController@startExam');
Route::get('exams/student/finish-exam-result/{slug}', 'StudentQuizController@finishExamResult');
Route::get('exams/student/test_result1', 'StudentQuizController@test_result');
Route::post('exams/student/finish-exam/{slug}', 'StudentQuizController@finishExam');
Route::get('exams/student/reports/{slug}', 'StudentQuizController@reports');
Route::post('exams/student/ajax_log', 'StudentQuizController@ajax_log');
Route::post('exams/student/ajax_rate', 'StudentQuizController@ajax_rate');
Route::get('exams/student/exam-attempts/{user_slug}/{exam_slug?}', 'StudentQuizController@examAttempts');
Route::get('exams/student/exam-attempts-finish', 'StudentQuizController@examAttemptsfinish');
Route::get('exams/student/get-exam-attempts/{user_slug}/{exam_slug?}', 'StudentQuizController@getExamAttemptsData');
Route::get('exams/student/get-exam-attempts-finish/{user_slug}/{exam_slug?}', 'StudentQuizController@getExamAttemptsDataFinish');
Route::get('student/analysis/by-exam/{user_slug}', 'StudentQuizController@examAnalysis');
Route::get('student/analysis/get-by-exam/{user_slug}', 'StudentQuizController@getExamAnalysisData');
Route::get('student/analysis/by-subject/{user_slug}/{exam_slug?}/{results_slug?}', 'StudentQuizController@subjectAnalysisInExam');
Route::get('student/analysis/subject/{user_slug}', 'StudentQuizController@overallSubjectAnalysis');
//Student Reports
//Route::get('student/exam/answers/{quiz_slug}/{result_slug}', 'ReportsController@viewExamAnswers');

Route::get('student/exam/answers/{quiz_slug}/{result_slug}', 'StudentQuizController@startExamAnswers');





//Quiz
Route::get('exams/quizzes', 'QuizController@index');
Route::get('exams/quiz/add', 'QuizController@create');
Route::post('exams/quiz/add', 'QuizController@store');
Route::get('exams/quiz/edit/{slug}', 'QuizController@edit');
Route::patch('exams/quiz/edit/{slug}', 'QuizController@update');
Route::delete('exams/quiz/delete/{slug}', 'QuizController@delete');
Route::get('exams/quiz/getList/{slug?}', 'QuizController@getDatatable');
Route::get('exams/quiz/update-questions/{slug}', 'QuizController@updateQuestions');
Route::post('exams/quiz/update-questions/{slug}', 'QuizController@storeQuestions');
Route::post('exams/quiz/get-questions', 'QuizController@getSubjectData');
//Certificates controller
Route::get('result/generate-certificate/{slug}', 'CertificatesController@getCertificate');
//Exam Series
Route::get('exams/exam-series', 'ExamSeriesController@index');
Route::get('exams/exam-series/add', 'ExamSeriesController@create');
Route::post('exams/exam-series/add', 'ExamSeriesController@store');
Route::get('exams/exam-series/edit/{slug}', 'ExamSeriesController@edit');
Route::patch('exams/exam-series/edit/{slug}', 'ExamSeriesController@update');
Route::delete('exams/exam-series/delete/{slug}', 'ExamSeriesController@delete');
Route::get('exams/exam-series/getList', 'ExamSeriesController@getDatatable');
//Exam Series free
Route::get('exams/exam-series-free', 'ExamSeriesfreeController@index');
Route::get('exams/exam-series-free/add', 'ExamSeriesfreeController@create');
Route::post('exams/exam-series-free/add', 'ExamSeriesfreeController@store');
Route::get('exams/exam-series-free/edit/{slug}', 'ExamSeriesfreeController@edit');
Route::patch('exams/exam-series-free/edit/{slug}', 'ExamSeriesfreeController@update');
Route::delete('exams/exam-series-free/delete/{slug}', 'ExamSeriesfreeController@delete');
Route::get('exams/exam-series-free/getList', 'ExamSeriesfreeController@getDatatable');
Route::get('exams/exam-series-free/rate/{slug}', 'ExamSeriesfreeController@rate');
Route::get('exams/exam-series-free/getrateList/{slug}', 'ExamSeriesfreeController@getrateDatatable');
Route::get('exams/exam-series-free/rate-result/{slug}', 'ExamSeriesfreeController@rateresult');
Route::get('exams/exam-series-free/getrateresultList/{slug}', 'ExamSeriesfreeController@getrateresultDatatable');
Route::get('exams/exam-series-free/total/{slug}', 'ExamSeriesfreeController@total');
//EXAM SERIES STUDENT LINKS
Route::get('exams/student-exam-series/list', 'ExamSeriesController@listSeries');
Route::get('exams/student-exam-series/{slug}', 'ExamSeriesController@viewItem');
Route::get('exams/view-exam-series/{slug}', 'ExamSeriesController@viewExam');
Route::get('exams/view-exam-series-chart/{slug}', 'ExamSeriesController@chartExam');
Route::get('exams/exam-series/update-series/{slug}', 'ExamSeriesController@updateSeries');
Route::post('exams/exam-series/update-series/{slug}', 'ExamSeriesController@storeSeries');
Route::post('exams/exam-series/get-exams', 'ExamSeriesController@getExams');
Route::get('payment/cancel', 'ExamSeriesController@cancel');
Route::post('payment/success', 'ExamSeriesController@success');
Route::get('exams/exam-series/check-exams/{slug}', 'ExamSeriesController@checkExams');
            /////////////////////
            // PAYMENT REPORTS //
            /////////////////////
Route::get('payments-report/', 'PaymentsController@overallPayments');
 Route::get('payments-report/online/', 'PaymentsController@onlinePaymentsReport');
Route::get('payments-order', 'PaymentsController@listorderPayment');
Route::get('payments-order/getList', 'PaymentsController@indexlistorderPayment');
/*Route::get('payments-order/success/{slug}', 'PaymentsController@successorderPayment');*/

Route::post('payments-order/success', 'PaymentsController@successorderPayment');
 // Route::get('payments-report/online/{slug}', 'PaymentsController@listOnlinePaymentsReport');
 Route::get('payments-report/online/{slug}', 'PaymentsController@getAllOnlinePayment');
Route::get('payments-report/online/getList/{slug}', 'PaymentsController@getOnlinePaymentReportsDatatable');
Route::get('payments-report/offline/', 'PaymentsController@offlinePaymentsReport');
Route::get('payments-report/offline/{slug}', 'PaymentsController@listOfflinePaymentsReport');
Route::get('payments-report/offline/getList/{slug}', 'PaymentsController@getOfflinePaymentReportsDatatable');
Route::get('payments-report/export', 'PaymentsController@exportPayments');
Route::post('payments-report/export', 'PaymentsController@doExportPayments');
Route::get('payments-report/add-offline', 'PaymentsController@addOfflinePayment');
Route::post('payments-report/add-offline', 'PaymentsController@updateOfflinePayments');
Route::get('payments-report/index-offline', 'PaymentsController@indexOfflinePayments');
Route::get('payments-report/index-online', 'PaymentsController@indexOnlinePayments');
Route::post('payments-report/getRecord', 'PaymentsController@getPaymentRecord');
Route::post('payments/approve-reject-offline-request', 'PaymentsController@approveOfflinePayment');
Route::get('payments/buypoint', 'PaymentsController@buypoint');
Route::post('payments/buypoint', 'PaymentsController@buypointdetail');
Route::get('payments/buypoint/{point}', 'PaymentsController@buypointdetail');
Route::get('/payments/momoreturn', 'PaymentsController@momoReturn');
Route::get('/payments/atmreturn', 'PaymentsController@atmReturn');
Route::post('/payments/momoipn', 'PaymentsController@momoIpn');
Route::get('/payments/history', 'PaymentsController@historyUser');
Route::get('payments-report/history', 'PaymentsController@historyPayment');
Route::get('payments/buy-item', 'PaymentsController@historyBuyItem');
Route::get('payments-report/buy-item', 'PaymentsController@getHistoryBuyItem');
            //////////////////
            // INSTRUCTIONS  //
            //////////////////
Route::get('exam/instructions/list', 'InstructionsController@index');
Route::get('exam/instructions', 'InstructionsController@index');
Route::get('exams/instructions/add', 'InstructionsController@create');
Route::post('exams/instructions/add', 'InstructionsController@store');
Route::get('exams/instructions/edit/{slug}', 'InstructionsController@edit');
Route::patch('exams/instructions/edit/{slug}', 'InstructionsController@update');
Route::delete('exams/instructions/delete/{slug}', 'InstructionsController@delete');
Route::get('exams/instructions/getList', 'InstructionsController@getDatatable');
//BOOKMARKS MODULE
Route::get('student/bookmarks/{slug}', 'BookmarksController@index');
Route::post('student/bookmarks/add', 'BookmarksController@create');
Route::delete('student/bookmarks/delete/{id}', 'BookmarksController@delete');
Route::delete('student/bookmarks/delete_id/{id}', 'BookmarksController@deleteById');
Route::get('student/bookmarks/getList/{slug}',  'BookmarksController@getDatatable');
Route::post('student/bookmarks/getSavedList',  'BookmarksController@getSavedBookmarks');
                //////////////////////////
                // Notifications Module //
                /////////////////////////
Route::get('admin/notifications/list', 'NotificationsController@index');
Route::get('admin/notifications', 'NotificationsController@index');
Route::get('admin/notifications/add', 'NotificationsController@create');
Route::post('admin/notifications/add', 'NotificationsController@store');
Route::get('admin/notifications/edit/{slug}', 'NotificationsController@edit');
Route::patch('admin/notifications/edit/{slug}', 'NotificationsController@update');
Route::delete('admin/notifications/delete/{slug}', 'NotificationsController@delete');
Route::get('admin/notifications/getList', 'NotificationsController@getDatatable');
// NOTIFICATIONS FOR STUDENT
Route::get('notifications/list', 'NotificationsController@usersList');
Route::get('notifications/show/{slug}', 'NotificationsController@display');
//BOOKMARKS MODULE
Route::get('toppers/compare-with-topper/{user_result_slug}/{compare_slug?}', 'ExamToppersController@compare');
                        ////////////////
                        // LMS MODULE //
                        ////////////////
//LMS Categories
Route::get('lms/categories', 'LmsCategoryController@index');
Route::get('lms/categories/add', 'LmsCategoryController@create');
Route::post('lms/categories/add', 'LmsCategoryController@store');
Route::get('lms/categories/edit/{slug}', 'LmsCategoryController@edit');
Route::patch('lms/categories/edit/{slug}', 'LmsCategoryController@update');
Route::delete('lms/categories/delete/{slug}', 'LmsCategoryController@delete');
Route::get('lms/categories/getList', [ 'as'   => 'lmscategories.dataTable',
    'uses' => 'LmsCategoryController@getDatatable']);
//LMS Contents
Route::get('lms/content', 'LmsContentController@index');
Route::get('lms/content/add', 'LmsContentController@create');
Route::post('lms/content/add', 'LmsContentController@store');
Route::get('lms/content/edit/{slug}', 'LmsContentController@edit');
Route::patch('lms/content/edit/{slug}', 'LmsContentController@update');
Route::delete('lms/content/delete/{slug}', 'LmsContentController@delete');
Route::get('lms/content/getList', [ 'as'   => 'lmscontent.dataTable',
    'uses' => 'LmsContentController@getDatatable']);
//LMS Series
Route::get('lms/series', 'LmsSeriesController@index');
Route::get('lms/series/add', 'LmsSeriesController@create');
Route::post('lms/series/add', 'LmsSeriesController@store');
Route::get('lms/series/edit/{slug}', 'LmsSeriesController@edit');
Route::get('lms/seriessexam/edit/{slug}', 'LmsSeriesController@editstydy');

Route::patch('lms/series/edit/{slug}', 'LmsSeriesController@update');
Route::delete('lms/series/delete/{slug}', 'LmsSeriesController@delete');
Route::get('lms/series/getList', 'LmsSeriesController@getDatatable');

Route::get('lms/seriesexam', 'LmsExamController@index');
Route::get('lms/series/getExamList', 'LmsExamController@getDatatable');

//LMS COMBO ADMIN LINKS
Route::get('lms/seriescombo', 'LmsComboController@index');
Route::get('lms/seriescombo/getExamList', 'LmsComboController@getDatatable');
Route::get('lms/seriescombo/add', 'LmsComboController@create');
Route::post('lms/seriescombo/add', 'LmsComboController@store');
Route::get('lms/seriescombo/edit/{slug}', 'LmsComboController@edit');
Route::patch('lms/seriescombo/edit/{slug}', 'LmsComboController@update');
Route::delete('lms/seriescombo/delete/{slug}', 'LmsComboController@delete');


//LMS SERIES STUDENT LINKS
Route::get('lms/exam-categories/list', 'LmsSeriesController@listCategories');
Route::get('lms/exam-categories/study', 'LmsSeriesController@listCategoriesstudy');
Route::get('lms/exam-categories/payment', 'LmsSeriesController@listPayments');
Route::get('lms/exam-series/list/{slug?}/{stt?}', 'LmsSeriesController@listSeries');
Route::get('lms/exam-series/{slug}', 'LmsSeriesController@viewItem');
Route::get('lms/series/update-series/{slug}', 'LmsSeriesController@updateSeries');
Route::post('lms/series/update-series/{slug}', 'LmsSeriesController@storeSeries');
Route::post('lms/series/get-series', 'LmsSeriesController@getSeries');
Route::get('payment/cancel', 'LmsSeriesController@cancel');
Route::post('payment/success', 'LmsSeriesController@success');
//LMS Student view
Route::get('learning-management/categories', 'StudentLmsController@index');
Route::get('learning-management/view/{slug}', 'StudentLmsController@viewCategoryItems');
Route::get('learning-management/series', 'StudentLmsController@series');
Route::get('learning-management/series/{slug}/{content_slug?}', 'StudentLmsController@viewItem');
Route::get('user/paid/{slug}/{content_slug}', 'StudentLmsController@verifyPaidItem');
Route::get('learning-management/content/{req_content_type}', 'StudentLmsController@content');
Route::get('learning-management/content/show/{slug}', 'StudentLmsController@showContent');
Route::get('learning-management/lesson/show/{combo_slug}/{slug?}/{stt?}', 'StudentLmsController@showLesson');
Route::get('learning-management/lesson/combo/{slug?}', 'StudentLmsController@showCombo');
Route::get('learning-management/lesson-selected/show/{slug?}/{lesson_id?}', 'StudentLmsController@showLessonSelected');
//Payments Controller
Route::get('payments/list/{slug}', 'PaymentsController@index');
Route::get('payments/getList/{slug}', 'PaymentsController@getDatatable');
Route::get('payments/checkout/{type}/{slug}', 'PaymentsController@checkout');
Route::get('payments/paynow/{slug}', 'DashboardController@index');
Route::post('payments/paynow/{slug}', 'PaymentsController@paynow');
Route::post('payments/paypal/status-success','PaymentsController@paypal_success');
Route::get('payments/paypal/status-cancel', 'PaymentsController@paypal_cancel');
Route::post('payments/payu/status-success','PaymentsController@payu_success');
Route::post('payments/payu/status-cancel', 'PaymentsController@payu_cancel');
Route::post('payments/offline-payment/update', 'PaymentsController@updateOfflinePayment');
Route::post('payments/ajaxcheckout', 'PaymentsController@ajaxcheckout');
                        ////////////////////////////
                        // SETTINGS MODULE //
                        ///////////////////////////
//LMS Categories
Route::get('mastersettings/settings/', 'SettingsController@index');
Route::get('mastersettings/settings/index', 'SettingsController@index');
Route::get('mastersettings/settings/add', 'SettingsController@create');
Route::post('mastersettings/settings/add', 'SettingsController@store');
Route::get('mastersettings/settings/edit/{slug}', 'SettingsController@edit');
Route::patch('mastersettings/settings/edit/{slug}', 'SettingsController@update');
Route::get('mastersettings/settings/view/{slug}', 'SettingsController@viewSettings');
Route::get('mastersettings/settings/add-sub-settings/{slug}', 'SettingsController@addSubSettings');
Route::post('mastersettings/settings/add-sub-settings/{slug}', 'SettingsController@storeSubSettings');
Route::patch('mastersettings/settings/add-sub-settings/{slug}', 'SettingsController@updateSubSettings');
Route::get('mastersettings/settings/getList', [ 'as'   => 'mastersettings.dataTable',
     'uses' => 'SettingsController@getDatatable']);
                        ////////////////////////////
                        // EMAIL TEMPLATES MODULE //
                        ///////////////////////////
//LMS Categories
Route::get('email/templates', 'EmailTemplatesController@index');
Route::get('email/templates/add', 'EmailTemplatesController@create');
Route::post('email/templates/add', 'EmailTemplatesController@store');
Route::get('email/templates/edit/{slug}', 'EmailTemplatesController@edit');
Route::patch('email/templates/edit/{slug}', 'EmailTemplatesController@update');
Route::delete('email/templates/delete/{slug}', 'EmailTemplatesController@delete');
Route::get('email/templates/getList', [ 'as'   => 'emailtemplates.dataTable',
    'uses' => 'EmailTemplatesController@getDatatable']);
Route::get('email/thongbaothithu', 'EmailTemplatesController@thongbaothithu');
//Coupons Module
// Route::get('coupons/list', 'CouponcodesController@index');
// Route::get('coupons/add', 'CouponcodesController@create');
// Route::post('coupons/add', 'CouponcodesController@store');
// Route::get('coupons/edit/{slug}', 'CouponcodesController@edit');
// Route::patch('coupons/edit/{slug}', 'CouponcodesController@update');
// Route::delete('coupons/delete/{slug}', 'CouponcodesController@delete');
// Route::get('coupons/getList/{slug?}', 'CouponcodesController@getDatatable');
// Route::get('coupons/get-usage', 'CouponcodesController@getCouponUsage');
// Route::get('coupons/get-usage-data', 'CouponcodesController@getCouponUsageData');
// Route::post('coupons/update-questions/{slug}', 'CouponcodesController@storeQuestions');
// Route::post('coupons/validate-coupon', 'CouponcodesController@validateCoupon');
//Feedback Module
// Route::get('feedback/list', 'FeedbackController@index');
// Route::get('feedback/view-details/{slug}', 'FeedbackController@details');
// Route::get('feedback/send', 'FeedbackController@create');
// Route::post('feedback/send', 'FeedbackController@store');
// Route::delete('feedback/delete/{slug}', 'FeedbackController@delete');
// Route::get('feedback/getlist', 'FeedbackController@getDatatable');
//SMS Module
// Route::get('sms/index', 'SMSAgentController@index');
// Route::post('sms/send', 'SMSAgentController@sendSMS');
                        /////////////////////
                        // MESSAGES MODULE //
                        /////////////////////
Route::group(['prefix' => 'messages'], function () {
    Route::get('/', ['as' => 'messages', 'uses' => 'MessagesController@index']);
    Route::get('create', ['as' => 'messages.create', 'uses' => 'MessagesController@create']);
    Route::post('/', ['as' => 'messages.store', 'uses' => 'MessagesController@store']);
    Route::get('{id}', ['as' => 'messages.show', 'uses' => 'MessagesController@show']);
    Route::put('{id}', ['as' => 'messages.update', 'uses' => 'MessagesController@update']);
});
                        /////////////////////
                        // PRIVACY POLICY  //
                        /////////////////////
Route::get('site/{slug?}', 'SiteController@sitePages');
// privacy-policy
                         ////////////////////
                         // UPDATE PATCHES //
                         ////////////////////
 Route::get('updates/patch1', 'UpdatesController@patch1');
 Route::get('updates/patch2', 'UpdatesController@patch2');
 Route::get('updates/patch3', 'UpdatesController@patch3');
 Route::get('updates/patch4', 'UpdatesController@patch4');
 Route::get('update/application','UpdatesController@updateDatabase');
Route::get('refresh-csrf', function(){
    return csrf_token();
});
//Fornt End Part
 Route::get('exams/list', 'FrontendExamsController@examsList');
Route::get('exams/start-exam/{slug}', 'FrontendExamsController@startExam');
Route::post('exams/finish-exam/{slug}', 'FrontendExamsController@finishExam');
//Resume Exam
Route::post('resume/examdata/save','StudentQuizController@saveResumeExamData');
Route::get('exam-types','QuizController@examTypes');
Route::get('edit/exam-type/{code}','QuizController@editExamType');
Route::post('update/exam-type/{code}','QuizController@updateExamType');
Route::post('razoapay/success','PaymentsController@razorpaySuccess');
//Theme Updates
Route::post('subscription/email','SiteController@saveSubscription');
//Subscribed Users
Route::get('subscribed/users','UsersController@SubscribedUsers');
Route::get('subscribed/users/data','UsersController@SubscribersData');
//All Exam categories
Route::get('exam/categories/{slug?}','SiteController@frontAllExamCats');
Route::get('practice-exams/{slug?}','SiteController@frontAllExamCats');
Route::get('LMS/all-categories/{slug?}','SiteController@forntAllLMSCats');
Route::get('LMS/contents/{slug}','SiteController@forntLMSContents');
Route::get('download/lms/contents/{slug}','SiteController@downloadLMSContent');
Route::get('lms/video/{slug}/{cat_id?}','SiteController@viewVideo');
Route::get('contact-us',function(){
      $view_name = getTheme().'::site.contact-us';
      $data['active_class']  = "contact-us";
      $data['title']  = getPhrase('contact_us');
      return view($view_name,$data);
});
Route::post('send/contact-us/details','SiteController@ContactUs');
Route::post('get/series/contents','SiteController@getSeriesContents');
//Themes
Route::get('themes/list','SiteThemesController@index');
Route::get('themes/data','SiteThemesController@getDatatable');
Route::get('make/default/theme/{id}','SiteThemesController@makeDefault');
Route::get('theme/settings/{slug}','SiteThemesController@viewSettings');
Route::post('theme/update/settings/{slug}','SiteThemesController@updateSubSettings');
//Class
// Route::get('users/staff/{role?}', 'UsersController@index');
// Route::get('users/create', 'UsersController@create');
// Route::delete('users/delete/{slug}', 'UsersController@delete');
// Route::post('users/create/{role?}', 'UsersController@store');
// Route::get('users/edit/{slug}', 'UsersController@edit');
// Route::patch('users/edit/{slug}', 'UsersController@update');
// Route::get('users/profile/{slug}', 'UsersController@show');
Route::get('classes', 'ClassesController@index');
Route::get('classes/getList',  'ClassesController@getDatatable');
Route::get('classes/add', 'ClassesController@create');
Route::post('classes/add', 'ClassesController@store');
Route::get('classes/edit/{slug}', 'ClassesController@edit');
Route::patch('classes/edit/{slug}', 'ClassesController@update');
Route::delete('classes/delete/{id}', 'ClassesController@delete');
Route::get('classes/classes-details/{slug}', 'ClassesController@viewClassesDetails');
Route::post('classes/search/user', 'ClassesController@getParentsOnSearch');
Route::post('classes/classes-details/{slug}', 'ClassesController@updateClassesDetails');
Route::get('classes/user/getList/{slug}',  'ClassesController@getClassesUserDatatable');
Route::delete('classes/user/delete/{id}', 'ClassesController@deleteUserClasses');
Route::get('books', 'BooksController@index');
Route::get('books/getList',  'BooksController@getDatatable');
Route::get('books/add', 'BooksController@create');
Route::post('books/add', 'BooksController@store');
# log test 9-3-2020 test
Route::get('log', 'LogController@index');
Route::get('log/getList', 'LogController@getData');
Route::post('log/add', 'LogController@postVideo');
Route::get('log/view', 'LogController@getVideo');
# update 10-3-2020
Route::post('log/add_comment', 'LogController@addComment');
# log 10-3-2020 show admin lms comment
Route::get('admin/lms_comment', 'LogController@lmsComment');
# log 10-3-2020 layout count lsm comment (ajax) setInterVal
Route::post('log/count_lms_comment', 'LogController@count_lms_comment');
# log 10-3-2020 reply lsm comment (ajax)
Route::post('log/lms_comment_reply', 'LogController@lms_comment_reply');
# log 11-3-2020 layout count lsm reply (ajax) setInterVal
Route::post('log/count_lms_notification_comment', 'LogController@count_lms_notification_comment');
# log 11-3-2020 view series comment
Route::get('learning-management/comment', 'LogController@view_comment_reply');
# log 11-3-2020 add comment in list comment
Route::post('log/add_comment_reply', 'LogController@add_comment_reply');
# log 13-3-2020 iframe test block download video
Route::get('log/iframe', 'LogController@iframe');
# log 16-3-2020 test token url get network .ts video
Route::get('/upload','LogController@test_public');
# log 16-3-2020 test token url get network .ts video
Route::get('log/test','LogController@ts');
Route::get('/public/uploads/lms/series/demo_gioithieu/', function () {
    return redirect('log/test');
});
# 30-9-2020
// Route::get('lms/categories/detail/{slug}', 'LmsCategoryDetailController@index');
Route::get('lms/series', 'LmsSeriesController@index');
Route::get('lms/series/add', 'LmsSeriesController@create');
Route::get('lms/seriessexam/add', 'LmsSeriesController@createexam');
Route::post('lms/series/add', 'LmsSeriesController@store');
Route::get('lms/series/edit/{slug}', 'LmsSeriesController@edit');
Route::patch('lms/series/edit/{slug}', 'LmsSeriesController@update');
Route::delete('lms/series/delete/{slug}', 'LmsSeriesController@delete');
Route::get('lms/series/getList/', 'LmsSeriesController@getDatatable');
Route::post('lms/series/import-excel', 'LmsSeriesController@importExel');
# 30-9-2020
Route::get('lms/{series}/content', 'LmsContentController@index');
Route::get('lms/{series}/content/view/{slug}', 'LmsContentController@view');
Route::get('lms/{series}/content/getView', 'LmsContentController@getView');

Route::get('lms/{series}/content/add', 'LmsContentController@create');
Route::post('lms/{series}/content/add', 'LmsContentController@store');

Route::get('lms/{series}/content/add/after/{slug}', 'LmsContentController@createAfter');
Route::post('lms/content/add/after', 'LmsContentController@storeAfter');

Route::get('lms/{series}/content/edit/{slug}', 'LmsContentController@edit');
Route::patch('lms/{series}/content/edit/{slug}', 'LmsContentController@update');
Route::delete('lms/{series}/content/delete/{slug}', 'LmsContentController@delete');
Route::get('lms/{series}/content/getList', 'LmsContentController@getDatatable');
Route::post('lms/{series_slug}/content/import-exams', 'LmsContentController@importExams');
Route::post('lms/{series_slug}/content/import-mucluc', 'LmsContentController@importMucLuc');
# 01-10-2020
Route::get('lms/class/{slug}', 'ParentsController@lmsClass');
Route::get('lms/class/{slug}/add', 'LmsClassController@create');
Route::post('lms/class/{slug}/add', 'LmsClassController@store');
Route::get('lms/class/edit/{id}/{slug}', 'LmsClassController@edit');
Route::patch('lms/class/edit/{id}/{slug}', 'LmsClassController@update');
Route::delete('lms/class/delete/{id}/{slug}', 'LmsClassController@delete');
Route::get('lms/class/getList/{slug}', [
  'as'   => 'lmsclass.dataTable',
  'uses' => 'LmsClassController@getDatatable'
]);
Route::post('updateTimeVideo', 'StudentLmsController@updateTimeVideo')->name('updateTimeVideo');
Route::post('finishTimeVideo', 'StudentLmsController@finishTimeVideo')->name('finishTimeVideo');
Route::post('nextUrl', 'StudentLmsController@nextUrl')->name('nextUrl');
Route::post('testLog', 'StudentLmsController@lms_test_log')->name('testLog');
/*Route::get('learning-management/lesson/exercise/{combo_slug}/{series}/{slug}', 'StudentLmsController@studentExercise');*/

Route::get('test/{combo_slug}/{slug?}/{stt?}',  'StudentLmsController@flashCard');
Route::get('learning-management/lesson/exercise/{combo_slug}/{series}/{slug}', 'StudentLmsController@studentExercises');

Route::get('learning-management/lesson/audit/{combo_slug}/{series}/{slug}', 'StudentLmsController@studentAudit');
Route::post('learning-management/lesson/audit/{combo_slug}/{series}/{slug}', 'StudentLmsController@storeResut');
Route::get('stream-video/save', 'LmsContentController@saveStreamVideo');

Route::get('log-controller/check-send-mail', 'LogController@checkSendMail');

Route::get('lms/class-content', 'LmsClassContentsController@index');
Route::get('lms/class-content/add', 'LmsClassContentsController@create');
Route::post('lms/class-content/add', 'LmsClassContentsController@store');
Route::get('lms/class-content/edit/{slug}', 'LmsClassContentsController@edit');
Route::patch('lms/class-content/edit/{slug}', 'LmsClassContentsController@update');
Route::delete('lms/class-content/delete/{slug}', 'LmsClassContentsController@delete');
Route::get('lms/class-content/getList', [
  'as'   => 'lmsclass_content.dataTable',
  'uses' => 'LmsClassContentsController@getDatatable'
]);

Route::get('lms/class-content/detail/{id}', 'LmsClassContentsController@detail');
Route::patch('lms/class-content/detail/edit/{slug}', 'LmsClassContentsController@update');
Route::delete('lms/class-content/detail/delete/{slug}', 'LmsClassContentsController@delete');
Route::get('lms/class-content/detail-datatable/getList/{id}', [
  'as'   => 'lmsclass_content_detail.dataTable',
  'uses' => 'LmsClassContentsController@detailDatatable'
]);

Route::post('lms/class-content/ajax-update-status', 'LmsClassContentsController@update_status');

Route::post('lms/content/ajax-update-try', 'LmsContentController@update_try');

Route::get('log/test', 'LogController@test');

Route::post('learning-management/ajaxcheckout', 'StudentLmsController@ajaxcheckout');
Route::post('learning-management/razorpaySuccess', 'StudentLmsController@razorpaySuccess');


//thanh toán momo
Route::get('payments/lms/{slug}', 'PaymentsController@lmsPayments');
Route::get('payments/momoqr/{slug}', 'PaymentsController@getMomoQr');
Route::get('payments/atm/{bankcode}/{slug}', 'PaymentsController@getAtm');
Route::post('payments/transfer', 'PaymentsController@atmTransfer');
Route::post('payments/transfer/delete', 'PaymentsController@delatmTransfer');


// comments


Route::post('comments/add', 'CommentController@store');
Route::post('comments/update', 'CommentController@update');
Route::post('comments/index', 'CommentController@index');

Route::get('comments/index', 'CommentController@indexadmin');
Route::get('comments/index/getExamList', 'CommentController@getDatatable');

Route::post('comments/getComments', 'CommentController@getComments');

Route::post('comments/reply', 'CommentController@reply');
Route::post('comments/countComments', 'CommentController@countComments');

Route::get('lms/exam-categories/comments', 'CommentController@listComments');
Route::get('lms/exam-categories/comments/getExamList', 'CommentController@listgetDatatable');


Route::get('lms/flashcard', 'FlashcardController@index');
Route::get('lms/flashcard/getList',  'FlashcardController@getDatatable');
Route::get('lms/flashcard/add', 'FlashcardController@create');
Route::post('lms/flashcard/add', 'FlashcardController@store');
Route::get('lms/flashcard/edit/{slug}', 'FlashcardController@edit');
Route::patch('lms/flashcard/edit/{slug}', 'FlashcardController@update');

Route::get('lms/flashcard/view/{slug}', 'FlashcardController@show');
Route::get('/lms/flashcard/show/{slug}', 'FlashcardController@getFlashcard');
Route::get('/lms/flashcard-detail/add/{slug}', 'FlashcardController@createdetail');
Route::post('/lms/flashcard-detail/add', 'FlashcardController@storedetail');
Route::get('/lms/flashcard-detail/edit/{slug}', 'FlashcardController@editdetail');
Route::patch('/lms/flashcard-detail/edit/{slug}', 'FlashcardController@updatedetail');

//Route::post('/lms/flashcard-detail/add', 'FlashcardController@storedetail');

//Question bank
// Route::get('exams/questionbank', 'QuestionBankController@index');
// 
//Route::get('exams/questionbank/add-question/{slug}', 'QuestionBankController@create');
// Route::get('exams/questionbank/view/{slug}', 'QuestionBankController@show');

// Route::post('exams/questionbank/add', 'QuestionBankController@store');
// Route::get('exams/questionbank/edit-question/{slug}', 'QuestionBankController@edit');
// Route::patch('exams/questionbank/edit/{slug}', 'QuestionBankController@update');
// Route::delete('exams/questionbank/delete/{id}', 'QuestionBankController@delete');
// Route::get('exams/questionbank/getList',  'QuestionBankController@getDatatable');

// Route::get('exams/questionbank/getquestionslist/{slug}', 
//      'QuestionBankController@getQuestions');