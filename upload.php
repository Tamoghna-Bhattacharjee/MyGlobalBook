<?php
include('includes/connect.php');
include('includes/classes/User.php');
include('includes/classes/Post.php');

session_start();
if(!$_SESSION)
    header('Location: index.php');

$userLoggedIn = $_SESSION['username'];

$user_detail_query = "SELECT * FROM users WHERE username = '$userLoggedIn'";
$user_detail_result = mysqli_query($conn , $user_detail_query);

$user_info = mysqli_fetch_assoc($user_detail_result);
//$userLoggedIn = $user_info['username'];
?>
<!DOCTYPE html>
<html lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <title></title>

    <!--BOOTSTRAP CSS CDN-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">

    <!--ORIGINAL BOOTSTRAP CSS-->
    <link rel="stylesheet" href="Bootstrap-file/css/bootstrap.min.css">
    
    <!--JQUERY UI CSS-->
    <link rel="stylesheet" href="JS/jquery-ui-1.12.1/jquery-ui.min.css">
    
    <!--LOADING CUSTOM STYLESHEET-->
    <link rel="icon" type="image" href="images/brand_icon.jpg">
    <link rel="stylesheet" href="CSS/glyphicon.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="CSS/upload_image_css/jquery.Jcrop.css">
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
     <nav class="navbar navbar-expand-md navbar-light bg-info fixed-top">
         <div class="container-fluid">
             <div class="navbar=header">
                 <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar-collapse">
                     <span class="sr-only">Collapse button</span>
                     <span class="navbar-toggler-icon"></span>
                 </button>
                 <a href="home_page.php" class="navbar-brand"><span id="brand-title" class="ml-2">MyGlobalBook</span></a>
                 <?php 
                    echo '<a href="'.$user_info['username'].'" class="navbar-brand" title="go to your Timeline"><span class="mx-3" id="user">Hello\'&nbsp;<img src="'.$user_info['profile_pic'].'" class="rounded-circle img-fluid mb-1" width="30px" alt="profile pic">&nbsp;'.$user_info['first_name'].'</span></a>';
                 ?>
             </div><!--.navbar-header-->
             
             <div class="collapse navbar-collapse" id="navbar-collapse">
                <div class="form-group mr-auto" id="live-search">
                        <div class="input-group">
                            <input type="search" class="form-control" placeholder="Search anything...">
                            <div class="input-group-append"><button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-search"></span></button></div>
                        </div>
                    </div>
                <ul class="nav navbar-nav ml-auto">
                    <li class="nav-item"><a href="home_page.php" class="nav-link"><span class="glyphicon glyphicon-home"></span></a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><span class="glyphicon glyphicon-envelope"></span></a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><span class="glyphicon glyphicon-bell"></span></a></li>
                    <li class="nav-item"><a href="friend_request.php" class="nav-link"><span class="material-icons">people</span></a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><span class="glyphicon glyphicon-cog"></span></a></li>
                    <li class="nav-item"><a href="logout.php" class="nav-link"><span class="glyphicon glyphicon-log-out"></span></a></li>
                </ul>
             </div><!--.collapse .navbar-collapse-->
             
         </div><!--.container-fluid-->
         
     </nav>


<?php 
    
$profile_id = $userLoggedIn;
$imgSrc = "";
$result_path = "";
$msg = "";

/***********************************************************
	0 - Remove The Temp image if it exists
***********************************************************/
	if (!isset($_POST['x']) && !isset($_FILES['image']['name']) ){
		//Delete users temp image
			$temppath = 'images/default_profilepics/'.$profile_id.'_temp.jpeg';
			if (file_exists ($temppath)){ @unlink($temppath); }
	} 


if(isset($_FILES['image']['name'])){	
/***********************************************************
	1 - Upload Original Image To Server
***********************************************************/	
	//Get Name | Size | Temp Location		    
		$ImageName = $_FILES['image']['name'];
		$ImageSize = $_FILES['image']['size'];
		$ImageTempName = $_FILES['image']['tmp_name'];
	//Get File Ext   
		$ImageType = @explode('/', $_FILES['image']['type']);
		$type = $ImageType[1]; //file type	
	//Set Upload directory    
		$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/images/default_profilepics';
	//Set File name	
		$file_temp_name = $profile_id.'_original.'.md5(time()).'n'.$type; //the temp file name
		$fullpath = $uploaddir."/".$file_temp_name; // the temp file path
		$file_name = $profile_id.'_temp.jpeg'; //$profile_id.'_temp.'.$type; // for the final resized image
		$fullpath_2 = $uploaddir."/".$file_name; //for the final resized image
	//Move the file to correct location
		$move = move_uploaded_file($ImageTempName ,$fullpath) ; 
		chmod($fullpath, 0777);  
		//Check for valid uplaod
		if (!$move) { 
			die ('File didnt upload');
		} else { 
			$imgSrc= "images/default_profilepics/".$file_name; // the image to display in crop area
			$msg= "Upload Complete!";  	//message to page
			$src = $file_name;	 		//the file name to post from cropping form to the resize		
		} 

/***********************************************************
	2  - Resize The Image To Fit In Cropping Area
***********************************************************/		
		//get the uploaded image size	
			clearstatcache();				
			$original_size = getimagesize($fullpath);
			$original_width = $original_size[0];
			$original_height = $original_size[1];	
		// Specify The new size
			$main_width = 500; // set the width of the image
			$main_height = $original_height / ($original_width / $main_width);	// this sets the height in ratio									
		//create new image using correct php func			
			if($_FILES["image"]["type"] == "image/gif"){
				$src2 = imagecreatefromgif($fullpath);
			}elseif($_FILES["image"]["type"] == "image/jpeg" || $_FILES["image"]["type"] == "image/pjpeg"){
				$src2 = imagecreatefromjpeg($fullpath);
			}elseif($_FILES["image"]["type"] == "image/png"){ 
				$src2 = imagecreatefrompng($fullpath);
			}else{ 
				$msg .= "There was an error uploading the file. Please upload a .jpg, .gif or .png file. <br />";
			}
		//create the new resized image
			$main = imagecreatetruecolor($main_width,$main_height);
			imagecopyresampled($main,$src2,0, 0, 0, 0,$main_width,$main_height,$original_width,$original_height);
		//upload new version
			$main_temp = $fullpath_2;
			imagejpeg($main, $main_temp, 90);
			chmod($main_temp,0777);
		//free up memory
			imagedestroy($src2);
			imagedestroy($main);
			//imagedestroy($fullpath);
			@ unlink($fullpath); // delete the original upload					
									
}//ADD Image 	

/***********************************************************
	3- Cropping & Converting The Image To Jpg
***********************************************************/
if (isset($_POST['x'])){
	
	//the file type posted
		$type = $_POST['type'];	
	//the image src
		$src = 'images/default_profilepics/'.$_POST['src'];	
		$finalname = $profile_id.md5(time());	
	
	if($type == 'jpg' || $type == 'jpeg' || $type == 'JPG' || $type == 'JPEG'){	
	
		//the target dimensions 150x150
			$targ_w = $targ_h = 150;
		//quality of the output
			$jpeg_quality = 90;
		//create a cropped copy of the image
			$img_r = imagecreatefromjpeg($src);
			$dst_r = imagecreatetruecolor( $targ_w, $targ_h );
			imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
			$targ_w,$targ_h,$_POST['w'],$_POST['h']);
		//save the new cropped version
			imagejpeg($dst_r, "images/default_profilepics/".$finalname."n.jpeg", 90); 	
			 		
	}else if($type == 'png' || $type == 'PNG'){
		
		//the target dimensions 150x150
			$targ_w = $targ_h = 150;
		//quality of the output
			$jpeg_quality = 90;
		//create a cropped copy of the image
			$img_r = imagecreatefrompng($src);
			$dst_r = imagecreatetruecolor( $targ_w, $targ_h );		
			imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
			$targ_w,$targ_h,$_POST['w'],$_POST['h']);
		//save the new cropped version
			imagejpeg($dst_r, "images/default_profilepics/".$finalname."n.jpeg", 90); 	
						
	}else if($type == 'gif' || $type == 'GIF'){
		
		//the target dimensions 150x150
			$targ_w = $targ_h = 150;
		//quality of the output
			$jpeg_quality = 90;
		//create a cropped copy of the image
			$img_r = imagecreatefromgif($src);
			$dst_r = imagecreatetruecolor( $targ_w, $targ_h );		
			imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
			$targ_w,$targ_h,$_POST['w'],$_POST['h']);
		//save the new cropped version
			imagejpeg($dst_r, "images/default_profilepics/".$finalname."n.jpeg", 90); 	
		
	}
		//free up memory
			imagedestroy($img_r); // free up memory
			imagedestroy($dst_r); //free up memory
			@ unlink($src); // delete the original upload					
		
		//return cropped image to page	
		$result_path ="images/default_profilepics/".$finalname."n.jpeg";

		//Insert image into database
		$insert_pic_query = mysqli_query($conn, "UPDATE users SET profile_pic='$result_path' WHERE username='$userLoggedIn'");
		header("Location: ".$userLoggedIn);
														
}// post x
?>
<div id="Overlay" style="width:100%; height:100%; border:0px #990000 solid; position:absolute; top:0px; left:0px; z-index:2000; display:none;"></div>
<div class="row">
<div class="col-4"></div>
<div class="col-6 column profileCanvas">


	<div id="formExample">
		
	    <p><b> <?=$msg?> </b></p>
	    
	    <form action="upload.php" method="post"  enctype="multipart/form-data">
	        Upload something<br /><br />
	        <input type="file" id="image" name="image" style="width:200px; height:30px; " /><br /><br />
	        <input type="submit" value="Submit" style="width:85px; height:25px;" />
	    </form><br /><br />
	    
	</div> <!-- Form-->  


    <?php
    if($imgSrc){ //if an image has been uploaded display cropping area?>
	    <script>
	    	$('#Overlay').show();
			$('#formExample').hide();
	    </script>
        <div class="row">
           
            <div id="CroppingContainer col-8" style="background-color:#FFF; overflow:hidden; border:2px #666 solid; z-index:2001;">

                <div id="CroppingArea" style="width:500px; max-height:400px; position:relative; overflow:hidden; margin:40px 0px 40px 40px; border:2px #666 solid; float:left;">
                    <img src="<?=$imgSrc?>" border="0" id="jcrop_target" style="border:0px #990000 solid; position:relative; margin:0px 0px 0px 0px; padding:0px; " />
                </div>

                <div id="InfoArea" style="width:180px; height:150px; position:relative; overflow:hidden; margin:40px 0px 0px 40px; border:0px #666 solid; float:left;">
                    <p style="margin:0px; padding:0px; color:#444; font-size:18px;">
                        <b>Crop Profile Image</b><br /><br />
                    </p>
                </div>

                <br /><br>

                <div id="CropImageForm" style="width:100px; height:30px; float:left; margin:10px 0px 0px 40px;">
                    <form action="upload.php" method="post" onsubmit="return checkCoords();">
                        <input type="hidden" id="x" name="x" />
                        <input type="hidden" id="y" name="y" />
                        <input type="hidden" id="w" name="w" />
                        <input type="hidden" id="h" name="h" />
                        <input type="hidden" value="jpeg" name="type" />
                        <?php // $type ?>
                        <input type="hidden" value="<?=$src?>" name="src" />
                        <input type="submit" value="Save" style="width:100px; height:30px;" />
                    </form>
                </div>

                <div id="CropImageForm2" style="width:100px; height:30px; float:left; margin:10px 0px 0px 40px;">
                    <form action="upload.php" method="post" onsubmit="return cancelCrop();">
                        <input type="submit" value="Cancel Crop" style="width:100px; height:30px;" />
                    </form>
                </div>

            </div>
            <!-- CroppingContainer -->

        </div>
	<?php 
	} ?>
</div>
</div> 
 
 
 
 
 <?php if($result_path) {
	 ?>
     
     <img src="<?=$result_path?>" style="position:relative; margin:10px auto; width:150px; height:150px;" />
	 
 <?php } ?>
 
 
    <br /><br />
    <!--JQUERY-->
        
        <!-- FIRST LOAD JQUERY CDN -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <!--SECOND LOAD THE LOCAL VERSION IF JQUERY CDN FAILS-->
        <script>
            window.jQuery || document.write('<script src="JS/jquery-3.3.1.min.js"><\/script>');
        </script>
        
    <!--JQUERY-->
    
    <!--bootbox CDN-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
    
    <!--BOOTSTRAP CDN-->

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) CDN-->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="Bootstrap-file/js/bootstrap.min.js"></script>
    
    <!--JQUERY UI LOADING-->
    <script src="JS/jquery-ui-1.12.1/jquery-ui.min.js"></script>
        
    <script src="JS/upload_image_js/jcrop_bits.js"></script>
    <script src="JS/upload_image_js/jquery.Jcrop.js"></script>
</body>

</html>