$(document).ready(function(){
    $(".header-user .user").on("click", function(e){
        if ($(".user-dropdown").is(":visible")) {
            $(".user-dropdown").hide();
        } else {
            $(".user-dropdown").show();
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

});