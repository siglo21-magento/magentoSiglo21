require(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'Magento_Customer/js/customer-data'
    ],
    function( $, modal, customerData ) {            
    
        $(document).ready(function(){
    
            var deleted = JSON.parse(localStorage.getItem('itemsDeleted'));
            
            if(deleted !== null){
    
                displayDeleteds(deleted);
    
            }
    
            $('.toast__close').click(function(e){
                e.preventDefault();
                var parent = $(this).parent('.toast');
                parent.fadeOut("slow");            
            });

            $(document).on('click', '.btn-show-inventory', function(e){
                var sku = $(this).data('sku');
                e.preventDefault();
                openModalShowInventory(sku);

            });
            
    
        });    
    
        function openModalShowInventory(sku){
            $.ajax({
                url: BASE_URL + 'getinventory/index/inventory',
                type: "post",
                data: {
                    sku : sku
                },
                dataType: "json",            
                cache: false
            })
            .done(function (json) {
                console.log(json);
                openModal(json);               
            })
            .fail(function (e) {            
                alert("error");
            });
            
        }
    
        function openModal(json) {
            if ($('#inventory-popup').length) {
                var options = {
                    type: 'popup',
                    modalClass: 'modal_inventory_popup',
                    responsive: true,
                    innerScroll: true,
                    title: '',
                    buttons: [],
                    opened: function($Event) {
                        $('.modal-header button.action-close', $Event.srcElement).hide();
                    }
                };
                var inventoryPopup = modal(options, $('#inventory-popup'));            
                $('#inventory-popup').trigger('openModal');   
                $("#pop-inventory-title-product").empty().text(json.title);
                $("#img-product-title").attr('src', json.img).attr('title', json.title);
                $(".content-inventory-office").empty().append(json.content);
            }
        }
        
    });