$(function () {
    // 1. Global AJAX CSRF header setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    // 2. Button Spinner Helper
    function showButtonSpinner($button) {
        var originalHtml = $button.html();
        $button.data('original-html', originalHtml);
        $button.prop('disabled', true);
        $button.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>' + originalHtml);
    }

    function resetButtonSpinner($button) {
        var originalHtml = $button.data('original-html');
        if (originalHtml) {
            $button.prop('disabled', false);
            $button.html(originalHtml);
        }
    }

    // 3. Normal Form Submit Loader [data-loader]
    $(document).on('submit', 'form[data-loader]', function () {
        var $button = $(this).find('button[type="submit"]');
        showButtonSpinner($button);
    });

    // 4. AJAX Form Handler [data-ajax]
    $(document).on('submit', 'form[data-ajax]', function (e) {
        e.preventDefault();

        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        var alertId = $form.data('ajax-alert');
        var $alert = alertId ? $('#' + alertId) : null;
        var $otpBoxes = $form.find('.otp-box');

        // Check 6-digit OTP if form contains OTP boxes
        if ($otpBoxes.length > 0) {
            var otpValue = '';
            $otpBoxes.each(function () {
                otpValue += $(this).val();
            });

            if (otpValue.length < 6) {
                $otpBoxes.each(function () {
                    if (!$(this).val()) {
                        $(this).focus();
                        return false;
                    }
                });
                return;
            }

            $form.find('#otp-hidden').val(otpValue);
        }

        // Show spinner and reset previous errors
        showButtonSpinner($submitBtn);
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.step-error').addClass('d-none').text('');
        if ($alert) {
            $alert.addClass('d-none').text('');
        }

        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method') || 'POST',
            data: $form.serialize(),
            success: function (response) {
                if ($alert && response.message) {
                    $alert.removeClass('d-none alert-danger').addClass('alert-success').text(response.message);
                }

                var redirectUrl = response.redirect || (response.data && response.data.redirect);
                if (redirectUrl) {
                    setTimeout(function () {
                        window.location.href = redirectUrl;
                    }, 300);
                }
            },
            error: function (xhr) {
                resetButtonSpinner($submitBtn);

                if (xhr.status === 419) {
                    window.location.reload();
                    return;
                }

                var errorMessage = 'An error occurred. Please try again.';

                // Parse Laravel validation errors
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function (field, messages) {
                            var $input = $form.find('[name="' + field + '"]');
                            $input.addClass('is-invalid');
                            $input.closest('.input-group').addClass('is-invalid');

                            if (field === 'otp') {
                                $form.find('.otp-box').addClass('is-invalid');
                            }

                            var $fieldError = $form.find('#' + field.replace(/\./g, '_') + '-error');
                            if ($fieldError.length) {
                                $fieldError.removeClass('d-none').text(messages[0]);
                            }
                        });

                        var firstError = Object.values(xhr.responseJSON.errors)[0];
                        if (firstError && firstError.length) {
                            errorMessage = firstError[0];
                        }
                    } else if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                        var $fallbackError = $form.find('#otp-error, #login-error, #email-error, #password-error, .step-error').first();
                        if ($fallbackError.length) {
                            $fallbackError.removeClass('d-none').text(errorMessage);
                        }
                    }
                }

                if ($alert) {
                    $alert.removeClass('d-none alert-success text-success').addClass('alert-danger text-danger').text(errorMessage);
                }

                if ($otpBoxes.length > 0) {
                    $otpBoxes.val('');
                    $form.find('#otp-hidden').val('');
                    $otpBoxes.first().focus();
                }
            }
        });
    });

    // 5. Password Visibility Toggle (.toggle-pwd)
    $(document).on('click', '.toggle-pwd', function () {
        var targetSelector = $(this).data('target');
        var $input = $(targetSelector);

        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
        } else {
            $input.attr('type', 'password');
        }

        $(this).find('.eye-icon, .eye-slash-icon').toggleClass('d-none');
    });

    // 6. OTP 6-Box Navigation (.otp-box)
    $(document).on('input', '.otp-box', function () {
        var $form = $(this).closest('form');
        $form.find('.otp-box').removeClass('is-invalid');
        $form.find('#otp-error').addClass('d-none').text('');

        var cleanValue = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(cleanValue);

        var currentIndex = parseInt($(this).data('index'), 10);
        var $allBoxes = $(this).closest('.otp-container').find('.otp-box');

        if (cleanValue && currentIndex < 5) {
            $allBoxes.eq(currentIndex + 1).focus();
        }

        // Auto-submit when all 6 digits are typed
        var fullCode = '';
        $allBoxes.each(function () {
            fullCode += $(this).val();
        });

        if (fullCode.length === 6) {
            $(this).closest('form').find('#otp-hidden').val(fullCode);
            $(this).closest('form').trigger('submit');
        }
    });

    $(document).on('keydown', '.otp-box', function (e) {
        if (e.key === 'Backspace' && !$(this).val()) {
            var currentIndex = parseInt($(this).data('index'), 10);
            if (currentIndex > 0) {
                var $allBoxes = $(this).closest('.otp-container').find('.otp-box');
                $allBoxes.eq(currentIndex - 1).focus();
            }
        }
    });

    $(document).on('paste', '.otp-box[data-index="0"]', function (e) {
        e.preventDefault();

        var clipboardText = (e.originalEvent.clipboardData || window.clipboardData).getData('text') || '';
        var digits = clipboardText.replace(/[^0-9]/g, '').slice(0, 6);
        var $allBoxes = $(this).closest('.otp-container').find('.otp-box');

        for (var i = 0; i < digits.length; i++) {
            $allBoxes.eq(i).val(digits[i]);
        }

        if (digits.length === 6) {
            $allBoxes.eq(5).focus();
            $(this).closest('form').find('#otp-hidden').val(digits);
            $(this).closest('form').trigger('submit');
        } else if (digits.length > 0) {
            $allBoxes.eq(digits.length).focus();
        }
    });

    // 7. Resend Code Countdown [data-resend]
    function runResendCountdown($button, totalSeconds) {
        var $textSpan = $button.find('.resend-text');
        var $countdownSpan = $button.find('.resend-countdown');
        var secondsLeft = totalSeconds;

        $button.prop('disabled', true);
        $textSpan.addClass('d-none');
        $countdownSpan.removeClass('d-none').text('(' + secondsLeft + 's)');

        var countdownInterval = setInterval(function () {
            secondsLeft--;

            if (secondsLeft <= 0) {
                clearInterval(countdownInterval);
                $button.prop('disabled', false);
                $textSpan.removeClass('d-none');
                $countdownSpan.addClass('d-none');
            } else {
                $countdownSpan.text('(' + secondsLeft + 's)');
            }
        }, 1000);
    }

    // Initialize resend timer on page load
    $('[data-resend]').each(function () {
        var cooldown = parseInt($(this).data('resend-cooldown'), 10) || 60;
        runResendCountdown($(this), cooldown);
    });

    $(document).on('click', '[data-resend]', function () {
        var $button = $(this);
        var requestUrl = $button.data('resend');
        var alertId = $button.data('resend-alert');
        var $alert = alertId ? $('#' + alertId) : null;
        var cooldown = parseInt($button.data('resend-cooldown'), 10) || 60;
        var email = $button.data('resend-email') || $button.closest('form').find('input[name="email"]').val();

        $button.prop('disabled', true);

        $.ajax({
            url: requestUrl,
            type: 'POST',
            data: { email: email },
            success: function (response) {
                if ($alert) {
                    $alert.removeClass('d-none alert-danger text-danger').addClass('text-success').text(response.message || 'Code resent.');
                }
                runResendCountdown($button, cooldown);
            },
            error: function (xhr) {
                if (xhr.status === 419) {
                    window.location.reload();
                    return;
                }

                var errorMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error resending code.';
                if ($alert) {
                    $alert.removeClass('d-none alert-success text-success').addClass('text-danger').text(errorMsg);
                }

                $button.prop('disabled', false);
            }
        });
    });

    /* ==========================================================================
       8. In-Page Content Navigation (PJAX / [data-nav])
       Seamlessly transitions page content without a full browser reload.
       ========================================================================== */

    /**
     * Updates the active visual state on sidebar navigation links
     * to match the current target URL pathname.
     *
     * @param {string} targetUrl - The destination URL being navigated to.
     */
    function updateSidebarActiveState(targetUrl) {
        var currentPathname;

        try {
            currentPathname = new URL(targetUrl, window.location.origin).pathname;
        } catch (e) {
            currentPathname = targetUrl;
        }

        // Iterate through all desktop and mobile navigation links
        $('.sidebar-nav-item, .sidebar-nav-link').each(function () {
            var $link = $(this);
            var linkHref = $link.attr('href');

            // Skip empty or anchor-only links
            if (!linkHref || linkHref === '#') {
                return;
            }

            var linkPathname;
            try {
                linkPathname = new URL(linkHref, window.location.origin).pathname;
            } catch (e) {
                linkPathname = linkHref;
            }

            var isCurrentPage = (linkPathname === currentPathname);

            if (isCurrentPage) {
                $link.addClass('active');
                $link.removeClass('text-secondary');
            } else {
                $link.removeClass('active');
                $link.addClass('text-secondary');
            }
        });
    }

    /**
     * Dismisses any open Bootstrap modal or offcanvas drawer
     * to avoid lingering dark backdrops during page transitions.
     */
    function dismissActiveOverlays() {
        // Close mobile sidebar offcanvas if open
        var mobileSidebarElement = document.getElementById('mobileSidebarOffcanvas');
        if (mobileSidebarElement && typeof bootstrap !== 'undefined') {
            var offcanvasInstance = bootstrap.Offcanvas.getInstance(mobileSidebarElement);
            if (offcanvasInstance) {
                offcanvasInstance.hide();
            }
        }

        // Close any open modals
        $('.modal.show').each(function () {
            if (typeof bootstrap !== 'undefined') {
                var modalInstance = bootstrap.Modal.getInstance(this);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
        });

        // Clean up any remaining modal or offcanvas backdrops
        $('.modal-backdrop, .offcanvas-backdrop').remove();
        $('body').removeClass('modal-open').css('overflow', '');
    }

    /**
     * Loads a page's content asynchronously and injects it into #app-content.
     *
     * @param {string} destinationUrl - The target URL to fetch.
     * @param {boolean} shouldUpdateHistory - Whether to push the URL into browser history.
     */
    function navigateToPage(destinationUrl, shouldUpdateHistory) {
        var $contentContainer = $('#app-content');

        // If the main content container is missing, fallback to standard browser navigation
        if (!$contentContainer.length) {
            window.location.href = destinationUrl;
            return;
        }

        // Apply a gentle fade-out effect before fetching new content
        $contentContainer.addClass('app-content-fade');

        $.ajax({
            url: destinationUrl,
            type: 'GET',
            dataType: 'html',
            success: function (responseHtml) {
                // Parse the received HTML document
                var htmlParser = new DOMParser();
                var parsedDoc = htmlParser.parseFromString(responseHtml, 'text/html');

                // 1. Update the document title if available in the new page
                var newTitleElement = parsedDoc.querySelector('title');
                if (newTitleElement && newTitleElement.innerText) {
                    document.title = newTitleElement.innerText;
                }

                // 2. Locate the new page content
                var newContentElement = parsedDoc.querySelector('#app-content');
                if (!newContentElement) {
                    // If target page does not share the #app-content layout, do a normal redirect
                    window.location.href = destinationUrl;
                    return;
                }

                // 3. Dismiss any active overlays before injecting new content
                dismissActiveOverlays();

                // 4. Inject the new page content
                $contentContainer.html(newContentElement.innerHTML);

                // 5. Update active state across all sidebar navigation links
                updateSidebarActiveState(destinationUrl);

                // 6. Update browser history and address bar if requested
                if (shouldUpdateHistory) {
                    window.history.pushState({ url: destinationUrl }, '', destinationUrl);
                }

                // 7. Scroll smoothly to top of the page
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });

                // 8. Reveal the new content smoothly
                $contentContainer.removeClass('app-content-fade');
            },
            error: function () {
                // On request failure, fallback to full page redirect
                window.location.href = destinationUrl;
            }
        });
    }

    // Intercept clicks on links with the [data-nav] attribute
    $(document).on('click', 'a[data-nav]', function (event) {
        var targetHref = $(this).attr('href');

        // Verify the link is internal and not an empty anchor
        var isInternalLink = targetHref && (
            targetHref.startsWith('/') ||
            targetHref.startsWith(window.location.origin)
        );

        if (!targetHref || targetHref === '#' || !isInternalLink) {
            return;
        }

        // Prevent full page reload and navigate dynamically
        event.preventDefault();
        navigateToPage(targetHref, true);
    });

    // Handle browser navigation buttons (Back and Forward)
    window.addEventListener('popstate', function (event) {
        var historicalUrl = (event.state && event.state.url)
            ? event.state.url
            : window.location.href;

        navigateToPage(historicalUrl, false);
    });

    /* ==========================================================================
       9. My Books Horizontal Carousel Controls
       Enables smooth left/right scrolling with RTL language support.
       ========================================================================== */

    /**
     * Scrolls the books horizontal carousel track.
     *
     * @param {'next'|'prev'} direction - The direction to scroll ('next' or 'prev').
     */
    function scrollBooksSlider(direction) {
        var sliderElement = document.getElementById('booksSlider');

        // Exit safely if the carousel slider is not present on the current page
        if (!sliderElement) {
            return;
        }

        var scrollAmountPerClick = 280; // Approximate card width + gap
        var isRtlDirection = (document.documentElement.getAttribute('dir') === 'rtl');

        // Calculate scroll offset based on direction and page reading direction
        var scrollOffset = (direction === 'next') ? scrollAmountPerClick : -scrollAmountPerClick;

        if (isRtlDirection) {
            scrollOffset = -scrollOffset;
        }

        sliderElement.scrollBy({
            left: scrollOffset,
            behavior: 'smooth'
        });
    }

    // Previous button click handler
    $(document).on('click', '#booksPrevBtn', function () {
        scrollBooksSlider('prev');
    });

    // Next button click handler
    $(document).on('click', '#booksNextBtn', function () {
        scrollBooksSlider('next');
    });
});

