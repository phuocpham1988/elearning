 <?php $contents = $series->getContents();  
 $active_class = '';
 $active_class_id = 0;
 $content_image_path = IMAGE_PATH_UPLOAD_LMS_DEFAULT;
 if(isset($content) && $content)
 {
    if(isset($content->id)) 
        $active_class_id = $content->id;
    if($content->image)
    $content_image_path = IMAGE_PATH_UPLOAD_LMS_CONTENTS.$content->image;
 }
 ?>
@if($content)
<!-- <div class="row">
    <div class="col-md-3"> <img src="{{$content_image_path}}" class="img-responsive center-block" alt=""> </div>
    <div class="col-md-8 col-md-offset-1">
        <div class="series-details">
            <h2>{{$content->title}} </h2>
                {!! $content->description!!}
        </div>
    </div>
</div> -->
@endif
<div class="clearfix">&nbsp;</div>
 <ul class="lesson-list list-unstyled">
        @foreach($contents as $content)
        <?php 
            $active_class = '';
            if($active_class_id == $content->id)
                $active_class = ' active ';
            $url = '#';
            $type = 'File';
              $user = Auth::user();       
             if($user->role_id == 6){
                  $children_ids  = App\User::where('parent_id',$user->id)->pluck('id')->toArray();
                  $is_paid  = [];
                  foreach ($children_ids as $key => $value) {
                     $is_paid[]  = App\Payment::isParentPurchased($item->id, 'lms', $value);
                  }
                  // dd($is_paid);
                  $paid_staus  = in_array('notpurchased', $is_paid);
                   $paid  = FALSE;
                  if($paid_staus)
                   $paid  = TRUE;
                }
                else{
                   $paid = ($item->is_paid && !isItemPurchased($item->id, 'lms')) ? TRUE : FALSE;
                }
            if($content->file_path) {
                switch($content->content_type)
                {
                    case 'file': $url = VALID_IS_PAID_TYPE.$series->slug.'/'.$content->slug;
                                 $type = 'File';   
                                break;
                    case 'image': $url = IMAGE_PATH_UPLOAD_LMS_CONTENTS.$content->slug;
                                    $type = 'Image'; 
                    case 'url': 
                            //$url = $content->file_path;
                            $url = URL_STUDENT_LMS_SERIES_VIEW.$series->slug.'/'.$content->slug;
                                $type = 'Video';   
                                break;
                    case 'video_url':
                    case 'video':
                    case 'iframe': 
                                    $url = URL_STUDENT_LMS_SERIES_VIEW.$series->slug.'/'.$content->slug;
                                    $type = 'Video';    
                                    break;
                    case 'audio_url':
                    case 'audio': 
                                    $url = URL_STUDENT_LMS_SERIES_VIEW.$series->slug.'/'.$content->slug;
                                    $type = 'Audio';   
                                    break;
                }
            }
        ?>
         <?php if($paid) $url = '#'; ?>
        <li class="list-item {{$active_class}}">
        @if($content->content_type=='url')
        <a  href="{{$url}}" 
        @if($paid)
            onclick="showMessage('Bạn chưa mua khóa học này');" 
        @endif
        >{{$content->title}}   
        </a> 
        @else
        <a href="{{$url}}" 
        @if($paid)
            onclick="showMessage('Bạn chưa mua khóa học này');" 
        @endif
        >{{$content->title}}   
        </a>  
        @endif
            <span class="buttons-right pull-right">
                <a href="javascript:void(0);"> {{$type}}</a>
            </span> 
        </li>
        @endforeach
    </ul>