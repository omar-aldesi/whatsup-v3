"use strict"

// Get Device width
let deviceWidth = window.innerWidth;

// 07. Header 3
let header = document.querySelector(".header");
if (header) {
    window.onscroll = function () {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            header.classList.add("sticky");
        } else {
            header.classList.remove("sticky");
        }
    };
}



// Active menu dynamiclly
const sections = document.querySelectorAll("section[id]");
window.addEventListener("scroll", navHighlighter);

function navHighlighter() {
    let scrollY = window.pageYOffset;

    sections.forEach(current => {
        const sectionHeight = current.offsetHeight;
        const sectionTop = current.offsetTop - 80;
        const sectionId = current.getAttribute("id");

        if (
            scrollY > sectionTop &&
            scrollY <= sectionTop + sectionHeight
        ) {
            document.querySelector(".nav-menu-item a[href*=" + sectionId + "]").classList.add("menu-item-active");
        } else {
            document.querySelector(".nav-menu-item a[href*=" + sectionId + "]").classList.remove("menu-item-active");
        }
    });
}

// mobile menu side bar
const menuBtn = document.querySelector(".bars")
const mainNav = document.querySelector(".nav-menu-wrapper");

if (mainNav != null && menuBtn != null) {
    const closeSidebar = mainNav.querySelector(".close-sidebar");
    const overlay = document.querySelector(".main-nav-overlay");

    menuBtn.addEventListener("click", () => {
      mainNav.classList.toggle("show-menu-wrap");
      overlay.classList.add("d-block");
        if (mainNav.classList.contains("show-menu-wrap")) {
          
        overlay.addEventListener("click", () => {
          mainNav.classList.remove("show-menu-wrap");
          overlay.classList.remove("d-block");
        });
          
        closeSidebar.addEventListener("click", () => {
          mainNav.classList.remove("show-menu-wrap");
          overlay.classList.remove("d-block");    
        });
            
        } else {
            overlay.classList.remove("d-block");
      }

    })
}


// Review Slider

const reviewSlider = document.querySelector(".review-slider");
if (reviewSlider !== null) {
  new Swiper(reviewSlider, {
    slidesPerView: 1,
    spaceBetween: 50,
    loop: true,
    navigation: {
      nextEl: ".slide-next",
      prevEl: ".slide-prev",
    },
  });
}