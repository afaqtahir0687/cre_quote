/**
 * Crelogics ERP - SaaS Public Landing Page Scripts
 * Handles:
 * 1. Sticky Navigation
 * 2. Automated Infinite Module Carousel & Live Content Switcher
 * 3. Automated 5-Step Connected Workflow Slider & Progress Fill
 * 4. FAQ Accordion
 * 5. Smooth Anchor Scrolling
 * 6. Dynamic Counter Animation
 */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // =========================================================================
  // 1. Sticky Header Scroll Effect
  // =========================================================================
  var header = document.querySelector('.erp-header');
  if (header) {
    var checkScroll = function () {
      if (window.scrollY > 20) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    };
    window.addEventListener('scroll', checkScroll, { passive: true });
    checkScroll();
  }

  // =========================================================================
  // 2. Automated Infinite Module Carousel & Live Content Switcher
  // =========================================================================
  var carouselWrapper = document.getElementById('moduleCarouselWrapper');
  var carouselTrack = document.getElementById('moduleCarouselTrack');
  var moduleTabs = document.querySelectorAll('.erpgo-module-tab');
  var modulePanels = document.querySelectorAll('.erpgo-module-panel');
  var prevBtn = document.getElementById('carouselPrevBtn');
  var nextBtn = document.getElementById('carouselNextBtn');

  if (moduleTabs.length > 0 && modulePanels.length > 0 && carouselWrapper) {
    var activeModuleIndex = 0;
    var moduleTimer = null;
    var isModuleHovered = false;
    var moduleList = Array.from(moduleTabs);

    function activateModule(index, smoothScroll) {
      if (index < 0) index = moduleList.length - 1;
      if (index >= moduleList.length) index = 0;
      activeModuleIndex = index;

      var activeTab = moduleList[activeModuleIndex];
      var targetId = activeTab.getAttribute('data-erpgo-tab');

      // Update Tab classes
      moduleTabs.forEach(function (tab) { tab.classList.remove('active'); });
      activeTab.classList.add('active');

      // Update Panel visibility
      modulePanels.forEach(function (panel) { panel.classList.remove('active'); });
      var targetPanel = document.getElementById('pane-' + targetId);
      if (targetPanel) {
        targetPanel.classList.add('active');
      }

      // Scroll active tab into view inside the horizontal carousel wrapper
      if (smoothScroll !== false && carouselWrapper) {
        var wrapperRect = carouselWrapper.getBoundingClientRect();
        var tabRect = activeTab.getBoundingClientRect();
        var offsetLeft = activeTab.offsetLeft - (wrapperRect.width / 2) + (tabRect.width / 2);
        carouselWrapper.scrollTo({
          left: Math.max(0, offsetLeft),
          behavior: 'smooth'
        });
      }
    }

    function startModuleAutoSlide() {
      stopModuleAutoSlide();
      moduleTimer = setInterval(function () {
        if (!isModuleHovered) {
          activateModule(activeModuleIndex + 1, true);
        }
      }, 4500);
    }

    function stopModuleAutoSlide() {
      if (moduleTimer) {
        clearInterval(moduleTimer);
        moduleTimer = null;
      }
    }

    // Tab Clicks
    moduleTabs.forEach(function (tab, idx) {
      tab.addEventListener('click', function () {
        activateModule(idx, true);
        startModuleAutoSlide();
      });
    });

    // Prev / Next Carousel Buttons
    if (prevBtn) {
      prevBtn.addEventListener('click', function () {
        activateModule(activeModuleIndex - 1, true);
        startModuleAutoSlide();
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function () {
        activateModule(activeModuleIndex + 1, true);
        startModuleAutoSlide();
      });
    }

    // Pause on Hover
    var previewWindow = document.querySelector('.erpgo-preview-window');
    var pauseTargets = [carouselWrapper, previewWindow];

    pauseTargets.forEach(function (target) {
      if (target) {
        target.addEventListener('mouseenter', function () { isModuleHovered = true; });
        target.addEventListener('mouseleave', function () { isModuleHovered = false; });
      }
    });

    // Start auto slide
    startModuleAutoSlide();
  }

  // =========================================================================
  // 3. Automated 5-Step Connected Workflow Slider & Progress Fill
  // =========================================================================
  var workflowContainer = document.getElementById('workflowTimelineContainer');
  var workflowContentCard = document.getElementById('workflowContentCard');
  var stepNodes = document.querySelectorAll('.workflow-step-node');
  var stepPanes = document.querySelectorAll('.workflow-step-pane');
  var progressBar = document.getElementById('workflowProgressBar');
  var nextStepButtons = document.querySelectorAll('.workflow-next-btn');

  if (stepNodes.length > 0 && stepPanes.length > 0) {
    var stepList = Array.from(stepNodes);
    var currentStepIndex = 0;
    var stepTimer = null;
    var isWorkflowHovered = false;

    function activateStep(index) {
      if (index < 0) index = stepList.length - 1;
      if (index >= stepList.length) index = 0;
      currentStepIndex = index;

      var activeNode = stepList[currentStepIndex];
      var targetStep = activeNode.getAttribute('data-step-target');

      // Update Node classes
      stepNodes.forEach(function (node) { node.classList.remove('active'); });
      activeNode.classList.add('active');

      // Update Pane classes
      stepPanes.forEach(function (pane) { pane.classList.remove('active'); });
      var targetPane = document.getElementById('pane-' + targetStep);
      if (targetPane) {
        targetPane.classList.add('active');
      }

      // Update Progress Bar Fill
      if (progressBar) {
        var percentage = (currentStepIndex / (stepList.length - 1)) * 100;
        progressBar.style.width = percentage + '%';
      }
    }

    function startStepAutoSlide() {
      stopStepAutoSlide();
      stepTimer = setInterval(function () {
        if (!isWorkflowHovered) {
          activateStep(currentStepIndex + 1);
        }
      }, 5000);
    }

    function stopStepAutoSlide() {
      if (stepTimer) {
        clearInterval(stepTimer);
        stepTimer = null;
      }
    }

    // Node Clicks
    stepNodes.forEach(function (node, idx) {
      node.addEventListener('click', function () {
        activateStep(idx);
        startStepAutoSlide();
      });
    });

    // "Proceed to..." next buttons
    nextStepButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var targetStep = this.getAttribute('data-next');
        var targetIdx = stepList.findIndex(function (n) {
          return n.getAttribute('data-step-target') === targetStep;
        });
        if (targetIdx !== -1) {
          activateStep(targetIdx);
          startStepAutoSlide();
        }
      });
    });

    // Pause on Hover
    var wfTargets = [workflowContainer, workflowContentCard];
    wfTargets.forEach(function (target) {
      if (target) {
        target.addEventListener('mouseenter', function () { isWorkflowHovered = true; });
        target.addEventListener('mouseleave', function () { isWorkflowHovered = false; });
      }
    });

    // Initialize progress bar width and start auto rotation
    activateStep(0);
    startStepAutoSlide();
  }

  // =========================================================================
  // 4. FAQ Accordion
  // =========================================================================
  var faqCards = document.querySelectorAll('.faq-card');
  faqCards.forEach(function (card) {
    var faqHeader = card.querySelector('.faq-header');
    var faqContent = card.querySelector('.faq-content');

    if (faqHeader && faqContent) {
      faqHeader.addEventListener('click', function () {
        var isOpen = card.classList.contains('active');

        // Close other accordions
        faqCards.forEach(function (other) {
          if (other !== card && other.classList.contains('active')) {
            other.classList.remove('active');
            var otherContent = other.querySelector('.faq-content');
            if (otherContent) {
              otherContent.style.maxHeight = null;
            }
          }
        });

        // Toggle current
        if (isOpen) {
          card.classList.remove('active');
          faqContent.style.maxHeight = null;
        } else {
          card.classList.add('active');
          faqContent.style.maxHeight = faqContent.scrollHeight + 'px';
        }
      });
    }
  });

  // Open first FAQ by default
  if (faqCards.length > 0) {
    var firstCard = faqCards[0];
    var firstContent = firstCard.querySelector('.faq-content');
    if (firstContent) {
      firstCard.classList.add('active');
      firstContent.style.maxHeight = firstContent.scrollHeight + 'px';
    }
  }

  // =========================================================================
  // 5. Smooth Anchor Scrolling with Header Offset
  // =========================================================================
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      var href = this.getAttribute('href');
      if (href && href !== '#' && href.length > 1) {
        var targetElement = document.querySelector(href);
        if (targetElement) {
          e.preventDefault();
          var headerOffset = 80;
          var elementPosition = targetElement.getBoundingClientRect().top;
          var offsetPosition = elementPosition + window.pageYOffset - headerOffset;

          window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
          });

          // Close mobile navbar if open
          var navCollapse = document.getElementById('navbarLandingContent');
          if (navCollapse && navCollapse.classList.contains('show')) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
              var bsCollapse = bootstrap.Collapse.getInstance(navCollapse);
              if (bsCollapse) bsCollapse.hide();
            } else {
              navCollapse.classList.remove('show');
            }
          }
        }
      }
    });
  });

  // =========================================================================
  // 6. Dynamic Count-Up Animation for Social Proof Counter
  // =========================================================================
  var counters = document.querySelectorAll('.counter-highlight');
  if (counters.length > 0 && 'IntersectionObserver' in window) {
    var counterObserver = new IntersectionObserver(function (entries, observer) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var el = entry.target;
          var targetVal = parseInt(el.getAttribute('data-count'), 10) || 0;

          if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || targetVal <= 0) {
            el.textContent = targetVal;
            observer.unobserve(el);
            return;
          }

          var duration = Math.min(1600, Math.max(500, targetVal * 120));
          var startTime = null;

          function animateCount(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var easeOut = 1 - Math.pow(1 - progress, 3);
            var current = Math.floor(easeOut * targetVal);
            el.textContent = current;

            if (progress < 1) {
              requestAnimationFrame(animateCount);
            } else {
              el.textContent = targetVal;
            }
          }

          requestAnimationFrame(animateCount);
          observer.unobserve(el);
        }
      });
    }, { threshold: 0.3 });

    counters.forEach(function (counter) {
      counterObserver.observe(counter);
    });
  }

  // =========================================================================
  // 7. Contact Form AJAX Submission with Toast Notification
  // =========================================================================
  var contactForm = document.getElementById('contactUsForm');
  if (contactForm) {
    contactForm.addEventListener('submit', function (e) {
      e.preventDefault();

      var submitBtn = contactForm.querySelector('button[type="submit"]');
      var originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span><i class="fas fa-spinner fa-spin me-2"></i>Sending...</span>';
      }

      var formData = new FormData(contactForm);

      fetch(contactForm.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(function (response) {
        return response.json();
      })
      .then(function (data) {
        if (data.success || data.status === 'success') {
          contactForm.reset();

          if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 5000,
              timerProgressBar: true,
              didOpen: function (toast) {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
              }
            });

            Toast.fire({
              icon: 'success',
              title: data.message || 'Thank you! Your message has been sent successfully. Our team will contact you shortly.'
            });
          }
        } else {
          var errorText = data.message || 'Could not send message. Please try again.';
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 5000,
              timerProgressBar: true,
              icon: 'error',
              title: errorText
            });
          } else {
            alert(errorText);
          }
        }
      })
      .catch(function (error) {
        // Fallback: submit form traditionally if AJAX fails
        contactForm.submit();
      })
      .finally(function () {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnHtml;
        }
      });
    });
  }
});
