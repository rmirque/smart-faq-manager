/**
 * Public Scripts
 *
 * @package Smart_FAQ_Manager
 */

(function() {
    'use strict';
    
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    function init() {
        initAccordions();
        initSmoothScroll();
        initSearch();
    }
    
    /**
     * Initialize accordion functionality
     */
    function initAccordions() {
        var accordions = document.querySelectorAll('.smart-faq-accordion');
        
        accordions.forEach(function(accordion) {
            var items = accordion.querySelectorAll('.smart-faq-item');
            
            items.forEach(function(item) {
                var question = item.querySelector('.smart-faq-question');
                
                if (!question) return;
                
                // Add ARIA attributes
                question.setAttribute('role', 'button');
                question.setAttribute('aria-expanded', 'false');
                question.setAttribute('tabindex', '0');
                // Ensure question has an id for aria-labelledby
                if (!question.id) {
                    question.id = 'faq-question-' + Math.random().toString(36).substr(2, 9);
                }
                
                var answer = item.querySelector('.smart-faq-answer');
                if (answer) {
                    var answerId = 'faq-answer-' + Math.random().toString(36).substr(2, 9);
                    answer.id = answerId;
                    question.setAttribute('aria-controls', answerId);
                    answer.setAttribute('role', 'region');
                    answer.setAttribute('aria-labelledby', question.id);
                }
                
                // Click event
                question.addEventListener('click', function() {
                    toggleAccordion(item, question);
                });
                
                // Keyboard support
                question.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        toggleAccordion(item, question);
                    }
                });
            });
        });
    }
    
    /**
     * Toggle accordion item
     */
    function toggleAccordion(item, question) {
        var isActive = item.classList.contains('active');
        
        if (isActive) {
            item.classList.remove('active');
            question.setAttribute('aria-expanded', 'false');
        } else {
            item.classList.add('active');
            question.setAttribute('aria-expanded', 'true');
            
            // Track click interaction (only on expand, not collapse)
            trackInteraction(item, 'click');
        }
    }
    
    /**
     * Track FAQ interaction via AJAX
     */
    function trackInteraction(item, interactionType) {
        if (typeof smartFaqPublic === 'undefined') {
            return;
        }
        
        var faqId = item.getAttribute('data-faq-id');
        if (!faqId) {
            return;
        }
        
        // Send AJAX request
        var xhr = new XMLHttpRequest();
        xhr.open('POST', smartFaqPublic.ajaxUrl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(
            'action=smart_faq_track_interaction' +
            '&faq_id=' + faqId +
            '&page_id=' + smartFaqPublic.pageId +
            '&interaction_type=' + interactionType +
            '&nonce=' + smartFaqPublic.nonce
        );
    }
    
    /**
     * Initialize smooth scrolling to FAQs
     */
    function initSmoothScroll() {
        var hash = window.location.hash;
        
        if (hash && hash.indexOf('#faq-') === 0) {
            var target = document.querySelector(hash);
            
            if (target && target.classList.contains('smart-faq-item')) {
                setTimeout(function() {
                    // Calculate offset (100px from top)
                    var targetPosition = target.getBoundingClientRect().top + window.pageYOffset - 100;
                    
                    // Smooth scroll
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // If it's an accordion, open it
                    if (target.closest('.smart-faq-accordion')) {
                        var question = target.querySelector('.smart-faq-question');
                        if (question && !target.classList.contains('active')) {
                            toggleAccordion(target, question);
                        }
                    }
                    
                    // Add highlight effect
                    target.classList.add('faq-highlight');
                    setTimeout(function() {
                        target.classList.remove('faq-highlight');
                    }, 2000);
                }, 300);
            }
        }
    }
    
    /**
     * Initialize search/filter functionality
     */
    function initSearch() {
        var searchInputs = document.querySelectorAll('.smart-faq-search-input');
        
        searchInputs.forEach(function(searchInput) {
            var searchContainer = searchInput.closest('.smart-faq-search');
            var clearButton = searchContainer ? searchContainer.querySelector('.smart-faq-search-clear') : null;
            var countDisplay = searchContainer ? searchContainer.querySelector('.smart-faq-search-count') : null;
            var widgetContainer = searchContainer ? searchContainer.nextElementSibling : null;
            
            if (!widgetContainer) return;
            
            // Search on input
            searchInput.addEventListener('input', function() {
                var searchTerm = this.value.toLowerCase().trim();
                var items = widgetContainer.querySelectorAll('.smart-faq-item');
                var visibleCount = 0;
                
                items.forEach(function(item) {
                    var question = item.querySelector('.smart-faq-question');
                    var answer = item.querySelector('.smart-faq-answer');
                    
                    var questionText = question ? question.textContent.toLowerCase() : '';
                    var answerText = answer ? answer.textContent.toLowerCase() : '';
                    
                    if (searchTerm === '' || questionText.includes(searchTerm) || answerText.includes(searchTerm)) {
                        item.style.display = '';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Show/hide clear button
                if (clearButton) {
                    clearButton.style.display = searchTerm ? 'block' : 'none';
                }
                
                // Update count
                if (countDisplay) {
                    if (searchTerm) {
                        countDisplay.textContent = visibleCount + ' of ' + items.length + ' FAQs';
                        countDisplay.style.display = 'block';
                    } else {
                        countDisplay.style.display = 'none';
                    }
                }
            });
            
            // Clear search
            if (clearButton) {
                clearButton.addEventListener('click', function() {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input'));
                    searchInput.focus();
                });
            }
        });
    }
    
    /**
     * Lazy load images in FAQ answers
     */
    function lazyLoadImages() {
        if ('IntersectionObserver' in window) {
            var images = document.querySelectorAll('.smart-faq-answer img[data-src]');
            
            var imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            images.forEach(function(img) {
                imageObserver.observe(img);
            });
        }
    }
    
    // Initialize lazy loading if needed
    lazyLoadImages();
    
})();
