

if (document.documentElement.clientWidth < 777) {

$("#center_01").slick({
  centerMode: true,
  infinite: true,
  centerPadding: "10px",
  slidesToShow: 6,
  speed: 1600,
  autoplay: true,
  autoplaySpeed: 2000,
  responsive: [
    {
      breakpoint: 768,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: "10px",
        slidesToShow: 6 // 12
      }
    },
    {
      breakpoint: 480,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: "10px",
        slidesToShow: 6
      }
    }
  ]
});

$("#center_02").slick({
  centerMode: true,
  infinite: true,
  centerPadding: "10px",
  slidesToShow: 6,
  speed: 1600,
  autoplay: true,
  autoplaySpeed: 2300,
  responsive: [
    {
      breakpoint: 768,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: "10px",
        slidesToShow: 6
      }
    },
    {
      breakpoint: 480,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: "10px",
        slidesToShow: 6
      }
    }
  ]
});
} else {
  $("#center_01").slick({
  centerMode: true,
  infinite: true,
  centerPadding: "10px",
  slidesToShow: 12,
  speed: 1600,
  autoplay: true,
  autoplaySpeed: 2000,
  responsive: [
    {
      breakpoint: 768,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: "10px",
        slidesToShow: 12 // 12
      }
    },
    {
      breakpoint: 480,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: "10px",
        slidesToShow: 12
      }
    }
  ]
});

$("#center_02").slick({
  centerMode: true,
  infinite: true,
  centerPadding: "10px",
  slidesToShow: 12,
  speed: 1600,
  autoplay: true,
  autoplaySpeed: 2300,
  responsive: [
    {
      breakpoint: 768,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: "10px",
        slidesToShow: 12
      }
    },
    {
      breakpoint: 480,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: "10px",
        slidesToShow: 12
      }
    }
  ]
});
}
