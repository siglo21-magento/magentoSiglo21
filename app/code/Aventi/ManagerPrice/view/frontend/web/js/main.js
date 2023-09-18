require(
[
    'jquery',        
    'Magento_Customer/js/model/customer'
],
function( $, customer) {    
    
    $(window).load(function () {
        
        validateLogged();  
                           
    });

    $(document).ready(function(){
        
    })

    function validateLogged(){

        $.ajax({
            url: BASE_URL + 'managerprice/index/islogged',
            type: "get",
            dataType: "json",            
            cache: false
        })
        .done(function (json) {
            
            if(json){
                $(".action.top-links.theme-header-icon > .label").html("<b>Mi cuenta</b>");
            }           
        })
        .fail(function (e) {            
            alert("error");
        });

    }

    $(document).on('hover', '.showcart', function() {
                
        $("#minicart-content-wrapper .product-item-name").each(function(index){
            var link = $(this).children('a');           
            var str = link.text();
            var substr = str.substring(0, 35) + ' ...';            
            link.text(substr);                  

        })

    });
        

});
    