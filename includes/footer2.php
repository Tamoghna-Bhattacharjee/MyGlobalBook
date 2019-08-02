
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

<!--<script src="../JS/myGlobalBook.js"></script>-->
    
<script>
$(function(){
   
    var userLoggedIn = '<?php echo $userLoggedIn;?>';
    
    /*--------------------
        MESSAGE DROPDOWN
    ----------------------*/
    
    function getDropDownData(userLoggedIn , type)
    {
        var url;
        
        if(type == 'notification')
        {
            
        }
        
        if(type == 'message')
        {
            url = 'ajax_load_DropdownMessages.php';
        }
        
        if($('.dropdown-menu-messages').css('display') == 'none')
        {
            $('.dropdown-menu').slideDown();
            $.ajax({

                url: 'includes/ajax/' + url,
                type: 'POST',
                data: 'page=1&userLoggedIn='+userLoggedIn,
                cache: false,
                success: function(data){
                    $('.dropdown_items').html(data);
                }
            });
            
            $('.dropdown-menu-messages').scroll(function(){
                var innerHeight = $('.dropdown-menu-messages').innerHeight();
                var scrollTop = $('.dropdown-menu-messages').scrollTop();
                var page = $('.next_page_dropDownMessages').val();
                var moreMessages = $('.MoreMessages').val();

                if((scrollTop + innerHeight >= $('.dropdown-menu-messages')[0].scrollHeight) && moreMessages == 'true')
                {
                    $.ajax({
                        url: 'includes/ajax/' + url,
                        type: 'POST',
                        data: 'page='+ page +'&userLoggedIn='+userLoggedIn,
                        cache: false,
                        success: function(data){ 
                            $('.next_page_dropDownMessages').remove();
                            $('.MoreMessages').remove();
                            $('.dropdown_items').append(data);
                        }
                    });
                }
            });
        }
        else
        {
            $('.dropdown-menu').slideUp();
            $('.dropdown_items').html("");
        }
    }
    
    $('#envelope-dropdown').click(function(){
        
        getDropDownData( userLoggedIn , 'message' );
    });
    
    $(document).click(function(e){
        if(e.target.id != 'envelope-dropdown')
        {
            $('.dropdown-menu-messages').slideUp();
        }
    });
    /*---------------
        live search
    -----------------*/
    
    //making the input field to grew horizontally on focusin
    $('#live_search_field').focusin(function(){
        if(window.matchMedia("(min-width: 800px)").matches)
        {
            $(this).animate({width: '400px'} , 500 , function(){});
        }
    });
    
    //making the input field to short horizontally on focusout
    $('#live_search_field').focusout(function(){
        
        $('.search_results').slideUp();
        $(this).animate({width: '-=200px'} , 500 , function(){});
        
    });
    
    $('#live_search_field').keyup(function(){
        var value = $(this).val();
        $.post("includes/ajax/ajax_live_user_search.php" , {value: value , userLoggedIn: userLoggedIn} , function(data){
            $('.search_results').html(data);
            $('.search_results').slideDown().delay(500);
        });
    });
    
});   
</script>   