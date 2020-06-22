/* Url Shortener */

document.querySelector('#single-link') && document.querySelector('#single-link').addEventListener("click", function() {
    document.querySelectorAll('.single-link').forEach(element => element.classList.add('d-flex'));
    document.querySelectorAll('.single-link-col').forEach(element => element.classList.add('d-block'));
    document.querySelectorAll('.multi-link').forEach(element => element.classList.remove('d-flex'));
    window.setTimeout(function () {
        document.querySelector('#i_url').focus();
    }, 0);
});

document.querySelector('#multi-link') && document.querySelector('#multi-link').addEventListener("click", function() {
    document.querySelectorAll('.multi-link').forEach(element => element.classList.add('d-flex'));
    document.querySelectorAll('.single-link-col').forEach(element => element.classList.remove('d-block'));
    document.querySelectorAll('.single-link').forEach(element => element.classList.remove('d-flex'));
    window.setTimeout(function () {
        document.querySelector('#i_urls').focus();
    }, 0);
});

document.querySelector('.home-copy') && document.querySelector('.home-copy').addEventListener('click', function () {
    this.querySelectorAll('span').forEach(function (element) {
        element.classList.toggle('d-none');
    });
    this.classList.add('btn-success');
    this.classList.remove('btn-primary');

    document.querySelector('#copy-form-container input').removeAttribute('style');

    setTimeout(function() {
        $('#copy-form-container').fadeOut('done', function () {
            $('#short-form-container').fadeIn();
        });
    }, 500);

});

// Set dynamic height to the URLs text area
document.querySelector('#i_urls') && document.querySelector('#i_urls').addEventListener("input", (function () {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
    this.style.overflowY = 'hidden';
}), false);

// Copy tooltip
$('[data-toggle="tooltip-copy"]').tooltip({animation: true});

// Info tooltip
$('[data-toggle="tooltip-url"]').tooltip({animation: true, delay: {"show": 500, "hide": 100}});

document.querySelectorAll('[data-toggle="tooltip-copy"]').forEach(function (element) {
    element.addEventListener('click', function (e) {
        // Update the tooltip
        $(this).tooltip('hide').attr('data-original-title', this.dataset.copied).tooltip('show');
    });

    element.addEventListener('mouseleave', function () {
        this.setAttribute('data-original-title', this.dataset.copy);
    });
});

document.querySelectorAll('.link-copy').forEach(function (element) {
    element.addEventListener('click', function (e) {
        e.preventDefault();

        try {
            let url = this.dataset.url;
            let tempInput = document.createElement('input');

            document.body.append(tempInput);

            // Set the input's value to the url to be copied
            tempInput.value = url;

            // Select the input's value to be copied
            tempInput.select();

            // Copy the url
            document.execCommand("copy");

            // Remove the temporary input
            tempInput.remove();
        } catch (e) {}
    });
});

// Initialize toasts
$('.toast').toast();

/* Pricing plans */
document.querySelector('#plan-monthly') && document.querySelector('#plan-monthly').addEventListener("click", function() {
    document.querySelectorAll('.plan-monthly').forEach(element => element.classList.add('d-block'));
    document.querySelectorAll('.plan-yearly').forEach(element => element.classList.remove('d-block'));
});

document.querySelector('#plan-yearly') && document.querySelector('#plan-yearly').addEventListener("click", function() {
    document.querySelectorAll('.plan-yearly').forEach(element => element.classList.add('d-block'));
    document.querySelectorAll('.plan-monthly').forEach(element => element.classList.remove('d-block', 'plan-preload'));
});

/* Payment form */
if (document.querySelector('#payment-form')) {
    let radios = document.querySelector('#payment-form').elements["interval"];

    // Event listener for interval changes
    for(var i = 0, max = radios.length; i < max; i++) {
        radios[i].onchange = function() {
            // Update the URL address
            history.pushState(null, null, this.dataset.periodUrl);

            // Update the form action
            document.querySelector('#payment-form').setAttribute('action', this.dataset.formUrl);

            // Update the Summary
            document.querySelector('#billing-period').textContent = this.dataset.billingPeriod;
            document.querySelector('#plan-price').textContent = this.dataset.planPrice;
            document.querySelector('#total-price').textContent = this.dataset.planPrice;
            if (document.querySelector('#pay-amount')) {
                document.querySelector('#pay-amount').textContent = this.dataset.planPrice;
            }
        }
    }
}

/* Share Modal */
document.querySelector('.link-share') && document.querySelector('.link-share').addEventListener('click', function() {
    let url = this.dataset.url;
    let title = this.dataset.title;
    let qr = this.dataset.qr;

    document.querySelectorAll('#share-twitter, #share-facebook, #share-reddit, #share-pinterest, #share-linkedin, #share-email, #share-qr').forEach(function(element) {
        element.setAttribute('data-url', url);
        element.setAttribute('data-title', title);
        element.setAttribute('data-qr', qr);
    });
});

document.querySelector('#share-twitter') && document.querySelector('#share-twitter').addEventListener('click', function (e) {
    e.preventDefault();

    popupCenter("https://twitter.com/intent/tweet?text="+encodeURIComponent(this.dataset.title)+"&url="+encodeURIComponent(this.dataset.url), encodeURIComponent(this.dataset.title), 550, 250);
});

document.querySelector('#share-facebook') && document.querySelector('#share-facebook').addEventListener('click', function (e) {
    e.preventDefault();

    popupCenter("https://www.facebook.com/sharer/sharer.php?u="+encodeURIComponent(this.dataset.url), encodeURIComponent(this.dataset.title), 550, 300);
});

document.querySelector('#share-reddit') && document.querySelector('#share-reddit').addEventListener('click', function (e) {
    e.preventDefault();

    popupCenter("http://www.reddit.com/submit?url="+encodeURIComponent(this.dataset.url), encodeURIComponent(this.dataset.title), 550, 530);
});

document.querySelector('#share-pinterest') && document.querySelector('#share-pinterest').addEventListener('click', function (e) {
    e.preventDefault();

    popupCenter("http://pinterest.com/pin/create/button/?url="+encodeURIComponent(this.dataset.url)+"&description="+encodeURIComponent(this.dataset.title), encodeURIComponent(this.dataset.title), 550, 300);
});

document.querySelector('#share-linkedin') && document.querySelector('#share-linkedin').addEventListener('click', function (e) {
    e.preventDefault();

    popupCenter("https://www.linkedin.com/sharing/share-offsite/?url="+encodeURIComponent(this.dataset.url), encodeURIComponent(this.dataset.title), 550, 300);
});

document.querySelector('#share-email') && document.querySelector('#share-email').addEventListener('click', function (e) {
    e.preventDefault();

    window.open("mailto:?Subject="+encodeURIComponent(this.dataset.title)+"&body="+encodeURIComponent(this.dataset.title)+" - "+encodeURIComponent(this.dataset.url), "_self");
});

document.querySelector('#share-qr') && document.querySelector('#share-qr').addEventListener('click', function (e) {
    e.preventDefault();

    popupCenter(this.dataset.qr, encodeURIComponent(this.dataset.title), 300, 300);
});

/* Slide Menu */
document.querySelectorAll('.slide-menu-toggle').forEach(function(element) {
    element.addEventListener('click', function() {
        document.querySelector('#slide-menu').classList.toggle('active');
    });
});

/* Stats Menu */
document.querySelector('[name="stats-menu"]') && document.querySelector('[name="stats-menu"]').addEventListener('change', function () {
    window.location.href = document.querySelector('[name="stats-menu"]').value;
});

/* Delete */
document.querySelectorAll('[data-target="#deleteLinkModal"]').forEach(function (element) {
    element.addEventListener('click', function () {
        document.querySelector('#deleteLinkMessage').textContent = this.dataset.text;
        document.querySelector('#deleteLinkModal form').setAttribute('action', this.dataset.action);
    });
});

/* Table Filters */
document.querySelector('#search-filters') && document.querySelector('#search-filters').addEventListener('click', function(e) {
    e.stopPropagation();
});

/* Dynamic field additions and deletions */
document.querySelectorAll('#geo-container, #platform-container').forEach(element => {
    element.addEventListener('click', function (e) {
        let parentId = this.getAttribute('id');

        if (e.target.closest('.input-delete')) {
            // Delete the inputs parent container
            e.target.closest('.input-delete').parentNode.parentNode.parentNode.parentNode.remove();

            // If there are no inputs left, enable the dummy inputs
            if (element.querySelectorAll('select').length == 1) {
                element.querySelector('input[name="' + (element.getAttribute('id') == 'geo-container' ? 'geo' : 'platform') + '[empty][key]"]').removeAttribute('disabled');
                element.querySelector('input[name="' + (element.getAttribute('id') == 'geo-container' ? 'geo' : 'platform') + '[empty][value]"]').removeAttribute('disabled');
            }
        }

        if (e.target.closest('.input-add')) {
            // Clone the input template
            let input = document.querySelector('#' + parentId + ' .input-template').cloneNode(true);

            // Clean up class names
            input.classList.remove('d-none', 'input-template');

            // Enable the inputs
            input.querySelector('select').removeAttribute('disabled');
            input.querySelector('input').removeAttribute('disabled');

            let inputId = new Date().getTime();

            input.querySelector('select').setAttribute('name', (element.getAttribute('id') == 'geo-container' ? 'geo' : 'platform') + '['+ inputId +'][key]');
            input.querySelector('input').setAttribute('name', (element.getAttribute('id') == 'geo-container' ? 'geo' : 'platform') + '['+ inputId +'][value]');

            element.querySelector('input[name="' + (element.getAttribute('id') == 'geo-container' ? 'geo' : 'platform') + '[empty][key]"]') && element.querySelector('input[name="' + (element.getAttribute('id') == 'geo-container' ? 'geo' : 'platform') + '[empty][key]"]').setAttribute('disabled', 'disabled');
            element.querySelector('input[name="' + (element.getAttribute('id') == 'geo-container' ? 'geo' : 'platform') + '[empty][value]"]') && element.querySelector('input[name="' + (element.getAttribute('id') == 'geo-container' ? 'geo' : 'platform') + '[empty][value]"]').setAttribute('disabled', 'disabled');

            // Append the inputs to the DOM
            document.querySelector('#' + parentId + ' .input-content').append(input);
        }
    });
});

/* UTM Builder */
document.querySelector('#utm_builder') && document.querySelector('#utm_builder').addEventListener('click', function () {
    let urlInput = document.querySelector('#i_url');

    let sources = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];

    try {
        let url = new URL(urlInput.value);

        sources.forEach(function(source) {
            // If the URL already has a source set
            if (url.searchParams.has(source)) {
                // Update the input with the current source value
                document.querySelector('#' + source).value = url.searchParams.get(source);
            }
        });
    } catch(e) {
        sources.forEach(function(source) {
            // Update the input with the current source value
            document.querySelector('#' + source).value = '';
        });
    }
});

document.querySelectorAll('#utm_source, #utm_medium, #utm_campaign, #utm_term, #utm_content').forEach(element => {
    element.addEventListener('input', function () {
        let urlInput = document.querySelector('#i_url');

        try {
            let url = new URL(urlInput.value);

            let target = element.getAttribute('id');

            let inputValue = document.querySelector('#' + target).value;

            if (inputValue === "") {
                url.searchParams.delete(target);
            } else {
                url.searchParams.set(target, inputValue);
            }

            urlInput.value = url.href;
        } catch (e) {

        }
    });
});

/* General */
$('[data-toggle="tooltip"]').tooltip({animation: true, trigger: 'hover', boundary: 'window'});

document.querySelector('[data-scroll-to="features"]') && document.querySelector('[data-scroll-to="features"]').addEventListener('click', function (e) {
    doScrolling('#features', 500, 72);
    e.preventDefault();
});

/**
 * Center the pop-up window
 *
 * @param url
 * @param title
 * @param w
 * @param h
 */
let popupCenter = (url, title, w, h) => {
    // Fixes dual-screen position                         Most browsers      Firefox
    let dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : window.screenX;
    let dualScreenTop = window.screenTop != undefined ? window.screenTop : window.screenY;

    let width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    let height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    let systemZoom = width / window.screen.availWidth;
    let left = (width - w) / 2 / systemZoom + dualScreenLeft;
    let top = (height - h) / 2 / systemZoom + dualScreenTop;
    let newWindow = window.open(url, title, 'scrollbars=yes, width=' + w / systemZoom + ', height=' + h / systemZoom + ', top=' + top + ', left=' + left);

    // Puts focus on the newWindow
    if (window.focus) newWindow.focus();
};

/**
 * Get the value of a given cookie
 *
 * @param   name
 * @returns {*}
 */
let getCookie = (name) => {
    let cn = name + '=';
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');

    for(let i = 0; i <ca.length; i++) {
        let c = ca[i];
        while(c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if(c.indexOf(cn) == 0) {
            return c.substring(cn.length, c.length);
        }
    }
    return '';
};

/**
 * Set a cookie
 *
 * @param   name
 * @param   value
 * @param   expire
 * @param   path
 */
let setCookie = (name, value, expire, path) => {
    document.cookie = name + "=" + value + ";expires=" + (new Date(expire).toUTCString()) + ";path=" + path;
};

let getElementY = (query) => {
    return window.pageYOffset + document.querySelector(query).getBoundingClientRect().top;
};

let doScrolling = (element, duration, offset) => {
    let startingY = window.pageYOffset;
    let elementY = getElementY(element);
    let targetY = document.body.scrollHeight - elementY < window.innerHeight ? document.body.scrollHeight - window.innerHeight : elementY;
    let diff = targetY - offset - startingY;
    let easing = function (t) { return t<.5 ? 4*t*t*t : (t-1)*(2*t-2)*(2*t-2)+1 };
    let start;

    if (!diff) return;

    // Bootstrap our animation - it will get called right before next frame shall be rendered.
    window.requestAnimationFrame(function step(timestamp) {
        if (!start) start = timestamp;
        // Elapsed milliseconds since start of scrolling.
        let time = timestamp - start;
        // Get percent of completion in range [0, 1].
        let percent = Math.min(time / duration, 1);
        // Apply the easing.
        // It can cause bad-looking slow frames in browser performance tool, so be careful.
        percent = easing(percent);

        window.scrollTo(0, startingY + diff * percent);

        // Proceed with animation as long as we wanted it to.
        if (time < duration) {
            window.requestAnimationFrame(step);
        }
    })
};