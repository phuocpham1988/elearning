@extends('layouts.sitelayout')

@section('content')

  <!-- Page Banner -->
    <section class="cs-primary-bg cs-page-banner" style="margin-top: 110px;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    @if($lms_series)
                    <h2 class="cs-page-banner-title">{{$lms_series->title}}</h2>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <!-- /Page Banner -->

   <div>
        <div class="container">
            <div class="row cs-row">
                <!-- Side Bar -->
                <div class="col-md-3">
                    <!-- Icon List  -->
                    <ul class="cs-icon-list">

                    @if(count($lms_cates))

                         @foreach($lms_cates as $category)

                          <li id={{$category->slug}}><a href="{{URL_VIEW_ALL_LMS_CATEGORIES.'/'.$category->slug}}">{{$category->category}}</a></li>

                          @endforeach

                       @else

                         <h4>{{getPhrase('no_categories_are_available')}}</h4> 

                   @endif 
                       
                    </ul>
                    <!-- /Icon List  -->
                </div>
                <!-- Main Section -->
                 @if(count($contents))
                <div class="col-md-9">
                 
                    <div class="row">
                
                 @foreach($contents as $content)
                        <div class="col-md-4 col-sm-6">
                        <!-- Product Single Item -->
                     <div class="cs-info-box-bordered btm30 cs-animate">
                        <div class="cs-icon color-teal">
                            @if($content->image) 
                            <img src="{{IMAGE_PATH_UPLOAD_LMS_CONTENTS.$content->image}}" style="width: 200px;">
                            @else
                             <img src="{{IMAGE_PATH_EXAMS_DEFAULT}}" style="width: 200px;">
                            @endif
                        </div>
                        <h4>{{$content->title}}</h4>

                        @if($content->content_type == 'file')

                           <p><a href="{{URL_DOWNLOAD_LMS_CONTENT.$content->slug}}">{{getPhrase('download_file')}}</a></p>

                        @elseif($content->content_type == 'url' || $content->content_type == 'video_url' || $content->content_type == 'audio_url')
                        
                           <p class=" btn btn-blue btn-sm btn-radius"><a href="{{$content->file_path}}" target="_blank"><!-- {{getPhrase('view')}} --> 見る </a></p>

                        @elseif($content->content_type == 'iframe')
                        
                            <p class=" btn btn-blue btn-sm btn-radius"><a href="{{URL_LMS_VIDEO_CONTENT.$content->slug.'/'.$lms_series->id}}" target="_blank"><!-- {{getPhrase('view')}} --> 見る </a></p>

                        @endif

                    </div>
                    </div>
                     @endforeach   
                       <!-- Pagination -->
              
                    <div class="row text-center">
                        <div class="col-sm-12">
                            <ul class="pagination cs-pagination ">
                                {{ $contents->links() }}
                            </ul>
                        </div>
                    </div>
                    <!-- /Pagination -->
                   
                       
                       
                    </div>
                
                    
                </div>
                 @endif
            </div>
        </div>
    </div>


  

@stop

@section('footer_scripts')
<script>
    var my_slug  = "{{$lms_cat_slug}}";

    if(!my_slug){

        $(".cs-icon-list li").first().addClass("active");
    }
    else{

        $("#"+my_slug).addClass("active");
    }


    

</script>
 

 
 
@stop