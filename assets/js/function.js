$(document).ready(function () {
    setTimeout(function () {
        $(".loader-wrapper").fadeOut("slow", function () {
            $(".box").removeClass("hidden");
            $(".box").css("opacity", 1);
        });
    }, 2000);
  });
  
