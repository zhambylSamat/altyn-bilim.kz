<?php
 
  $subject_id = $_GET['subject_id'];
	$topic_id = $_GET['topic_id'];
	$subtopic_id = $_GET['subtopic_id'];

  $tutorial_video = 'materials/materials_tutorial_video.php';
  $tutorial_document = 'materials/materials_tutorial_document.php';
  $end_video = 'materials/materials_end_video.php';
  $test = 'materials/materials_test.php';
  $test_solve = 'materials/materials_test_solve.php';

  $dir = $tutorial_video;
  // if (isset($_GET['obj_content'])) {
  //   if ($_GET['obj_content'] == 'tutorial_video') {
  //     $dir = $tutorial_video;
  //   } else if ($_GET['obj_content'] == 'tutorial_document') {
  //     $dir = $tutorial_document;
  //   } else if ($_GET['obj_content'] == 'end_video') {
  //     $dir = $end_video;
  //   }
  // }

  $html = "";
  $html .= "<div class='btn-group' role='group'>";
  $html .= "<button type='button' class='btn btn-default select-materials ".(($dir==$tutorial_video) ? 'active' : '')."'
      data-dir='loads/".$tutorial_video."?subtopic_id=".$subtopic_id."'>Тақырыптық видео(лар)</button>";
  $html .= "</div>";

  $html .= "<div class='btn-group' role='group'>";
  $html .= "<button type='button' class='btn btn-default select-materials ".(($dir==$tutorial_document) ? 'active' : '')."'
      data-dir='loads/".$tutorial_document."?subtopic_id=".$subtopic_id."'>Тапсырма(лар)</button>";
  $html .= "</div>";

  $html .= "<div class='btn-group' role='group'>";
    $html .= "<button type='button' class='btn btn-default select-materials ".(($dir==$end_video) ? 'active' : '')."'
      data-dir='loads/".$end_video."?subtopic_id=".$subtopic_id."'>Қорытынды видео(лар)</button>";
  $html .= "</div>";

  $html .= "<div class='btn-group' role='group'>";
    $html .= "<button type='button' class='btn btn-default select-materials ".(($dir==$test) ? 'active' : '')."' data-dir='loads/".$test."?subtopic_id=".$subtopic_id."'>Тест</button>";
  $html .= "</div>";

  $html .= "<div class='btn-group' role='group'>";
    $html .= "<button type='button' class='btn btn-default select-materials ".(($dir==$test_solve) ? 'active' : '')."' data-dir='loads/".$test_solve."?subtopic_id=".$subtopic_id."'>Тест жауаптары</button>";
  $html .= "</div>";
?>


<div class="btn-group-vertical btn-group-justified material-btn-groups hidden-xs" role="group" data-subtopic-id='<?php echo $subtopic_id; ?>'>
  <?php echo $html; ?>
</div>

<div class="btn-group-vertical  material-btn-groups hidden-lg hidden-md hidden-sm" role="group" data-subtopic-id='<?php echo $subtopic_id; ?>'>
  <?php echo $html; ?>
 <!--  <div class="btn-group" role="group">
    <button type="button" class="btn btn-default select-materials <?php echo ($dir==$tutorial_video) ? 'active' : ''; ?>"
      data-dir='<?php echo 'loads/'.$tutorial_video.'?subtopic_id='.$subtopic_id; ?>'>Тақырыптық видео(лар)</button>
  </div>
  <div class="btn-group" role="group">
    <button type="button" class="btn btn-default select-materials <?php echo ($dir==$tutorial_document) ? 'active' : ''; ?>"
      data-dir='<?php echo 'loads/'.$tutorial_document.'?subtopic_id='.$subtopic_id; ?>'>Тапсырма(лар)</button>
  </div>
  <div class="btn-group" role="group">
    <button type="button" class="btn btn-default select-materials <?php echo ($dir==$end_video) ? 'active' : ''; ?>"
      data-dir='<?php echo 'loads/'.$end_video.'?subtopic_id='.$subtopic_id; ?>'>Қорытынды видео(лар)</button>
  </div>
  <div class='btn-group' role='group'>
    <button type='button' class='btn btn-default select-materials' data-dir='<?php echo 'loads/'.$test.'?subtopic_id='.$subtopic_id; ?>'>Тест</button>
  </div>
  <div class='btn-group' role='group'>
    <button type='button' class='btn btn-default select-materials' data-dir='<?php echo 'loads/'.$test_solve.'?subtopic_id='.$subtopic_id; ?>'>Тест жауаптары</button>
  </div> -->
</div>
<br><br>

<div id='material-content'>
	<?php
    include_once($dir);
	?>
</div>