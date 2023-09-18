define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'ko',
    'uiComponent',
    './../../Magento_Catalog/js/price-utils',
    './../../mage/storage',
    './../../Magento_Checkout/js/model/full-screen-loader',
    './../../Magento_Customer/js/customer-data'
], function ($,
             modal,
             ko,
             Component,
             priceUtils,
             storage,
             fullScreenLoader,
             customerData
) {
    "use strict";

    $(document).on('change', '#searchItem', function () {
      var optsList = $('#products')[0].options;
      var value = $(this).val();
      for (var i = 0; i < optsList.length; i++) {
        if (optsList[i].value === value) {
          setTimeout(function () {
            $("#btnSubmit").click();
          }, 500);
        }
      }
    });

    $(document).on('click', '.quickview-quickorder', function(){
        var id = $(this).data('id');
        var index = $(this).data('index');
        var timer = setInterval(function() {            
            if($(".product_quickview_content").length > 0){                
                var price = $('[data-price-type="finalPrice"]').attr('data-price-amount');
                
                $(".btn-cart").removeClass('btn-product btn-cart tocart action').addClass('send-attributes');
                $("#product_quickview_content"+id).find("form").attr('action', '');
                $("#product_quickview_content"+id).find("form").append('<input type="hidden" name="index" value="'+index+'" />');
                $("#product_quickview_content"+id).find("form").append('<input type="hidden" name="configprice" value="'+price+'" />');
                $("#product_quickview_content"+id).find("form").addClass('form-attributes');

                $.each(listProducts(), function (i, value) {           
                    if (value.id() == id && value.type() == 'configurable' && value.index() == index) {
                        $.each(value.attributesSelected(), function (i, v) {
                            var field = $("[name='"+v.name+"']");
                            if(!field.is(":hidden")){
                                field.val(v.value);
                                if(field.is("select")){
                                    field.trigger('change');
                                }
                            }
                        });
                    }
                });                
                clearInterval(timer);
            }            
        }, 1000);
    });
    $(document).on('change', '.super-attribute-select', function(){
        var price = $('[data-price-type="finalPrice"] > .price').text();
        price = price.split('$')[1];
        price = price.replace('.', '').replace(',', '.');        
        $('[data-price-type="finalPrice"]').attr('data-price-amount', price);
    });
    $(document).on('click', '.send-attributes', function(e){
      e.preventDefault();
      var array = $(".form-attributes").serializeArray();
      var id = '';
      var index = 0;      
      $.each(array, function (i, value) {
        if (value.name == 'product' && value.value != '') {
            id = value.value;
        }
        if (value.name == 'index' && value.value != '') {            
            index = parseInt(value.value);
        }
      });      
      var price = $("#product-price-"+id);      
      if(id !== 0){
        $.each(listProducts(), function (i, value) {            
          if (value.id() == id && value.type() == 'configurable' && value.index() == index) {              
            value.updateAttr(array);
            $.each(array, function (ind, v) {
              if (v.name == 'qty' && v.value != '') {
                var getPrice = price.data('price-amount');
                if(getPrice > 0){
                  value.price(getPrice);
                  value.status(true);
                  value.priceFormat(globalSelf.getFormattedPrice(getPrice));
                }
                value.qty(0);
                globalSelf.updateQuantityMethod(value, v.value);
              }
            });
            value.attrFormated(globalSelf.attrFormated(value));
          }
        });        
        $("#product_quickview_content"+id).parent().prev().find('[data-role="closeBtn"]').trigger('click');
      }
    });
    var listProducts = ko.observableArray([]);
    var globalSelf = '';
    return Component.extend({
        initialize: function () {
            globalSelf = this;
            this._super();
            localStorage.setItem('index', 1);
        },
        items: function (_id, _sku, _ref, _image, _name, _priceFormat, _totalFormat, _price, _qty, _status, _type, _url, _attributes, _formatInventory, _selected) {
            var index = parseInt(localStorage.getItem('index'));
            var _self = this;
            _self.id = ko.observable(_id);
            _self.sku = ko.observable(_sku);
            _self.ref = ko.observable(_ref);
            _self.name = ko.observable(_name);
            _self.priceFormat = ko.observable(_priceFormat);
            _self.totalFormat = ko.observable(_totalFormat);
            _self.price = ko.observable(_price);
            _self.qty = ko.observable(_qty);
            _self.status = ko.observable(_status);
            _self.image = ko.observable(_image);
            _self.type = ko.observable(_type);
            _self.url = ko.observable(_url);
            _self.attributes = ko.observable(_attributes);
            _self.attributesSelected = ko.observable(_selected);
            _self.attrFormated =ko.observable();
            _self.index = ko.observable(index);
            _self.formatInventory = ko.observable(_formatInventory);
            _self.updateAttr = function(selected) {
                _self.attributesSelected(selected);
            }
            index++;            
            localStorage.setItem('index', index);
        },
        showFormUpload: function () {

            if ($(".body-quick").is(":visible")) {
                $(".body-quick").slideUp("slow", function () {
                    $('.form-quick').slideDown('slow');
                });
            } else {
                $(".body-quick").slideDown("slow", function () {
                    $('.form-quick').slideUp('slow');
                });
            }
        },
        sendDateFile: function (e) {
            $('body').trigger('processStart');
            var _self = this;
            var formData = new FormData(document.getElementById("upload-quick"));
            $.ajax({
                url: $(e).data('action'),
                type: "post",
                dataType: "json",
                data: formData,
                cache: false,
                contentType: false,
                processData: false
            })
                .done(function (json) {
                    if (json.status == 200) {
                      listProducts.removeAll();
                      $.each(json.message, function (key, p) {
                        listProducts.push(new _self.items(p.id, p.sku, p.ref, p.image, p.name, p.priceFormat, p.totalFormat, p.price, p.qty, p.status, p.type, p.url, p.attributes, p.formatInventory))
                        listProducts.valueHasMutated();
                      });
                    }
                    $('body').trigger('processStop');
                    _self.showFormUpload();
                })
                .fail(function () {
                    alert("error");
                });
        },

        deleteProductId: function (sku) {
          listProducts.remove(function (remove) {
            return remove.sku == 'PG23';
          });
        },

        getProducts: function () {
            return listProducts;
        },
        deleteProduct: function () {
            listProducts.remove(this);
        },
        getFormattedPrice: function (price) {
            return priceUtils.formatPrice(price, window.orderPriceFormat);
        },
        updateQuantity: function (data, event) {
            var _self = this;
            var target = event.target;
            var action = 0;
            if (target.className.indexOf("js-up") > 0) {
                action = 1;
            } else {
                action = -1;
            }
            globalSelf.updateQuantityMethod(data, action);
        },
        updateQuantityInput: function (data, event) {
            var target = event.target;
            if (target.value != '') {
                var qty = parseInt(target.value) || 1;
                if (qty < 0) {
                    qty = 0;
                }
                data.qty(qty)
                var total = data.price() * data.qty();
                data.totalFormat(priceUtils.formatPrice(total, window.orderPriceFormat));
            }
        },
        exist: function (sku) {
            var total = 0;
            $.each(listProducts(), function (index, value) {
                if (sku == value.sku()) {
                    total = 1;
                    return 1;
                }
            });
            return total;
        },
        getProductsValid: function (force) {
            var products = [];

            if(typeof force == undefined) {
                force = false;
            }
            $.each(listProducts(), function (index, value) {
              if (value.status() || force || value.type() == 'configurable') {
                products.push({id: value.id(), qty: value.qty() , status : value.status(), type: value.type(), attributes: value.attributes(), selected: value.attributesSelected()})
              }
            });
            return products;
        },
        sendToCart: function (e) {
            var _self = this;
            var products = _self.getProductsValid();
            var _ul = $('.message-error ul');
            _ul.find('li').remove();            
            $('.message-error').slideUp('show');            
            storage.post(
                'quickorder/index/SendToCart',
                JSON.stringify({products: products}),
                true
            ).done(
                function (response) {                    
                    if (response.message.length > 0) {
                        _ul.find('li').remove();
                        $.each(response.message, function (i, v) {
                            _ul.append('<li>' + v + '</li>');
                        });
                        $('.message-error').slideDown('show');
                    } else {
                        $('.message-error').slideUp('show');
                        _ul.find('li').remove();
                        setTimeout(function () {
                            $('.message-error').slideUp('show');
                        }, 5000)
                        var sections = ['cart'];
                        customerData.reload(sections, true);
                        window.location.href = $('.js-go-pay').data('cart');
                    }                    
                }
            );
        },
        sendToPay: function () {
            var _self = this;
            $('body').trigger('processStart');
            var products = _self.getProductsValid();
            storage.post(
                'quickorder/index/SendToCart',
                JSON.stringify({products: products}),
                true
            ).done(
                function (response) {
                    var sections = ['cart'];
                    customerData.reload(sections, true);
                    $('body').trigger('processStop');
                }
            );
        },
        listOptions: function () {
            var search = $('#find-product input[name="search"]').val();
            if (search.length > 3) {

                $('body').trigger('processStart');
                storage.post(
                    'quickorder/index/SearchOption',
                    JSON.stringify({
                        search: search,
                        form_key: $('#find-product input[name="form_key"]').val(),
                    }),
                    true
                ).done(
                    function (response) {
                        $("#products").find('option').remove();
                        $.each(response, function (i, v) {
                            if (v.sku.includes(v.d_search)) {
                                $("#sku").val(v.sku);
                                $('#products').append('<option value="'+v.sku+'">'+v.name+'</option>');
                            }else if (v.ref.includes(v.d_search)) {
                                $("#sku").val(v.sku);
                                $('#products').append('<option value="'+v.ref+'">'+v.name+'</option>');
                            }else {
                              $("#sku").val(v.sku);
                              $('#products').append('<option value="'+v.name+' '+v.ref+'">'+v.name+'</option>');
                            }
                        });

                        $('body').trigger('processStop');
                    }
                );
            }
        },
        findProduct: function (e) {
            var self = this;
            $.ajax({
                url: $(e).data('action'),
                type: "POST",
                dataType: "json",
                data: $('#find-product').serialize(),
                cache: false,
            })
                .done(function (json) {
                    if (json.status == 200) {
                        $.each(json.message, function (key, p) {
                            //if (self.exist(p.sku) == 0) {
                                listProducts.push(new self.items(p.id, p.sku, p.ref, p.image, p.name, p.priceFormat, p.totalFormat, p.price, p.qty, p.status, p.type, p.url, p.attributes, p.formatInventory))
                                listProducts.valueHasMutated();
                                if(p.type === 'configurable'){
                                    setTimeout(function(){
                                        var local = parseInt(localStorage.getItem('index')) - 1;
                                        $("#quickview-"+p.id+"[data-index='"+ local +"']").trigger('click');
                                    }, 1000)
                                }
                            //}
                        });
                        fullScreenLoader.stopLoader();
                    }
                    // self.showFormUpload();
                    $('#find-product input[name="search"]').val('')
                })
                .fail(function () {
                    alert("error");
                });
        },
        openModal: function (e) {
            $("body").loader("show");
            var _self = this;
            var products = _self.getProductsValid(true);
            storage.post(
                'quickorder/index/SendToCart',
                JSON.stringify({products: products}),
                true
            ).done(function (json) {
                $('body').trigger('processStop');
                var sections = ['cart'];
                customerData.reload(sections, true);
                var numberItems = parseInt($('.minicart-wrapper  .counter-number').text());
                if (numberItems >= 1 || parseInt(json.items) > 0) {
                    $('.js-generate-quote').trigger('click');
                } else {
                    alert("Debes agregar al menos un producto al carrito");
                }
                $("body").loader("hide");
            });


        },
        editAttributes: function (data) {
            var self = this;
            globalSelf.openModalAttributes(data.id());
        },
        openModalAttributes: function (id) {
            var self = this;
            var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: "Escoge tus atributos",
                leftMargin:60,
                buttons: [{
                    text: $.mage.__('Continue'),
                    class: 'mymodal1',
                    click: function () {
                        var array = $("#form-attributes").serializeArray();
                        var res = globalSelf.validateAttributes(array);

                        if(res == 0){
                            $.each(listProducts(), function (index, value) {
                                if (value.id() == id && value.type() == 'configurable') {
                                    value.updateAttr(array);
                                }
                            });
                            this.closeModal();
                        }

                    }
                }]
            };
            var popup = modal(options, $('#dialog-form'));
            $("#dialog-form").html(self.modalTemplate(id)).modal("openModal");


        },
        modalTemplate: function(id){
            var self = this;
            var atrr = self.printAttributes(id);
            var html = `<form id="form-attributes" class="form-attributes">
                            <div class="row">
                            ${atrr}
                            </div>
                        </form>`;
            return html;
        },
        printAttributes: function(id){
            var divs = '';
            $.each(listProducts(), function (index, value) {
                if (value.id() == id && value.type() == 'configurable') {
                    $.each(value.attributes(), function (i, v) {
                        var label = "<label for='"+v.code+"'>"+v.label+"*</label>";
                        var options = "<option value=''>Seleccione "+v.label+"</option>";
                        $.each(v.options, function (ind, option) {
                            var selected = '';
                            if(value.attributesSelected() !== undefined){
                                $.each(value.attributesSelected(), function (index, opt) {
                                    if(opt.value === option.value){
                                        selected = 'selected';
                                    }
                                });
                            }
                            options += "<option value='"+option.value+"' "+selected+">"+option.label+"</option>";
                        });
                        var select = "<select required name='"+v.id+"' class='form-control' id='"+v.code+"' >"+options+"</select>";
                        var error = "<span style='color:#e10413' class='attribute-error'></span>"
                        divs += "<div class='col-md-4'>"+label+select+error+"</div>"
                    });
                }
            });
            return divs;
        },
        updateQuantityMethod: function(data, action){
          var qty = parseInt(data.qty()) + parseInt(action);
          if (qty < 0) {
            qty = 1;
          }
          data.qty(qty)
          var total = data.price() * qty;
          data.totalFormat(priceUtils.formatPrice(total, window.orderPriceFormat));
        },
        attrFormated: function(data){
          var html = '';
          $.each(data.attributesSelected(), function (index, value) {
            $.each(data.attributes(), function (i, v) {
              if(value.name === "super_attribute["+v.id+"]"){
                var val = v.options.find(opt => opt.value === value.value);
                html += "<p><strong>"+v.label+":</strong> <span>"+val.label+"</span></p>";
              }
            });
          });
          return html;
        }
        /*validateAttributes: function(array){
            var errors = 0;
            $.each(array, function (i, v) {
                if(v.value == ''){
                    $("[name='"+v.name+"']").next('span.attribute-error').text("Este campo es obligatorio");
                    errors ++;
                }
            });
            return errors;
        }*/
    });

});
