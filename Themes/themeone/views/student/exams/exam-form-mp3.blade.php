<div class="hikari-audio"> 
  <!-- Show audio file -->
  <?php if ($quiz->type == 1) { ?>
  <?php 
  $exam_path = PREFIX.'public/uploads/exams/'; 
  $rei_mondai = $quiz->category_id;
  $count_question = count($questions);
  $user_name = $user->username;
  ?>
  <script src="/Themes/themeone/assets/js/jquery-1.12.1.min.js"></script>
  <script type="text/javascript">
    function audioPlayer(){
      var currentSong = 0;
      var lenghtSong = jQuery("#playlist li a").length;
      var username = '<?php echo $user_name; ?>';
      jQuery("#audioPlayer")[0].src = jQuery("#playlist li a")[0];
      jQuery("#audioPlayer")[0].play();
      /*jQuery("#playlist li a").click(function(e){
       e.preventDefault(); 
       jQuery("#audioPlayer")[0].src = this;
       jQuery("#audioPlayer")[0].play();
       jQuery("#playlist li").removeClass("current-song");
       currentSong = jQuery(this).parent().index();
       jQuery(this).parent().addClass("current-song");
     });*/
     /*$.ajax({
         headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
         },
          url : "<?php echo AJAX_LOG_NGHE ?>",
          type : "post",
          data: {username: username, stt_question: currentSong},
          success : function (result){
         }
     });*/
      jQuery("#audioPlayer")[0].addEventListener("ended", function(){
          currentSong++;
          $.ajax({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
              },
               url : "<?php echo AJAX_LOG_NGHE ?>",
               type : "post",
                data: {username: username, stt_question: currentSong},
               success : function (result){
              }
          });
       jQuery("#playlist li").removeClass("current-song");
       jQuery("#playlist li:eq("+currentSong+")").addClass("current-song");
       jQuery("#audioPlayer")[0].src = jQuery("#playlist li a")[currentSong].href;
       jQuery("#audioPlayer")[0].play();
       if(currentSong == lenghtSong){
        jQuery("#audioPlayer")[0].pause();
        jQuery("#loa-icon").html('<i class="fa fa-flag"></i>');
        // currentSong = 0;
      }
    });
    }
  </script>
  <div style="pointer-events: none;">
    <!-- <span id="loa-icon"><img src="/public/uploads/exams/images/common/loa-icon.gif" style="width: 30px;"></span> -->
    <audio src="" controls id="audioPlayer" controlsList="nodownload" style="display: none;">
      Sorry, your browser doesn't support html5!
    </audio>
  </div>



  @if($rei_mondai == 1)
  <!-- <ul id="playlist" style="display: block; position: absolute; top: 0; left: 0; background: #ccc; z-index: 99999;"> -->
  <ul id="playlist" style="display: none;">
    <?php
    $i_question = 1;
    $elearning = 'https://elearning.hikariacademy.edu.vn/';
    foreach($questions as $question) {
      ?>
      <?php
      switch ($i_question) {
        case 1:
        echo '<li class="current-song"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/first.mp3'.'">'.'first.mp3'.'</a></li>';
        echo '<li class="current-song"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/mondai1.mp3'.'">'.'mondai1.mp3'.'</a></li>';
        echo '<li class="current-song"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei1.mp3'.'">'.'rei1.mp3'.'</a></li>';
        break;
        case 7:
        echo '<li class="current-song"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/mondai2.mp3'.'">'.'mondai2.mp3'.'</a></li>';
        echo '<li><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei2.mp3'.'">'.'rei2.mp3'.'</a></li>';
        break;
        case 14:
        //File giai lao
        echo '<li><a href="'.$exam_path.'rei/n'.$rei_mondai.'/kyuukei.mp3'.'">'.'/kyuukei.mp3'.'</a></li>';
        echo '<li class="current-song"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/mondai3.mp3'.'">'.'mondai3.mp3'.'</a></li>';
        echo '<li><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei3.mp3'.'">'.'rei3.mp3'.'</a></li>';
        break;
        case 20:
        echo '<li class="current-song"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/mondai4.mp3'.'">'.'mondai4.mp3'.'</a></li>';
        echo '<li><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei4.mp3'.'">'.'rei4.mp3'.'</a></li>';
        break;
        case 34:
        echo '<li class="current-song"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/mondai5.mp3'.'">'.'mondai5.mp3'.'</a></li>';
        break;
      }
      ?>
      
      @if ($i_question < 36)
        <li><a href="<?php  echo $exam_path.'files/common/N1/'.$i_question.'.mp3'; ?>"><?php echo 'Số ' .  $i_question; ?></a></li>
        <li><a href="<?php  echo $elearning . $question->question_file ?>"><?php echo 'Câu ' . $i_question; ?></a></li>
      @elseif ($i_question == 36)
        <li><a href="<?php  echo $exam_path.'files/common/N1/'.$i_question.'.mp3'; ?>"><?php echo 'Số ' .  $i_question; ?></a></li>
        <li class="current-song"><a href="<?php  echo $exam_path.'rei/n'.$rei_mondai.'/mondai53.mp3'; ?>">Mondai53</a></li>
        <li><a href="<?php  echo $elearning . $question->question_file ?>"><?php echo 'Câu ' . $i_question; ?></a></li>
      @endif


      <?php 
      if ($i_question == $count_question) {
        echo '<li class="current-song"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/finish.mp3'.'">'.'finish.mp3'.'</a></li>';
      } else {

      }
      $i_question++; 
    } 
    ?>
  </ul>
  @endif

  @if($rei_mondai == 2)
  <ul id="playlist" style="display: none">
    <!-- <ul id="playlist" style="display: none; position: absolute; top: 0; left: 0; background: #ccc; z-index: 99999;"> -->
    <?php
    $i_question = 1;
    $elearning = 'https://elearning.hikariacademy.edu.vn/';
    foreach($questions as $question) {
      ?>
      <?php
      switch ($i_question) {
        case 1:
        echo '<li class="current-song"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/first.mp3'.'">'.'first.mp3'.'</a></li>';
        echo '<li class="current-song"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/mondai1.mp3'.'">'.'mondai1.mp3'.'</a></li>';
        echo '<li class="current-song"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei1.mp3'.'">'.'rei1.mp3'.'</a></li>';
        break;
        case 6:
        echo '<li class="current-song"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/mondai2.mp3'.'">'.'mondai2.mp3'.'</a></li>';
        echo '<li><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei2.mp3'.'">'.'rei2.mp3'.'</a></li>';
        break;
        case 12:
        //File giai lao
        echo '<li><a href="'.$exam_path.'rei/n'.$rei_mondai.'/kyuukei.mp3'.'">'.'kyuukei.mp3'.'</a></li>';
        echo '<li class="current-song"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/mondai3.mp3'.'">'.'mondai3.mp3'.'</a></li>';
        echo '<li><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei3.mp3'.'">'.'rei3.mp3'.'</a></li>';
        break;
        case 17:
        echo '<li class="current-song"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/mondai4.mp3'.'">'.'mondai4.mp3'.'</a></li>';
        echo '<li><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei4.mp3'.'">'.'rei4.mp3'.'</a></li>';
        break;
        case 29:
        echo '<li class="current-song"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/mondai5.mp3'.'">'.'/mondai5.mp3'.'</a></li>';
        break;
      }
      ?>
      
      @if ($i_question <= 30)
        <li><a href="<?php  echo $exam_path.'files/common/N2/'.$i_question.'.mp3'; ?>"><?php echo 'Số ' .  $i_question; ?></a></li>
        <li><a href="<?php  echo $elearning . $question->question_file ?>"><?php echo 'Câu ' . $i_question; ?></a></li>
      @elseif ($i_question == 31)
        <li><a href="<?php  echo $exam_path.'files/common/N2/'.$i_question.'.mp3'; ?>"><?php echo 'Số ' .  $i_question; ?></a></li>
        <li class="current-song"><a href="<?php  echo $exam_path.'rei/n'.$rei_mondai.'/mondai53.mp3'; ?>">Mondai53</a></li>
        <li><a href="<?php  echo $elearning . $question->question_file ?>"><?php echo 'Câu ' . $i_question; ?></a></li>
      @endif

      <?php 
      if ($i_question == $count_question) {
        echo '<li class="current-song"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/finish.mp3'.'">'.'finish.mp3'.'</a></li>';
      }
      $i_question++; 
    } 
    ?>
  </ul>
  @endif

  <?php 
  //N3
  if ($rei_mondai == 3) {?>
  <ul id="playlist" style="display: none">
    <?php
    $i_question = 1;
    foreach($questions as $question) {
      ?>
      <?php
      switch ($i_question) {
        case 1:
        echo '<li class="current-song"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei1.mp3'.'">'.$exam_path.'rei/n'.$rei_mondai.'/rei1.mp3'.'</a></li>';
        break;
        case 7:
        echo '<li><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei2.mp3'.'">'.$exam_path.'rei/n'.$rei_mondai.'/rei2.mp3'.'</a></li>';
        break;
        case 13:
        //File giai lao
        echo '<li><a href="'.$exam_path.'files/common/giailao.mp3'.'">'.$exam_path.'files/common/giailao.mp3'.'</a></li>';
        echo '<li><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei3.mp3'.'">'.$exam_path.'rei/n'.$rei_mondai.'/rei3.mp3'.'</a></li>';
        break;
        case 16:
        echo '<li><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei4.mp3'.'">'.$exam_path.'rei/n'.$rei_mondai.'/rei4.mp3'.'</a></li>';
        break;
        case 20:
        echo '<li><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei5.mp3'.'">'.$exam_path.'rei/n'.$rei_mondai.'/rei5.mp3'.'</a></li>';
        break;
      }
      ?>
      <li><a href="<?php  echo $exam_path.'files/common/N3/'.$i_question.'.mp3'; ?>">common</a></li>
      <li><a href="<?php  echo SITE_URL . $question->question_file ?>"><?php  echo SITE_URL .$question->question_file; ?></a></li>
      <?php 
      if ($i_question == $count_question) {
        echo '<li class="current-song"><a href="'.$exam_path.'rei/finish.mp3'.'">'.$exam_path.'rei/finish.mp3'.'</a></li>';
      }
      $i_question++; 
    } 
    ?>
  </ul>
  <?php } ?>
  <?php 
  //N4
  if ($rei_mondai == 4) { ?>
  <ul id="playlist" style="display: none">
    <?php
    $i_question = 1;
    foreach($questions as $question) {
      ?>
      <?php
      switch ($i_question) {
        case 1:
        echo '<li class="current-song"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei1.mp3'.'">'.$exam_path.'rei/n'.$rei_mondai.'/rei1.mp3'.'</a></li>';
        break;
        case 9:
        echo '<li><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei2.mp3'.'">'.$exam_path.'rei/n'.$rei_mondai.'/rei2.mp3'.'</a></li>';
        break;
        case 16:
        echo '<li><a href="'.$exam_path.'files/common/giailao.mp3'.'">'.$exam_path.'files/common/giailao.mp3'.'</a></li>';
        echo '<li><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei3.mp3'.'">'.$exam_path.'rei/n'.$rei_mondai.'/rei3.mp3'.'</a></li>';
        break;
        case 21:
        echo '<li><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei4.mp3'.'">'.$exam_path.'rei/n'.$rei_mondai.'/rei4.mp3'.'</a></li>';
        break;
      }
      ?>
      <li><a href="<?php  echo $exam_path.'files/common/N4/'.$i_question.'.mp3'; ?>">common</a></li>
      <li><a href="<?php  echo SITE_URL.$question->question_file ?>"><?php echo SITE_URL.$question->question_file ?></a></li>
      <?php 
      if ($i_question == $count_question) {
        echo '<li class="current-song"><a href="'.$exam_path.'rei/finish.mp3'.'">'.$exam_path.'rei/finish.mp3'.'</a></li>';
      }
      $i_question++; 
    } 
    ?>
  </ul>
  <?php } ?>
  <?php  
   //N5
  if ($rei_mondai == 5) { ?>
  <ul id="playlist" style="display: none;">
    <?php
    $i_question = 1;
    foreach($questions as $question) {
      switch ($i_question) {
        case 1:
        echo '<li class="current-song" data-id="'.$question->id.'"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei1.mp3'.'">'.$exam_path.'rei/n'.$rei_mondai.'/rei1.mp3'.'</a></li>';
        break;
        case 8:
        echo '<li data-id="'.$question->id.'"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei2.mp3'.'">'.$exam_path.'rei/n'.$rei_mondai.'/rei2.mp3'.'</a></li>';
        break;
        case 14:
        echo '<li><a href="'.$exam_path.'files/common/giailao.mp3'.'">'.$exam_path.'files/common/giailao.mp3'.'</a></li>';
        echo '<li data-id="'.$question->id.'"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei3.mp3'.'">'.$exam_path.'rei/n'.$rei_mondai.'/rei3.mp3'.'</a></li>';
        break;
        case 19:
        echo '<li data-id="'.$question->id.'"><a href="'.$exam_path.'rei/n'.$rei_mondai.'/rei4.mp3'.'">'.$exam_path.'rei/n'.$rei_mondai.'/rei4.mp3'.'</a></li>';
        break;
      }
      ?>
      <li><a href="<?php  echo $exam_path.'files/common/N5/'.$i_question.'.mp3'; ?>">common</a></li>
      <li><a href="<?php  echo SITE_URL.$question->question_file ?>"><?php echo SITE_URL.$question->question_file ?></a></li>
      <?php 
      if ($i_question == $count_question) {
        echo '<li><a href="'.$exam_path.'rei/finish.mp3'.'">'.$exam_path.'rei/finish.mp3'.'</a></li>';
      }
      $i_question++; 
    } 
    ?>
  </ul>
  <?php } ?>
  <script>
    // loads the audio player
    audioPlayer();
  </script> 
  <?php } ?>
  <!--##### Show audio file -->
</div>