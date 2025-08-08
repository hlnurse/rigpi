(function ($) {

    //整體使用
    $.diagonal = {};

    // 呼叫版本
    $.diagonal.version = function (){
        return "1.0.0";
    }

    // 內部方法
    $.diagonal.api = new function (){

        var api = this;
        
        /**
         * 轉換角度
         * https://dotblogs.com.tw/junegoat/2010/09/13/17699
         * @param   來源座標如 {x:10, y:20}
         * @param   目標座標如 {x:10, y:20}
         * 
         */
        this.convert_position_angel = function (soucePoint,targetPoint) {
            var res = (Math.atan2(targetPoint.y - soucePoint.y, targetPoint.x -soucePoint.x)) / Math.PI * 180.0;
            return ( res >= 0 && res <=180) ? res += 90 : ((res < 0 && res >= -90) ? res += 90 : res += 450);
        }

        /**
         * 建立, 使用 $.diagonal.api.create.call(this)
         * @param   param.deg    角度
         * @param   param.color  顏色
         * @param   param.isrwd  是否自動適應寬度
         */
        this.create = function (param){

            if (param.isrwd == true) {

                api.render.call(this, param)

                // 定時偵測，若寬高改動，就重新渲染
                api.timer.call(this, param, function (param){

                    var deg = $.diagonal.deg(this);
                    // console.log('更動了, deg: ' + deg)

                    api.render.call(this, {
                        deg: deg,
                        color: param.color
                    })

                });

            }
            else {
                api.render.call(this, param)
            }

            
        }

        // 定時器
        this.timer = function (param, success){
            var _this = this;
            var next_w = $(this).width();
            var next_h = $(this).height();

            setInterval(function (){
                var current_w = $(_this).width();
                var current_h = $(_this).height();
                
                // 若寬高改變
                if (current_w != next_w || current_h != next_h) {
                    next_w = current_w;
                    next_h = current_h;
                    success.call(_this, param);
                }

            }, 20)
        }

        /**
         * 渲染樣式, 使用 $.diagonal.api.render.call(this)
         * param.deg
         * param.color
         */
        this.render = function (param){

            var background = " linear-gradient(" + param.deg + "deg, transparent 49.5%, "+ param.color + " 49.5%, "+ param.color + " 50.5%, transparent 50.5%) ";

            $(this).css({
                boxSizing: "border-box",
                background: background
            })
        }

    }

    $.diagonal.deg = function (selector){

        var selector_width = $(selector).width();
        var selector_height = $(selector).height();
        var wrap_offset = $(selector).offset();

        var sP = {
            x: wrap_offset.left, 
            y: wrap_offset.top + selector_height
        }

        var tP = {
            x: wrap_offset.left + selector_width,
            y: wrap_offset.top
        }

        var CPA = $.diagonal.api.convert_position_angel(sP, tP);
        var deg = 90 - CPA;

        return deg;
    };
    


    /**
     * @param   int    param.deg
     * @param   string param.color
     * @param   bool   param.isrwd
     */
    $.fn.diagonal = function (param){
        $.diagonal.api.create.call(this, param);
    }


}( jQuery ));