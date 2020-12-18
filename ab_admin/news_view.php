<?php
  include_once("../connection.php");
  try {
    $stmt = $conn->prepare("SELECT content FROM news WHERE type = :type");
    $stmt->bindParam(':type', $_GET['type'], PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo "Error : ".$e->getMessage()." !!!";
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0"/>
  <?php include_once('style.php');?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="../css/froala_editor.css">
  <link rel="stylesheet" href="../css/froala_style.css">
  <link rel="stylesheet" href="../css/plugins/code_view.css">
  <link rel="stylesheet" href="../css/plugins/draggable.css">
  <link rel="stylesheet" href="../css/plugins/colors.css">
  <link rel="stylesheet" href="../css/plugins/emoticons.css">
  <link rel="stylesheet" href="../css/plugins/image_manager.css">
  <link rel="stylesheet" href="../css/plugins/image.css">
  <link rel="stylesheet" href="../css/plugins/line_breaker.css">
  <link rel="stylesheet" href="../css/plugins/table.css">
  <link rel="stylesheet" href="../css/plugins/char_counter.css">
  <link rel="stylesheet" href="../css/plugins/video.css">
  <link rel="stylesheet" href="../css/plugins/fullscreen.css">
  <link rel="stylesheet" href="../css/plugins/file.css">
  <link rel="stylesheet" href="../css/plugins/quick_insert.css">
  <link rel="stylesheet" href="../css/plugins/help.css">
  <link rel="stylesheet" href="../css/third_party/spell_checker.css">
  <link rel="stylesheet" href="../css/plugins/special_characters.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.css">

  <style>
      body {
          text-align: center;
      }

      div#editor {
          width: 81%;
          margin: auto;
          text-align: left;
      }

      .ss {
        background-color: red;
      }
  </style>
</head>

<body>
  <div id='alert'></div>
  <div>
    <center><h1>For Teacher</h1></center>
  </div>
  <div id="editor">
    <div id='edit' style="margin-top: 30px;">
      <?php echo $result['content'];?>
    </div>
  </div>
  <div>
    <form id='news_form' method='post' action='admin_controller.php'>
      <input type="hidden" name="news_content" value=''>
      <input type="hidden" name="id" value='<?php echo $_GET['type'];?>'>
      <input type="submit" class='btn btn-lg btn-success' name="submit_news" value='Сақтау'>
    </form>
  </div>
  <div id='lll' style='width: 100%; height: 100%; position: fixed; top:0; background-color: rgba(0,0,0,0); z-index: 100;'>
    <center>
      <img src="../img/loader.gif" style='width: 10%; margin-top:25%;'>
    </center>
  </div>
  <?php include_once('js.php');?>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/mode/xml/xml.min.js"></script>

  <script type="text/javascript">
    $(document).ready(function(){
      $("#lll").css('display','none');
    });
    $(function(){
      $('#lll').hide().ajaxStart( function() {
        $(this).css('display','block');  // show Loading Div
      } ).ajaxStop ( function(){
        $(this).css('display','none'); // hide loading div
      });
    });
    $(document).on('submit','#news_form',(function(e) {
      $content = $("#edit .fr-view").html();
      $(this).find('input[name=news_content]').val($content)
      thisParent = $(this);
      e.preventDefault();
      $.ajax({
        url: "ajaxDb.php?<?php echo md5(md5('submitNews'))?>",
        type: "POST",
        data:  new FormData(this),
        contentType: false,
        cache: false,
        processData:false,
        beforeSend:function(){
          $('#lll').css('display','block');
        },
        success: function(dataS){
          $('#lll').css('display','none');
            // console.log(dataS);
            data = $.parseJSON(dataS);
            // console.log(data);
            if(data.success){
              $("#alert").html('<div class="alert alert-success alert-dismissible" role="alert" style="position: fixed; z-index: 10000; top:5%; width: 80%; left:10%; box-shadow: 0px 0px 10px green;"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><center><strong>Жаңалықтар базаға енгізілді</strong></center></div>');
            }
            else{
              console.log(data);
            }
          },
          error: function(dataS) 
          {
            console.log(dataS);
          }           
        });
    }));
  </script>

  <script type="text/javascript" src="../js/froala_editor.min.js" ></script>
  <script type="text/javascript" src="../js/plugins/align.min.js"></script>
  <script type="text/javascript" src="../js/plugins/char_counter.min.js"></script>
  <script type="text/javascript" src="../js/plugins/code_beautifier.min.js"></script>
  <script type="text/javascript" src="../js/plugins/code_view.min.js"></script>
  <script type="text/javascript" src="../js/plugins/colors.min.js"></script>
  <script type="text/javascript" src="../js/plugins/draggable.min.js"></script>
  <script type="text/javascript" src="../js/plugins/emoticons.min.js"></script>
  <script type="text/javascript" src="../js/plugins/entities.min.js"></script>
  <script type="text/javascript" src="../js/plugins/file.min.js"></script>
  <script type="text/javascript" src="../js/plugins/font_size.min.js"></script>
  <script type="text/javascript" src="../js/plugins/font_family.min.js"></script>
  <script type="text/javascript" src="../js/plugins/fullscreen.min.js"></script>
  <script type="text/javascript" src="../js/plugins/image.min.js"></script>
  <script type="text/javascript" src="../js/plugins/image_manager.min.js"></script>
  <script type="text/javascript" src="../js/plugins/line_breaker.min.js"></script>
  <script type="text/javascript" src="../js/plugins/inline_style.min.js"></script>
  <script type="text/javascript" src="../js/plugins/link.min.js"></script>
  <script type="text/javascript" src="../js/plugins/lists.min.js"></script>
  <script type="text/javascript" src="../js/plugins/paragraph_format.min.js"></script>
  <script type="text/javascript" src="../js/plugins/paragraph_style.min.js"></script>
  <script type="text/javascript" src="../js/plugins/quick_insert.min.js"></script>
  <script type="text/javascript" src="../js/plugins/quote.min.js"></script>
  <script type="text/javascript" src="../js/plugins/table.min.js"></script>
  <script type="text/javascript" src="../js/plugins/save.min.js"></script>
  <script type="text/javascript" src="../js/plugins/url.min.js"></script>
  <script type="text/javascript" src="../js/plugins/video.min.js"></script>
  <script type="text/javascript" src="../js/plugins/help.min.js"></script>
  <script type="text/javascript" src="../js/plugins/print.min.js"></script>
  <script type="text/javascript" src="../js/third_party/spell_checker.min.js"></script>
  <script type="text/javascript" src="../js/plugins/special_characters.min.js"></script>
  <script type="text/javascript" src="../js/plugins/word_paste.min.js"></script>

  <script>
    $(function(){
      $('#edit').froalaEditor()
    });
  </script>
</body>
</html>