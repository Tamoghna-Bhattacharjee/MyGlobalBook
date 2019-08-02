<?php
include('includes/header.php');

if(isset($_GET['update']))
{
    if($_GET['update'] == 'success')
        $update_success = "<div class='alert alert-success mt-1'>Update Successful.<span class='close' data-dismiss='alert'>&times;</span></div>";
    elseif($_GET['update'] == 'failed')
        $update_success = "<div class='alert alert-warning mt-1'>Update Unsuccessful.<span class='close' data-dismiss='alert'>&times;</span></div>";
    else
        header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
    
}

if(isset($_GET['password_change']))
{
    if($_GET['password_change'] == 'success')
        $password_change_message = "<div class='alert alert-success mt-1'>Password changed successfully.<span class='close' data-dismiss='alert'>&times;</span></div>";
    elseif($_GET['password_change'] == 'failed')
        $password_change_message = "<div class='alert alert-warning mt-1'>Failed to change password.<span class='close' data-dismiss='alert'>&times;</span></div>";
    else
        header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
}

//update detail
if(isset($_POST['update_detail']))
{
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    if(filter_var($_POST['email'] , FILTER_VALIDATE_EMAIL))
    {
        $email = filter_var($_POST['email'] , FILTER_VALIDATE_EMAIL);
        
        //to check weather the email is already been used or not
        $email_check_query_result = mysqli_query($conn , "SELECT username FROM users WHERE email = '$email'");
        $u = mysqli_fetch_assoc($email_check_query_result);
        
        if(mysqli_num_rows($email_check_query_result) == 0 || $u['username'] == $userLoggedIn)
        {
            $update_detail = mysqli_query($conn , "UPDATE users SET first_name = '$fname' , last_name = '$lname' , email = '$email' WHERE username = '$userLoggedIn'");
            
            if($update_detail === true)
            {
                header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?update=success");  
            }
            else
                header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?update=failed");
        }
        else
            $email_typeError_message = "<div class='alert alert-danger mt-1'>Email already been used.Try a different one!!<span class='close' data-dismiss='alert'>&times;</span></div>";
    }
    else
        $email_typeError_message = "<div class='alert alert-danger mt-1'>Enter email in correct format.<span class='close' data-dismiss='alert'>&times;</span></div>";
}

//change password
if(isset($_POST['change_password']))
{
    $oPassword = md5($_POST['opassword']);
    $nPassword = md5($_POST['npassword']);
    $cPassword = md5($_POST['cpassword']);
    
    //check if old password is correct or not
    $old_password_check = mysqli_query($conn , "SELECT password FROM users WHERE username = '$userLoggedIn'");
    $p = mysqli_fetch_assoc($old_password_check);
    if($p['password'] == $oPassword)
    {
        if($nPassword == $cPassword)
        {
            $update_password = mysqli_query($conn , "UPDATE users SET password = '$nPassword' WHERE username = '$userLoggedIn'");
            
            if($update_password === true)
            {
                header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?password_change=success");  
            }
            else
                header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?password_change=failed");
        }
        else
            $password_message = "<div class='alert alert-danger mt-1'>Please make sure you have confirm your password correctly!<span class='close' data-dismiss='alert'>&times;</span></div>";
    }
    else
        $password_message = "<div class='alert alert-danger mt-1'>Please enter your old password correctly.<span class='close' data-dismiss='alert'>&times;</span></div>";
}
?>

<div class="container" style="margin: 110px auto 0;">
    <div id="settings_page_body">
        
        <div id="change-profile-pic">
            <h4 class="mb-md-3 mb-sm-2">Change Profile Pic</h4>
            <img src="<?php echo $user_info['profile_pic'];?>" alt="profile pic" class="img-fluid">
            <a href="upload.php" role="button" class="btn btn-lg btn-info ml-md-4 ml-sm-2">Upload new profile pictuer</a>
        </div><hr />
        
        <div class="row">
           <!--update detail-->
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post" class="col-md-6 col-sm-12">
                <h4 class="mb-md-3 mb-sm-2">Change Your Deatils</h4>
                <div class="form-group row">
                    <label for="first_name" class="col-md-3 col-sm-12 col-form-label">First Name:</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $user_info['first_name'];?>" autocomplete="off">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="last_name" class="col-md-3 col-sm-12 col-form-label">Last Name:</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $user_info['last_name'];?>" autocomplete="off">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="email" class="col-md-3 col-sm-12 col-form-label">Email:</label>
                    <div class="col-md-9">
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $user_info['email'];?>" autocomplete="off">
                    </div>
                </div>
                <?php echo $email_typeError_message;?>
                <?php echo $update_success;?>
                <button class="btn btn-block btn-info" type="submit" name="update_detail">Update Details</button>
            </form>
            <!--update detail-->
            
            
            <!--uppdate password-->
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post" class="col-md-6 col-sm-12">
                <h4 class="mb-0">Change Your Password</h4><span class="text-danger mt-0 blockquote-footer">Password must contain atleast 8 charecters</span>
                <div class="form-group">
                    <label for="opassword" class="col-form-label sr-only">Old Password</label>
                    <input type="password" class="form-control" id="opassword" name="opassword" placeholder="Old Password" required pattern=".{8,}">
                </div>
                
                <div class="form-group">
                    <label for="npassword" class="col-form-label sr-only">New Password</label>
                    <input type="password" class="form-control" id="npassword" name="npassword" placeholder="New Password" required pattern=".{8,}">
                </div>
                
                <div class="form-group">
                    <label for="cpassword" class="col-form-label sr-only">Confirm Password</label>
                    <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="Confirm Password" required pattern=".{8,}">
                </div>
                <?php echo $password_message;?>
                <?php echo $password_change_message;?>
                <button class="btn btn-block btn-info" type="submit" name="change_password">Change Password</button>
            </form>
            <!--uppdate password-->
        </div><!--.row => 2--><br />
        
        <div style="text-align: center;"><button class="btn btn-lg btn-danger mx-auto" id="close_account">Close Account</button></div>
    </div>
    <!--#settings_page_body-->


</div>


<?php include('includes/footer2.php');?>

<script>
    var userLoggedIn = '<?php echo $userLoggedIn;?>';
    $('#close_account').click(function(){
        bootbox.confirm({
            message: "<h4>Do you want to close your account?</h4><p>If you want to close your account, your friends can't see you and your posts any more!!</p><p>You can reopen your account anytime by simply logging in.</p><p>If you press <span style='font-weight: 700;'>'Yes'</span> it will make you autometically logged out.</p>",
            buttons: {
                confirm: {
                    label: 'Yes',
                },
                cancel: {
                    label: 'No'
                }
            },
            callback: function (result) {
                if(result)
                {
                    $.post("includes/ajax/ajax_closeAccount.php" , {result: result , userLoggedIn: userLoggedIn} , function(){
                        window.location.href = "logout.php";
                    });
                }       
            }
        }); 
    });
</script>  
</body>

</html>
   
<?php
mysqli_close($conn);
?> 