/**
 * Footer JavaScript - Site-wide footer functionality
 * Handles header/navbar animations and footer effects
 */

$(document).ready(function() {
    
    // Shrinking header on scroll
    $(window).scroll(function() {
        if ($(this).scrollTop() > 50) {
            $('#mainHeader').addClass('shrink');
            $('#mainNavbar').addClass('shrink');
        } else {
            $('#mainHeader').removeClass('shrink');
            $('#mainNavbar').removeClass('shrink');
        }
    });
    
    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if(target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 150
            }, 800, 'easeInOutQuad');
        }
    });
    
    // Animate footer stats on scroll into view
    var statsAnimated = false;
    $(window).scroll(function() {
        if (!statsAnimated) {
            var footerOffset = $('.footer-stats').offset();
            if (footerOffset && $(window).scrollTop() + $(window).height() > footerOffset.top) {
                $('.stat-number').each(function() {
                    $(this).prop('Counter', 0).animate({
                        Counter: $(this).text().replace(/[^0-9.]/g, '')
                    }, {
                        duration: 2000,
                        easing: 'swing',
                        step: function(now) {
                            var text = $(this).parent().find('.stat-number').text();
                            var suffix = text.match(/[^0-9.]+$/);
                            $(this).text(Math.ceil(now) + (suffix ? suffix[0] : ''));
                        }
                    });
                });
                statsAnimated = true;
            }
        }
    });
    
    // Highlight active navigation item
    var currentPath = window.location.pathname.split('/').pop();
    $('.navbar-nav .nav-link').each(function() {
        var linkPath = $(this).attr('href');
        if (linkPath === currentPath || (currentPath === '' && linkPath === 'index.php')) {
            $(this).addClass('active');
        }
    });
    
    // Add hover effect to social links with jQuery UI
    $('.social-links a').hover(
        function() {
            $(this).effect('bounce', { times: 1, distance: 5 }, 300);
        }
    );
    
    // Fade in footer sections on load
    $('.footer-section').hide().fadeIn(1000);
    
    // Close mobile navbar on link click
    $('.navbar-nav .nav-link').on('click', function() {
        if ($(window).width() < 992) {
            $('.navbar-collapse').collapse('hide');
        }
    });
    
});
