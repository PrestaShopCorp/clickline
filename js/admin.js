$(document).ready(function(){
   $("#clickline-settings .clickline-menu-option").click(function(event){
       var settings_block = $("#clickline-configuration");
       var id = $(this).attr('id');
       if(id=='settings-option'){
           settings_block.slideToggle();
       }
       else{
           settings_block.slideUp();
       }
   }) 
});

