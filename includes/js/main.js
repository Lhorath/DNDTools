/*
    Dack's DND Tools - includes/js/main.js
    ======================================
    This file contains the global JavaScript that runs on every page.
    It is responsible for handling the main navigation menu and the homepage hero slider.
*/

// --- SECTION 1: DOMContentLoaded EVENT ---
// This is the main execution block. It runs after the entire page has loaded,
// ensuring that all HTML elements are available before the scripts try to interact with them.
document.addEventListener('DOMContentLoaded', () => {

    /**
     * Sets up the event listeners for the "Tools" dropdown menu in the desktop navigation.
     * It handles opening and closing the dropdown on click.
     */
    const setupDesktopDropdown = () => {
        const dropdownButton = document.querySelector('.tools-dropdown-btn');
        const dropdownContent = document.getElementById('tools-dropdown-content');
        if (!dropdownButton || !dropdownContent) return;

        // Toggles the dropdown's visibility when the button is clicked.
        dropdownButton.addEventListener('click', (event) => {
            event.stopPropagation(); // Prevents the window click listener from immediately closing it.
            dropdownContent.classList.toggle('hidden');
        });

        // Closes the dropdown if the user clicks anywhere else on the page.
        window.addEventListener('click', () => {
            if (!dropdownContent.classList.contains('hidden')) {
                dropdownContent.classList.add('hidden');
            }
        });
    };

    /**
     * Sets up the event listeners for the mobile navigation menu ("hamburger" button).
     * It handles toggling the menu's visibility and switching the button's icon.
     */
    const setupMobileMenu = () => {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const openIcon = document.getElementById('menu-open-icon');
        const closeIcon = document.getElementById('menu-close-icon');
        if (!mobileMenuButton || !mobileMenu || !openIcon || !closeIcon) return;

        // When the hamburger button is clicked, toggle the menu's visibility and swap the icons.
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            openIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        });
    };

    /**
     * Sets up the hero slider on the homepage.
     */
    const setupHeroSlider = () => {
        const sliderContainer = document.getElementById('hero-slider');
        if (!sliderContainer) return; // Exit if the slider doesn't exist on the current page.

        const slides = sliderContainer.querySelectorAll('.hero-slide');
        const prevButton = document.getElementById('slider-prev');
        const nextButton = document.getElementById('slider-next');
        const dotsContainer = document.getElementById('slider-dots');
        if (slides.length === 0) return;

        let currentSlide = 0;
        let slideInterval;

        // Create dot indicators for each slide.
        slides.forEach((_, i) => {
            const dot = document.createElement('button');
            dot.classList.add('slider-dot', 'w-3', 'h-3', 'bg-white/50', 'rounded-full', 'transition-colors');
            dot.addEventListener('click', () => {
                showSlide(i);
                resetInterval();
            });
            dotsContainer.appendChild(dot);
        });
        const dots = dotsContainer.querySelectorAll('.slider-dot');

        // Function to display a specific slide and update the active dot.
        const showSlide = (n) => {
            slides.forEach((slide, index) => {
                slide.classList.toggle('opacity-100', index === n);
                slide.classList.toggle('opacity-0', index !== n);
            });
            dots.forEach((dot, index) => {
                dot.classList.toggle('bg-white', index === n);
                dot.classList.toggle('bg-white/50', index !== n);
            });
            currentSlide = n;
        };

        // Functions to navigate to the next or previous slide.
        const nextSlide = () => showSlide((currentSlide + 1) % slides.length);
        const prevSlide = () => showSlide((currentSlide - 1 + slides.length) % slides.length);
        
        // Auto-play functionality.
        const startInterval = () => {
            slideInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds.
        };
        const resetInterval = () => {
            clearInterval(slideInterval);
            startInterval();
        };

        // Event listeners for arrow navigation buttons.
        nextButton.addEventListener('click', () => {
            nextSlide();
            resetInterval();
        });
        prevButton.addEventListener('click', () => {
            prevSlide();
            resetInterval();
        });

        // Initialize the slider to the first slide and start auto-play.
        showSlide(0);
        startInterval();
    };


    // --- SECTION 2: INITIALIZATION CALLS ---
    // =======================================
    // Call all the setup functions to initialize the site's interactive elements.
    setupDesktopDropdown();
    setupMobileMenu();
    setupHeroSlider();
});
