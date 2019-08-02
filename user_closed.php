<?php
include('includes/header.php');
?>

<div class="user-closed">
    <h1 class="font-weight-bold text-danger">This account is either closed or doesn't exsist!!</h1>
    <h2><a href="home_page.php" class="text-info" style="text-decoration: none;">Click here to go back!!</a></h2>
</div>

<?php
mysqli_close($conn);
include('includes/footer.php');
?>