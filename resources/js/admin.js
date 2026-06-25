import * as bootstrap from 'bootstrap';
import 'admin-lte';

window.bootstrap = bootstrap;

const legacyDataMap = {
  toggle: 'bsToggle',
  target: 'bsTarget',
  dismiss: 'bsDismiss',
  html: 'bsHtml',
  autohide: 'bsAutohide',
};

const normalizeLegacyBootstrapAttributes = () => {
  Object.entries(legacyDataMap).forEach(([legacy, modern]) => {
    document.querySelectorAll(`[data-${legacy}]`).forEach((element) => {
      if (!element.dataset[modern]) {
        element.dataset[modern] = element.dataset[legacy];
      }
    });
  });
};

const initTooltips = () => {
  document.querySelectorAll('[data-bs-toggle="tooltip"], [data-bs-toggle="tooltip-url"], [data-bs-toggle="tooltip-copy"]').forEach((element) => {
    const options = {
      html: element.dataset.bsHtml === 'true',
      trigger: 'hover',
      boundary: 'window',
    };

    if (element.dataset.bsToggle === 'tooltip-url') {
      options.delay = { show: 500, hide: 100 };
    }

    bootstrap.Tooltip.getOrCreateInstance(element, options);
  });
};

const initToasts = () => {
  document.querySelectorAll('.toast').forEach((element) => {
    bootstrap.Toast.getOrCreateInstance(element, {
      autohide: element.dataset.bsAutohide !== 'false',
    });
  });
};

const initCopyButtons = () => {
  document.querySelectorAll('.link-copy').forEach((element) => {
    element.addEventListener('click', async (event) => {
      event.preventDefault();

      try {
        if (navigator.clipboard) {
          await navigator.clipboard.writeText(element.dataset.url);
        } else {
          const tempInput = document.createElement('input');
          document.body.append(tempInput);
          tempInput.value = element.dataset.url;
          tempInput.select();
          document.execCommand('copy');
          tempInput.remove();
        }

        const tooltip = bootstrap.Tooltip.getOrCreateInstance(element);
        tooltip.setContent({ '.tooltip-inner': element.dataset.copied || element.title });
        tooltip.show();
      } catch {
        // Copy feedback is best-effort only.
      }
    });

    element.addEventListener('mouseleave', () => {
      const tooltip = bootstrap.Tooltip.getInstance(element);
      if (tooltip && element.dataset.copy) {
        tooltip.setContent({ '.tooltip-inner': element.dataset.copy });
      }
    });
  });
};

const popupCenter = (url, title, width, height) => {
  const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
  const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screenY;
  const screenWidth = window.innerWidth || document.documentElement.clientWidth || screen.width;
  const screenHeight = window.innerHeight || document.documentElement.clientHeight || screen.height;
  const systemZoom = screenWidth / window.screen.availWidth;
  const left = (screenWidth - width) / 2 / systemZoom + dualScreenLeft;
  const top = (screenHeight - height) / 2 / systemZoom + dualScreenTop;
  const popup = window.open(url, title, `scrollbars=yes,width=${width / systemZoom},height=${height / systemZoom},top=${top},left=${left}`);

  if (window.focus && popup) {
    popup.focus();
  }
};

const initShareModal = () => {
  document.querySelectorAll('.link-share').forEach((element) => {
    element.addEventListener('click', () => {
      document.querySelectorAll('#share-twitter, #share-facebook, #share-reddit, #share-pinterest, #share-linkedin, #share-email, #share-qr').forEach((shareTarget) => {
        shareTarget.dataset.url = element.dataset.url;
        shareTarget.dataset.title = element.dataset.title;
        shareTarget.dataset.qr = element.dataset.qr;
      });
    });
  });

  const shareActions = {
    'share-twitter': (element) => `https://twitter.com/intent/tweet?text=${encodeURIComponent(element.dataset.title)}&url=${encodeURIComponent(element.dataset.url)}`,
    'share-facebook': (element) => `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(element.dataset.url)}`,
    'share-reddit': (element) => `https://www.reddit.com/submit?url=${encodeURIComponent(element.dataset.url)}`,
    'share-pinterest': (element) => `https://pinterest.com/pin/create/button/?url=${encodeURIComponent(element.dataset.url)}&description=${encodeURIComponent(element.dataset.title)}`,
    'share-linkedin': (element) => `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(element.dataset.url)}`,
  };

  Object.entries(shareActions).forEach(([id, urlBuilder]) => {
    const element = document.getElementById(id);

    if (!element) {
      return;
    }

    element.addEventListener('click', (event) => {
      event.preventDefault();
      popupCenter(urlBuilder(element), encodeURIComponent(element.dataset.title), 550, id === 'share-reddit' ? 530 : 300);
    });
  });

  document.getElementById('share-email')?.addEventListener('click', function (event) {
    event.preventDefault();
    window.open(`mailto:?Subject=${encodeURIComponent(this.dataset.title)}&body=${encodeURIComponent(this.dataset.title)} - ${encodeURIComponent(this.dataset.url)}`, '_self');
  });

  document.getElementById('share-qr')?.addEventListener('click', function (event) {
    event.preventDefault();
    popupCenter(this.dataset.qr, encodeURIComponent(this.dataset.title), 300, 300);
  });
};

const initDeleteModal = () => {
  document.querySelectorAll('[data-bs-target="#deleteLinkModal"], [data-target="#deleteLinkModal"]').forEach((element) => {
    element.addEventListener('click', () => {
      const message = document.getElementById('deleteLinkMessage');
      const form = document.querySelector('#deleteLinkModal form');

      if (message) {
        message.textContent = element.dataset.text;
      }

      if (form) {
        form.setAttribute('action', element.dataset.action);
      }
    });
  });
};

const initDynamicTargetFields = () => {
  document.querySelectorAll('#geo-container, #platform-container').forEach((container) => {
    container.addEventListener('click', (event) => {
      const parentId = container.getAttribute('id');
      const targetType = parentId === 'geo-container' ? 'geo' : 'platform';

      if (event.target.closest('.input-delete')) {
        event.target.closest('.input-delete').parentNode.parentNode.parentNode.parentNode.remove();

        if (container.querySelectorAll('select').length === 1) {
          container.querySelector(`input[name="${targetType}[empty][key]"]`)?.removeAttribute('disabled');
          container.querySelector(`input[name="${targetType}[empty][value]"]`)?.removeAttribute('disabled');
        }
      }

      if (event.target.closest('.input-add')) {
        const input = document.querySelector(`#${parentId} .input-template`).cloneNode(true);
        input.classList.remove('d-none', 'input-template');
        input.querySelector('select').removeAttribute('disabled');
        input.querySelector('input').removeAttribute('disabled');

        const inputId = Date.now();
        input.querySelector('select').setAttribute('name', `${targetType}[${inputId}][key]`);
        input.querySelector('input').setAttribute('name', `${targetType}[${inputId}][value]`);

        container.querySelector(`input[name="${targetType}[empty][key]"]`)?.setAttribute('disabled', 'disabled');
        container.querySelector(`input[name="${targetType}[empty][value]"]`)?.setAttribute('disabled', 'disabled');
        document.querySelector(`#${parentId} .input-content`).append(input);
      }
    });
  });
};

const initUtmBuilder = () => {
  document.getElementById('utm_builder')?.addEventListener('click', () => {
    const urlInput = document.getElementById('i_url');
    const sources = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];

    try {
      const url = new URL(urlInput.value);
      sources.forEach((source) => {
        if (url.searchParams.has(source)) {
          document.getElementById(source).value = url.searchParams.get(source);
        }
      });
    } catch {
      sources.forEach((source) => {
        document.getElementById(source).value = '';
      });
    }
  });

  document.querySelectorAll('#utm_source, #utm_medium, #utm_campaign, #utm_term, #utm_content').forEach((element) => {
    element.addEventListener('input', () => {
      const urlInput = document.getElementById('i_url');

      try {
        const url = new URL(urlInput.value);
        const inputValue = element.value;

        if (inputValue === '') {
          url.searchParams.delete(element.id);
        } else {
          url.searchParams.set(element.id, inputValue);
        }

        urlInput.value = url.href;
      } catch {
        // Ignore invalid URL input until the user completes it.
      }
    });
  });
};

const initAdminInteractions = () => {
  document.getElementById('search-filters')?.addEventListener('click', (event) => {
    event.stopPropagation();
  });

  document.querySelectorAll('.app-sidebar .nav-link[aria-expanded]').forEach((element) => {
    element.addEventListener('click', () => {
      window.setTimeout(() => {
        element.setAttribute('aria-expanded', element.closest('.nav-item')?.classList.contains('menu-open') ? 'true' : 'false');
      });
    });
  });

  document.querySelector('[name="stats-menu"]')?.addEventListener('change', function () {
    window.location.href = this.value;
  });
};

document.addEventListener('DOMContentLoaded', () => {
  normalizeLegacyBootstrapAttributes();
  initTooltips();
  initToasts();
  initCopyButtons();
  initShareModal();
  initDeleteModal();
  initDynamicTargetFields();
  initUtmBuilder();
  initAdminInteractions();
});
