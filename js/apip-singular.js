//empty
(
    function( $ ) {
        $( document ).ready( function() {
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                var target = this.hash;
                $jmp = document.getElementsByName(target.substring(1))[0];
                var theTop = $jmp.offsetTop ? $jmp.offsetTop : $jmp.offsetParent.offsetTop; 
                $('html,body').animate({scrollTop: theTop - 10},1000);
            });
        });
    })
( jQuery );