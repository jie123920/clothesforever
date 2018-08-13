/**
 * Created by zhanglei on 2017/4/12.
 */
$(function() {

//        导航列表
    var Accordion = function(el, multiple) {
        this.el = el || {};
        this.multiple = multiple || false;

        // Variables privadas
        var links = this.el.find('.link');
        // Evento
        links.on('click', {el: this.el, multiple: this.multiple}, this.dropdown)
    }

    Accordion.prototype.dropdown = function(e) {
        var $el = e.data.el;
        $this = $(this),
            $next = $this.next();

        $next.slideToggle();
        $this.parent().toggleClass('open');

        if (!e.data.multiple) {
            $el.find('.submenu').not($next).slideUp().parent().removeClass('open');
        };
    }

    var accordion = new Accordion($('#accordion'), false);
//        添加购物车按钮
    $(".cf-commodities").delegate(".cf-commodities-pic","mousemove",function(){
        $(this).find(".add_cart").show();
    });
    $(".cf-commodities").delegate(".cf-commodities-pic","mouseout",function(){
        $(this).find(".add_cart").hide();
    });
    $(".cf-commodities").delegate(".add_cart","click",function(){
        $(this).parent().find(".car_size").show();
    });
    $(".cf-commodities").delegate(".cf_cancel","click",function(){
        $(this).parent().hide();
    });

    $(".car_size").delegate("span","click",function(){
        $(this).toggleClass('span_active').siblings().removeClass('span_active');
    });
    $(".color_sum").delegate("span","click",function(){
        $(this).toggleClass('size_span').siblings().removeClass('size_span');
    });
// 收藏点击

    $(".cf-commodities").delegate(".cf-follow","click",function(){
        if($(this).find(".fa-heart").css("display")=="none"){
            $(this).find(".fa-heart").show();
            $(this).find(".fa-heart-o").hide();
        }else{
            $(this).find(".fa-heart").hide();
            $(this).find(".fa-heart-o").show();
        }
        var goods_id = $(this).find('input').val();
        $.get('/goods/favorite?goods_id='+goods_id,{}, function(data, textStatus, xhr) {
            if (data.status == 0) {
                // if(tag == 1){
                //     $(this).find(".fa-heart").show();
                //     $(this).find(".fa-heart-o").hide();
                // }else{
                //     $(this).find(".fa-heart").hide();
                //     $(this).find(".fa-heart-o").show();
                // }
            }else{
                if(data.status == -1){
                    location.href = "/login?referer="+document.location.href;
                }else{
                    return  $(".wenzi").text(data.msg).parent().show();
                }
            }
        },"json").complete(function(){

        });

    });
    // 设计师
    $(".designer-caret-down").click(function(){
        if($(".designer-list").css("display")=="none"){
            $(".designer-list").show();
        }else{
            $(".designer-list").hide();
        }
    })
    // 颜色
    $(".color-caret-down").click(function(){
        if($(".color-list").css("display")=="none"){
            $(".color-list").show();
        }else{
            $(".color-list").hide();
        }
    })
    // 尺寸
    $(".size-caret-down").click(function(){
        if($(".size-list").css("display")=="none"){
            $(".size-list").show();
        }else{
            $(".size-list").hide();
        }
    })

    // $(".topicList h3").click(function(){
    //     var UL = $(this).next("ul");
    //     if(UL.css("display")=="none"){
    //         UL.css("display","block");
    //     }
    //     else{
    //         UL.css("display","none");
    //     }
    // });
    // snapchat标志
    $('.snap').mouseover(function(){
        $('.snapcode').show();
    })
    $('.snap').mouseout(function(){
        $('.snapcode').hide();
    })
    // 判断详情页面商品为空箭头消失
    // if($('.bx-viewport .slide').length == 0){
    //     $(".related-commodity").css("display","none");
    // }else{
    //     $(".related-commodity").show();
    // }
})
