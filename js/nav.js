document.addEventListener("DOMContentLoaded", function(event) {
   
    const showNavbar = (toggleId, navId, bodyId, headerId) =>{
    const toggle = document.getElementById(toggleId),
    nav = document.getElementById(navId),
    bodypd = document.getElementById(bodyId),
    headerpd = document.getElementById(headerId)
    
    // Validate that all variables exist
    if(toggle && nav && bodypd && headerpd){
    toggle.addEventListener('click', ()=>{
    // show navbar
    nav.classList.toggle('show')
    // change icon
    toggle.classList.toggle('bx-x')
    // add padding to body
    bodypd.classList.toggle('body-pd')
    // add padding to header
    headerpd.classList.toggle('body-pd')
    })
    }
    }
    
    showNavbar('header-toggle','nav-bar','body-pd','header')
    
    /*===== LINK ACTIVE =====*/
    const linkColor = document.querySelectorAll('.nav_link')
    
    function colorLink(){
    if(linkColor){
    linkColor.forEach(l=> l.classList.remove('active'))
    this.classList.add('active')
    }
    }
    linkColor.forEach(l=> l.addEventListener('click', colorLink))
    
     // Your code to run since DOM is loaded and ready
    });

    $(document).ready(function(){
        if(window.innerWidth < 767){
          $('#body-pd').removeClass('body-pd');
          $('.l-navbar').removeClass('show');
          $('.bx-menu').removeClass('bx-x');
          $('.header').removeClass('body-pd');
        }
        else{
            $('#body-pd').addClass('body-pd');
          $('.l-navbar').addClass('show');
          $('.bx-menu').addClass('bx-x');
          $('.header').addClass('body-pd');
        }
      });
      
      $(window).resize(function(){
        if(window.innerWidth < 1340){
          $('.btn-group').addClass('backup-btn-group').removeClass('btn-group');
        }else{
          $('.backup-btn-group').addClass('btn-group').removeClass('backup-btn-group');
        }
      });