@php
    $formId = $formId ?? null;
    $action = $action ?? 'submit';
    $inputId = 'g_recaptcha_response_' . str_replace(['-', '.'], '_', $action);
@endphp

<input type="hidden" name="g-recaptcha-response" id="{{ $inputId }}">

<script src="https://www.google.com/recaptcha/api.js?render={{ urlencode((string) config('captcha.sitekey')) }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById(@json($formId));
        const tokenInput = document.getElementById(@json($inputId));
        const siteKey = @json(config('captcha.sitekey'));
        const action = @json($action);

        if (!form || !tokenInput || !siteKey || typeof grecaptcha === 'undefined') {
            return;
        }

        form.addEventListener('submit', (event) => {
            if (tokenInput.value) {
                return;
            }

            event.preventDefault();

            grecaptcha.ready(() => {
                grecaptcha.execute(siteKey, { action }).then((token) => {
                    tokenInput.value = token;

                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
