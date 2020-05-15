$(document).ready(function($) {
    $("#email").keyup(function() {
        var data = {
            action: "email_validation",
            email: $("#email").val()
        };

        $.post( ajax.url, data, function(response) {
            if ("true" === response) {
                $(".message").text("");
            } else {
                $(".message").text(response);
            }
        });
    });
    $("#username").keyup(function() {
        var data = {
            action: "username_validation",
            username: $("#username").val()
        };

        $.post( ajax.url, data, function(response) {
            if ("true" === response) {
                $(".message").text("");
            } else {
                $(".message").text(response);
            }
        });
    });
    $("#reg-next").click(function() {
        var data = {
            action: "email_validation",
            email: $("#email").val()
        };

        $.post( ajax.url, data, function(response) {
            if ("true" === response) {
                $(".message").text("");
                $("#register-block-email").hide();
                $("#register-block-user").show();
            } else {
                $(".message").text(response);
            }
        });
    });
    $("#reg-back").click(function() {
        $(".message").text("");
        $("#register-block-user").hide();
        $("#register-block-email").show();
    });
    $("#reg-register").click(function() {
        var data = {
            action: "custom_registration",
            email: $("#email").val(),
            username: $("#username").val(),
            password: $("#password").val()
        };

        $.post( ajax.url, data, function(response) {
            if ("true" === response) {
                $(".message").text("");
                $("#register-block-user").hide();
                $("#register-block-end").show();
            } else {
                $(".message").text(response);
            }
        });
    });
});