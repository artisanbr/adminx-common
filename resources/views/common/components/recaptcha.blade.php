@props([
    'site' => new \Adminx\Common\Models\Site(),
    'noAjax' => false,
])
@once
    @prepend('js')
        <script src="https://www.google.com/recaptcha/api.js?render={{ $site->config->recaptcha_site_key }}"></script>
        <script>
            const grecaptchaKey = '{{ $site->config->recaptcha_site_key }}';

            @if($noAjax)
            grecaptcha.ready(function () {
                let forms = document.querySelectorAll('form[data-grecaptcha-action]');

                console.log(forms);

                Array.from(forms).forEach(function (form) {
                    form.onsubmit = (e) => {
                        e.preventDefault();

                        let grecaptchaAction = form.getAttribute('data-grecaptcha-action');

                        grecaptcha.execute(grecaptchaKey, {action: grecaptchaAction})
                            .then((token) => {

                                $(form).find('input#recaptcha-token').val(token);
                                $(form).find('input#recaptcha-action').val(grecaptchaAction);

                                form.submit();
                            });
                    }
                });

            });
            @else

            class Recaptcha {

                _form = null;
                _hidden_field_token = null;
                _hidden_field_action = null;

                constructor(form) {
                    this.form = form;
                    return this;
                }

                async build(callback) {
                    var self = this,
                        $form = self.form;

                    let grecaptchaAction = $form.data('grecaptcha-action') || '{{ config("services.recaptcha.action") }}';

                    grecaptchaAction = String(grecaptchaAction).replaceAll('-','_');

                    console.log(grecaptchaAction);

                    grecaptcha.ready(function () {
                        // do request for recaptcha token
                        // response is promise with passed token
                        grecaptcha.execute(grecaptchaKey, {action: grecaptchaAction}).then(function (token) {
                            self.hidden_field_token.val(token);
                            self.hidden_field_action.val(grecaptchaAction);
                            callback($form);
                        });
                    });


                }

                complete(callback) {
                    return this.build(callback);
                }

                done(callback) {
                    return this.complete(callback);
                }

                then(callback) {
                    return this.complete(callback);
                }

                ajax(callback) {
                    return this.complete(callback);
                }

                /** SETs **/
                set form(value) {
                    this._form = $(value);
                    this.hidden_field_token = 'input#recaptcha-token';
                    this.hidden_field_action = 'input#recaptcha-action';
                }

                set hidden_field_token(value) {
                    this._hidden_field_token = this.form.find(value);
                }

                set hidden_field_action(value) {
                    this._hidden_field_action = this.form.find(value);
                }

                /** GETs */
                get form() {
                    return this._form;
                }

                get hidden_field_token() {
                    return this._hidden_field_token;
                }

                get hidden_field_action() {
                    return this._hidden_field_action;
                }

                /** Statics */
                static form(form) {
                    return new Recaptcha(form);
                }
            }

            @endif
        </script>
    @endprepend
@endonce
<input type="hidden" name="recaptcha_token" id="recaptcha-token">
<input type="hidden" name="recaptcha_action" id="recaptcha-action">

