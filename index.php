<?php
include('includes/connect.php');
include('includes/function_modify.php');


//SIGNUP 
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['signup']))
{
    $fname = modify($_POST['firstname']);
    $lname = modify($_POST['lastname']);

    $phone = $_POST['phone'];
    
    $sEmail = modifyEmail($_POST['email_signup']);//signup email
    
    $saPassword = md5(strip_tags($_POST['password_signup1']));//actual password hashed
    $scPassword = strip_tags($_POST['password_signup2']);//confirmed password
    
    $gender = strtoupper($_POST['gender']);
    
    $dob = date("d M Y" , strtotime($_POST['dob']));
    
    /* VALIDATE EMAIL */
    if( filter_var( $sEmail , FILTER_VALIDATE_EMAIL ) )
    {
        $sEmail = filter_var( $sEmail , FILTER_VALIDATE_EMAIL );      //return the filtered data,  or false if failed
        
        
        //CHECK IF THE EMAIL IS ALREADY REGISTERED
        $ecquery = "SELECT email FROM users WHERE email='$sEmail'"; //email check query
        $ecResult = mysqli_query( $conn , $ecquery );//email check query result

        if(mysqli_num_rows($ecResult)>0)
        {
            $email_used = "<div class='alert alert-danger mt-1'>Email is already in use. Try with a different one!!<span class='close' data-dismiss='alert'>&times;</span></div>";
        }
        elseif(md5($scPassword) == $saPassword)
        {
            $sPassword = $saPassword;//hashed password
            
            //VERIFY THE LENGTH OF FIRST AND LAST NAME
            if(strlen($fname)>50 || strlen($lname)>50 || strlen($fname)<3 || strlen($lname)<3)
            {
                $nameError = "<div class='alert alert-danger mt-1 ml-3'>First name and Last name should range between 3 to 50<span class='close' data-dismiss='alert'>&times;</span></div>";
            }
            else
            {
                $username = strtolower($fname) . "_" . strtolower($lname) ;
                $check_username_query = "SELECT username FROM users WHERE username = '$username'";
                $check_username_result = mysqli_query($conn , $check_username_query);
                $i = 0;
                //GENERATE UNIQUE USERNAME
                while(mysqli_num_rows($check_username_result) !=0)
                {
                    $i++;
                    $username = $username . "_" . $i;
                    $check_username_result = mysqli_query($conn , $check_username_query);
                }
                
                //setting profile pics
                $rand = rand(1,2);
                if($rand == 1)
                    $profile_pic = "images/default_profilepics/head_deep_blue.png";
                elseif($rand == 2)
                    $profile_pic = "images/default_profilepics/head_wet_asphalt.png";
         
                
                $inserQuery = "INSERT INTO users (id , first_name , last_name , username , email , password , phone , gender , dob , signup_date , profile_pic , num_posts , num_likes , user_closed , friend_array) VALUES (NULL , '$fname' , '$lname' , '$username' , '$sEmail' , '$sPassword' , '$phone' , '$gender' , '$dob' , CURRENT_TIMESTAMP , '$profile_pic' , '0' , '0' , 'no' , ',')";
                
                $insertResult = mysqli_query($conn , $inserQuery);
                if($insertResult == true)
                {
                    session_start();
                    $_SESSION['fname']=$fname;
                    $_SESSION['lname']=$lname;
                    $_SESSION['username'] = $username;
                    $_SESSION['sEmail']=$sEmail;
                    
                    header('Location: registration_success.php');
                    exit();
                }
                
            }
            
        }
        else
            $confirmPassword = "<div class='alert alert-danger mt-1'>Confirm Password Correctly.<span class='close' data-dismiss='alert'>&times;</span></div>";
        
    }
    else
    {
        $iEmailFormat = "<div class='alert alert-danger mt-1'>Enter correct email address<span class='close' data-dismiss='alert'>&times;</span></div>";
    }
    
}
//SIGNUP


//LOGIN
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login']))
{
    $logEmail = modifyEmail($_POST['log_email']);
    $logPassword = md5(strip_tags($_POST['log_password']));
    
    $logEmail = filter_var($logEmail , FILTER_SANITIZE_EMAIL);  //sanitize email
    
    //VALIDATING EMAIL ==> validate basic format of email
    if(filter_var($logEmail , FILTER_VALIDATE_EMAIL))
    {
        $logEmail = filter_var($logEmail , FILTER_VALIDATE_EMAIL);
        
        $login_query = "SELECT * FROM users WHERE email='$logEmail' AND password='$logPassword'";
        $login_result = mysqli_query($conn , $login_query);
        
        if(mysqli_num_rows($login_result) == 1)
        {
            
            //reopen the closed account
            $user_closed_result = mysqli_query($conn , "SELECT * FROM users WHERE email='$logEmail' AND user_closed='yes'");
            if(mysqli_num_rows($user_closed_result) == 1)
            {
                $reopen_account = mysqli_query($conn , "UPDATE users SET user_closed='no' WHERE email='$logEmail'");
            }
            //reopen the closed account
            
            
            $row = mysqli_fetch_assoc($login_result);
            session_start();
            
//            $_SESSION['fname'] = $row['first_name'];
//            $_SESSION['lname'] = $row['last_name'];
            $_SESSION['username'] = $row['username'];
//            $_SESSION['email'] = $row['email'];
            
            header('Location: home_page.php');
            exit();
            
        }
        else
            $login_failure = "<div class='alert alert-danger mt-2'>Enter correct email/password.<span class='close' data-dismiss='alert'>&times;</span></div>";
        
    }
    else
        $email_format_error= "<div class='alert alert-danger mt-2'>Enter email in correct format!!<span class='close' data-dismiss='alert'>&times;</span></div>";
}

mysqli_close($conn);
//LOGIN
?>


<!DOCTYPE html>
<html lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <title>MyGlobalBook</title>

    <!--BOOTSTRAP CSS CDN-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">

    <!--ORIGINAL BOOTSTRAP CSS-->
    <link rel="stylesheet" href="Bootstrap-file/css/bootstrap.min.css">
    
    <!--JQUERY UI CSS-->
    <link rel="stylesheet" href="JS/jquery-ui-1.12.1/jquery-ui.min.css">
    
    <!--LOADING CUSTOM STYLESHEET-->
    <link rel="icon" type="image" href="images/brand_icon.jpg">
    <link rel="stylesheet" href="CSS/glyphicon.css">
    <link rel="stylesheet" href="CSS/index.css">
    
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <!-- content -->
    <div class="content">
        <h1 id="brand">MyGlobalBook</h1>
        <div class="container-fluid px-3">
            <div id="dialog"><!--form canvas-->
                <div class="row">
                   <div class="col-lg-1"></div>
                   <!-- login -->
                    <div class="col-lg-4 col-sm-12 m-3" id="login" >
                       <h3 id="login-heading" style="color: aliceblue;">Login Here</h3>
                       <blockquote class="blockquote" style="color: aliceblue;">
                           Log in here to connect to the society.
                       </blockquote>
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post" class="mt-3">
                            <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 row">
                                <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                    <label class="sr-only" for="emailid_login">Email Id</label>
                                    <input type="email" id="emailid_login" class="form-control" name="log_email" placeholder="Email Id" required value="<?php echo $logEmail;?>">
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 row">
                                <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                    <label class="sr-only" for="password_login">Password</label>
                                    <input type="password" id="password_login" class="form-control" name="log_password" placeholder="Password" required>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 row">
                                <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                    <?php echo $login_failure.$email_format_error;?>
                                </div>
                            </div>
                            </div>
                            <button type="submit" class="btn btn-info btn-lg" name="login">Log In</button>
                        </form>
                        <div class="row mt-3">
                            <div class="col-lg-12 col-md-12 col-sm-12 row">
                                <blockquote class="blockquote mx-3" style="color: aliceblue;">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odit beatae aliquam provident voluptatem quos totam assumenda, nostrum delectus, dolor dignissimos asperiores. Ad asperiores omnis, molestiae recusandae fuga id iste. Nulla.</blockquote>
                            </div>
                        </div>
                        
                    </div><!--#login-->
                    
                    <!--Signup-->
                    <div class="col-lg-6 col-sm-12 m-3" id="signup">
                       <h3 id="sine-heading" style="color: aliceblue;">Create an account</h3>
                       <blockquote class="blockquote" style="color: aliceblue;">
                           It's free and always will be.
                       </blockquote>
                       
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post" class="mt-3">
                                                    
                            <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 row">
                                <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                    <label class="sr-only" for="firstname">First Name</label>
                                    <input type="text" placeholder="First Name" id="firstname" class="form-control" name="firstname" required value="<?php echo $fname;?>">
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                    <label class="sr-only" for="lastname">Last Name</label>
                                    <input type="text" placeholder="Last Name" id="lastname" class="form-control" name="lastname" required value="<?php echo $lname;?>">
                                </div>
                                <?php echo $nameError;?>
                            </div>
                            
                            </div>
                            
                            <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 row">
                                <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                    <label class="sr-only" for="phone">Phone Number</label>
                                    <input type="tel" placeholder="Phone Number" id="phone" class="form-control" name="phone" pattern="[0-9]{10}" required value="<?php echo $phone;?>">
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                    <label class="sr-only" for="email_signup">Email</label>
                                    <input type="email" placeholder="your@email.com" id="email_signup" class="form-control" name="email_signup" required value="<?php echo $sEmail;?>">
                                    <?php echo $iEmailFormat;?>
                                </div>
                            </div>
                            </div>
                            
                            <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 row">
                                <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                    <label class="sr-only" for="password_signup">New Password</label>
                                    <input type="password" id="password_signup" class="form-control" name="password_signup1" placeholder="New Password" required pattern=".{8,}">
                                    <blockquote class="text-warning mt-1 mb-0"> -minimum 8 charecter required</blockquote>
                                </div>
                            </div>
                            </div>
                            
                            <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 row">
                                <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                    <label class="sr-only" for="password_signup2">Confirm Password</label>
                                    <input type="password" id="password_signup2" class="form-control" name="password_signup2" placeholder="Confirm Password" required pattern=".{8,}">
                                    <?php echo $confirmPassword;?>
                                </div>
                            </div>
                            </div>
                            
                            <div class="mt-3">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="male" name="gender" class="custom-control-input" value="male">
                                    <label class="custom-control-label" for="male" required style="color: aliceblue;">Male</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="female" name="gender" class="custom-control-input" value="female">
                                    <label class="custom-control-label" for="female" required style="color: aliceblue;">Female</label>
                                </div>
                            </div>
                            <div class="row mt-2">
                            <div class="col-lg-12 col-md-12 col-sm-12 row">
                                <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                    <label for="dob" class="col-form-label" style="color: aliceblue;">Birth-date</label>
                                    <input type="date" id="dob" class="form-control" name="dob" required>
                                </div>
                            </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg mt-3" name="signup">Sign Up</button>
                            <?php echo $email_used; ?>
                        </form>
                    </div><!--#signup-->
                </div><!--.row-->
            </div>
        </div>
    </div>

    <!-- particles.js container -->
    <div id="particles-js"></div>

     
         
    <!--JQUERY-->

    <!-- FIRST LOAD JQUERY CDN -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <!--SECOND LOAD THE LOCAL VERSION IF JQUERY CDN FAILS-->
    <script>
        window.jQuery || document.write('<script src="JS/jquery-3.3.1.min.js"><\/script>');
    </script>

    <!--JQUERY-->
    <!--BOOTSTRAP CDN-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) CDN-->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="Bootstrap-file/js/bootstrap.min.js"></script>

    <!--JQUERY UI LOADING-->
    <script src="JS/jquery-ui-1.12.1/jquery-ui.min.js"></script>

    <script src="JS/particles.min.js"></script>
    <script src="JS/app.js"></script>
</body>

</html>
