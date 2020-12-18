<?php
    if(isset($_POST['submit'])){

    }
?>
<!DOCTYPE html>
<html>
<head>
    <?php include_once('../meta.php');?>
    <title>test - Altyn Bilim</title>
    <?php include_once('style.php');?>
    <style type="text/css">
        input[type='file']{
            display: none;
        }
        .img-upload-style{
            position: relative;
            z-index: 1;
            border:2px dashed gray;
            border-radius: 5px;
            display: inline-block;
            padding:5px 0;
            width: 100%;
            cursor: pointer;
            font-size: 18px;
            /*font-weight: bold;*/
            color:gray;
            transition:0.1s;
            /*background-image: url('../img/123.JPG');*/
        }
        .img-upload-style:hover{
            border-color:#8BA0FD;
            color:#8BA0FD;
            transition:0.1s;
        }
        #upload_img{
            /*max-width: 300px;*/
            /*max-height: 200px;*/
            width: auto;
            height: 100px;
        }
        .cover{
            line-height: 100px;
            height: 114px;
            position: absolute;
            z-index: 1000;
            width: 100%;
            top:0;
            /*height: 100%;*/
            background-color: rgba(0,0,0,0);
            font-size: 0px;
            transition:0.2s;
            color:white;
        }
        .cover:hover{
            cursor: pointer;
            background-color: rgba(0,0,0,0.7);
            font-size:150%;
            transition:0.2s;
        }
    </style>
</head>
<body>
    <?php if(isset($_FILES['img-upload'])){
        echo $_FILES['img-upload']['name'];
    }?>
    <center>
        <form action='tmp.php' id='form' method='post' enctype="multipart/form-data">
            <label id='img' for='img-upload' class='img-upload-style'>
                
                <center>Upload image</center>      
            </label>
            <input type="file" id='img-upload' onchange="checkSet('img-upload')" name="img-upload">
            <br>
            <input type="submit" class='btn btn-info btn-sm' name="submit">
        </form>
    </center>
<?php include_once('js.php');?>
<script type="text/javascript">
    function checkSet(obj){
            $(function(){
                if($("#"+obj).val()!=''){
                    $img_link = $("#"+obj).val();
                    $img_index = $img_link.lastIndexOf('\\');
                    $img = $img_link.substring($img_index+1);
                    $("#img").html("<img id='upload_img' src='../img/"+$img+"' class='img-responsive'>");
                    $("#img").parent().prepend("<div class='cover' onclick='removeFile(\"img-upload\")' class='delete'>Delete</div>");
                    // $("#img").css('z-index','-100');
                    // $("#img").attr("for",'');
                    // $("#img").attr("onclick","removeFile('img-upload')");
                }
            });
    }
    function removeFile(obj){
        var conf = confirm("Are your shure to remove file?");
        if(conf){
            $(function(){
                $("#"+obj).attr('value', ''); 
                $("#img").html("<center>Upload image</center>");
                $(".cover").remove();
                // $("#img").css('z-index','1');
                // $("#img").removeAttr("onclick");
                // $("#img").attr("for","img-upload");
            });
        }
    }
</script>
</body>
</html>