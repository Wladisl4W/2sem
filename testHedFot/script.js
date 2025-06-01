$(document).ready(function() {
    $(".dropdown").mouseenter(function() {
        $(".dropdown-wrapper").addClass("showDropdown");
        $(".dropdown-wrapper").removeClass("hideDropdown");
    });
    $(".dropdown").mouseleave(function() {
        $(".dropdown-wrapper").addClass("hideDropdown");
        $(".dropdown-wrapper").removeClass("showDropdown");
    });

    $(".menuMobileItem").hide();
    $("#menuMobileButton").click(function() {
        $(".menuMobileItem").toggle();
    });

    $(".gallery").slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [ {
            breakpoint: 901,
            settings: {
                slidesToShow: 1,
                }
            },
        ]
    });

    window.setInterval(reklama, 5000);
    function reklama() { 
        let myToastEl = document.getElementById("liveToast"); 
        let myToast = bootstrap.Toast.getOrCreateInstance(myToastEl); 
        if (myToast["_element"].classList[2] !== "show") { 
            myToast.show(); 
        }
    }
});