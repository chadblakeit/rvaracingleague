var submenuTimeout = setTimeout(function(){},0);
var timeoutCleared = 1;

$(document).ready(function(){
    $(".header-user .user, .header-user .avatar").on("click", function(e){
        if ($(".user-dropdown").is(":visible")) {
            $(".user-dropdown").hide();
        } else {
            $(".user-dropdown").show();
        }
    });
    $(".mobile-menu .fa-bars").on("click", function(){
        if ($(".mobile-menu-dropdown").is(":visible")) {
            $(".mobile-menu-dropdown").hide();
        } else {
            $(".mobile-menu-dropdown").show();
        }
    });
    $(".header-user .user-dropdown").on("click", function(e) {
        /*console.log(e);
        e.preventDefault();
        e.stopPropagation();*/
    });
    /*$(".container").on("click", function(){
        if ($(".header-user .user-dropdown").is(":visible")) {
            $(".header-user .user").trigger("click");
        }
    });*/

    if ($(".invite-league-container").length > 0) {
        $(".invited-league").each(function() {
            var lc = this;
            console.log(lc);
            $(this).find(".accept-league").on("click", function (e) {
                var data = {
                    inviteleague_email: $(lc).find("input[name=inviteleague_email]").val(),
                    inviteleague: $(lc).find("input[name=inviteleague]").val(),
                    leagueindex: $(lc).attr('id')
                };
                $.ajax({
                    url: "/league/acceptinvite",
                    method: "post",
                    dataType: "json",
                    data: data,
                    beforeSend: function (xhr) {
                        $(lc).find('.accept-league').off('click');
                        $(lc).find('.accept-league').html('<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>');
                        $(lc).find('.accept-league').addClass("submitting");
                    },
                    success: function (xhr) {
                        console.log(xhr);
                        if (xhr.invitesuccess == true) {
                            $("#" + xhr.leagueindex).find('.accept-league').html('Success!');
                            $("#" + xhr.leagueindex).find('.accept-league').css('background', 'green');
                            $("#" + xhr.leagueindex).find('.accept-league').removeClass("submitting");
                            setTimeout(function () {
                                window.location.href = '/home';
                            }, 1000);
                        } else {
                            $("#" + xhr.leagueindex).find('.accept-league').html('Error');
                            $("#" + xhr.leagueindex).find('.accept-league').css('background', 'red');
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr);
                    }
                });
            });
        });
    }




    $(".league-dropdown-menu").hover(function(){
        if (timeoutCleared == 0) { clearTimeout(submenuTimeout); timeoutCleared=1; }
        if (!$(this).next(".submenu").hasClass("active")) {
            $(this).next(".submenu").addClass("active");
        }
    }).mouseout(function(){
        hideSubmenu();
    });

    $(".league-dropdown-menu").click(function(){
        if (timeoutCleared == 0) { clearTimeout(submenuTimeout); timeoutCleared=1; }
        if (!$(this).next(".submenu").hasClass("active")) {
            $(this).next(".submenu").addClass("active");
        } else {
            $(this).next(".submenu").removeClass("active");
        }
    });

    $(".submenu").hover(function(e){
        if (timeoutCleared == 0) { clearTimeout(submenuTimeout); timeoutCleared=1; }
        if (!$(this).hasClass("active")) {
            $(this).addClass("active");
        }
    }).mouseout(function(event){
        var e = event.toElement || event.relatedTarget;
        while (e && e.parentNode && e.parentNode != window) {
            if (e.parentNode == this || e == this) {
                if (e.preventDefault) e.preventDefault;
                return false;
            }
            e = e.parentNode;
        }
        hideSubmenu();
    });


});

function hideSubmenu() {
    timeoutCleared = 0;
    submenuTimeout = setTimeout(function(){
        $(".submenu").removeClass("active");
        timeoutCleared = 1;
    },100);
}