require([
    'ko',
    'jquery',
    'mage/url'
], function (ok, $, url) {

    /*$(document).on('click','.js-down,.js-up', function (){
        var input = $(this).closest('div').find('input');
        var val = input.val() || 1;
        if( $(this).hasClass('js-up')){
            val++;
        }else{
            val--;
        }
        val = (val <= 0) ? 1 : val;
        input.val(val);
    });

    $('.js-upload').click(function(e){
        e.preventDefault();
        var formData = new FormData(document.getElementById("upload-quick"));
        $.ajax({
            url: $('#upload-quick').attr('action'),
            type: "post",
            dataType: JSON,
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        })
            .done(function(json){

            });

    });

*/

});