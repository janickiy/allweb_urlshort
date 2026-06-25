import * as bootstrap from 'bootstrap';
import 'admin-lte';

window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-toggle]').forEach((element) => {
    if (!element.dataset.bsToggle) {
      element.dataset.bsToggle = element.dataset.toggle;
    }
  });

  document.querySelectorAll('[data-target]').forEach((element) => {
    if (!element.dataset.bsTarget) {
      element.dataset.bsTarget = element.dataset.target;
    }
  });

  document.querySelectorAll('[data-dismiss]').forEach((element) => {
    if (!element.dataset.bsDismiss) {
      element.dataset.bsDismiss = element.dataset.dismiss;
    }
  });

  document.querySelectorAll('[data-html]').forEach((element) => {
    if (!element.dataset.bsHtml) {
      element.dataset.bsHtml = element.dataset.html;
    }
  });

  document.querySelectorAll('[data-bs-toggle="tooltip"], [data-bs-toggle="tooltip-url"], [data-bs-toggle="tooltip-copy"]').forEach((element) => {
    new bootstrap.Tooltip(element, {
      html: element.dataset.bsHtml === 'true',
    });
  });
});
