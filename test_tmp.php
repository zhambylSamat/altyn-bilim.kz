<?php
function get_param($name) {
  //Be comaptible with both magic_quotes on and off.
  return get_magic_quotes_gpc() ? stripslashes($_POST[$name]) : $_POST[$name];
}
function html_escape($text) {
  //Escape html special chars from UTF8-encoded strings.
  return htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
}
//Get user data.
  if(!empty($_POST['back'])){
    $question = get_param('question');
    $text = get_param('text');
    $feedbackText = get_param('feedbackText');
    
    //Build question object to read the correct answer.
    require_once 'quizzes/quizzes.php';
    $builder = com_wiris_quizzes_api_QuizzesBuilder::getInstance();
    $questionObject = $builder->readQuestion($question);
    $correctAnswer = '';
    if ($questionObject->getCorrectAnswersLength() > 0) {
      $correctAnswer = $questionObject->getCorrectAnswer(0);
    }
  }else{
    $question = '';
    $correctAnswer = '';
    $text = '';
    $feedbackText = '';
  }
//Warning: in a production environment, $text variable should be sanitized in
//order to avoid XSS security issues.
  $question = html_escape($question);
  $correctAnswer = html_escape($correctAnswer);
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Level 3. Getting started with WIRIS quizzes</title>
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <!-- The following line includes the JavaScript part of the WIRIS quizzes library.-->
    <script type="text/javascript" src="quizzes/service.php?service=resource&amp;name=quizzes.js"></script>
    <!-- The following line includes the CKEditor JavaScript.-->
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
  </head>
<body> 
<div class="wrs_modal_dialogContainer wrs_modal_desktop wrs_stack" style="overflow: hidden; bottom: 0px; right: 10px; width: 582px;"><div class="wrs_modal_title_bar wrs_modal_desktop wrs_stack"><a class="wrs_modal_close_button" title="Close" role="button"></a><a class="wrs_modal_stack_button wrs_modal_desktop wrs_stack" title="Full-screen" role="button"></a><a class="wrs_modal_minimize_button wrs_modal_desktop wrs_stack" title="Minimise" role="button"></a><div class="wrs_modal_title">WIRIS EDITOR math</div></div><div class="wrs_modal_iframeContainer wrs_modal_desktop wrs_stack" style="width: 570px; height: 300px;"><iframe id="wrs_modal_iframe_id" class="wrs_modal_iframe wrs_modal_desktop wrs_stack" title="WIRIS Editor Modal Window" src="http://localhost/quizzes-gs-php/ckeditor/plugins/ckeditor_wiris/core/editor.html?lang=ru&amp;dir=ltr" style="width: 570px; height: 303px; margin: 6px;" frameborder="0"></iframe></div></div>
</body>
</html>