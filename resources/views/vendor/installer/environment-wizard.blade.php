@extends('vendor.installer.layouts.master')

@section('site_title', formatTitle([__('Installation'), config('info.software.name')]))

@section('content')

    <form method="post" action="{{ route('LaravelInstaller::environmentSaveWizard') }}" class="tabs-wrap">
        <div class="d-none card border-0 shadow-sm overflow-hidden">
            <div class="card-header">
                <div class="font-weight-medium py-1">{{ __('General settings') }}</div>
            </div>

            <div class="card-body">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="app_url">
                                {{ __('URL') }}
                            </label>
                            <input type="text" name="app_url" id="app_url" value="{{ route('home') }}" class="form-control{{ $errors->has('app_url') ? ' is-invalid' : '' }}">
                            @if ($errors->has('app_url'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('app_url') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-group">
                            <label for="i_title">{{ __('Title') }}</label>
                            <input type="text" name="app_name" id="i_title" class="form-control{{ $errors->has('app_name') ? ' is-invalid' : '' }}" value="{{ old('app_name') ?? config('info.software.name') }}">

                            @if ($errors->has('app_name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('app_name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header">
                <div class="font-weight-medium py-1">{{ __('Admin credentials') }}</div>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label for="i_name">{{ __('Name') }}</label>
                    <input id="i_name" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" autofocus>
                    @if ($errors->has('name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="i_email">{{ __('Email address') }}</label>
                    <input id="i_email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}">
                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="i_password">{{ __('Password') }}</label>
                    <input id="i_password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" value="{{ old('password') }}">
                    @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="i_password_confirmation">{{ __('Confirm password') }}</label>
                    <input id="i_password_confirmation" type="password" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation" value="{{ old('password_confirmation') }}">
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden mt-3">
            <div class="card-header">
                <div class="font-weight-medium py-1">{{ __('Database configuration') }}</div>
            </div>

            <div class="card-body">
                <div class="d-none form-group">
                    <label for="database_connection">
                        {{ __('Connection') }}
                    </label>
                    <select name="database_connection" id="database_connection" class="custom-select{{ $errors->has('database_connection') ? ' is-invalid' : '' }}">
                        @foreach(['mysql' => 'MySQL', 'sqlite' => 'SQLite', 'pgsql' => 'PostgreSQL', 'sqlsrv' => 'SQL Server'] as $key => $value)
                            <option value="{{ $key }}" {{ old('database_connection') == $key ? ' selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('database_connection'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('database_connection') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-row">
                    <div class="col">
                        <div class="form-group">
                            <label for="database_hostname">
                                {{ __('Hostname') }}
                            </label>
                            <input type="text" name="database_hostname" id="database_hostname" value="{{ old('database_hostname') ?? '127.0.0.1' }}" class="form-control{{ $errors->has('database_hostname') ? ' is-invalid' : '' }}">
                            @if ($errors->has('database_hostname'))
                                <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('database_hostname') }}</strong>
                        </span>
                            @endif
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-group">
                            <label for="database_port">
                                {{ __('Port') }}
                            </label>
                            <input type="number" name="database_port" id="database_port" value="{{ old('database_port') ?? '3306' }}" class="form-control{{ $errors->has('database_port') ? ' is-invalid' : '' }}">
                            @if ($errors->has('database_port'))
                                <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('database_port') }}</strong>
                        </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group {{ $errors->has('database_name') ? ' has-error ' : '' }}">
                    <label for="database_name">
                        {{ __('Name') }}
                    </label>
                    <input type="text" name="database_name" id="database_name" class="form-control{{ $errors->has('database_name') ? ' is-invalid' : '' }}" value="{{ old('database_name') }}">
                    @if ($errors->has('database_name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('database_name') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="database_username">
                        {{ __('User') }}
                    </label>
                    <input type="text" name="database_username" id="database_username" class="form-control{{ $errors->has('database_username') ? ' is-invalid' : '' }}" value="{{ old('database_username') }}">
                    @if ($errors->has('database_username'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('database_username') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="database_password">
                        {{ __('Password') }}
                    </label>
                    <input type="password" name="database_password" id="database_password" class="form-control{{ $errors->has('database_password') ? ' is-invalid' : '' }}" value="{{ old('database_password') }}">
                    @if ($errors->has('database_password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('database_password') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="d-none" id="tab3content">
                    <div class="block">

                        <div class="info">
                            <div class="d-none form-group {{ $errors->has('environment') ? ' has-error ' : '' }}">
                                <label for="environment">
                                    {{ trans('installer_messages.environment.wizard.form.app_environment_label') }}
                                </label>
                                <select name="environment" id="environment" onchange='checkEnvironment(this.value);'>
                                    <option value="production" selected>{{ trans('installer_messages.environment.wizard.form.app_environment_label_production') }}</option>
                                    <option value="local">{{ trans('installer_messages.environment.wizard.form.app_environment_label_local') }}</option>
                                    <option value="development">{{ trans('installer_messages.environment.wizard.form.app_environment_label_developement') }}</option>
                                    <option value="qa">{{ trans('installer_messages.environment.wizard.form.app_environment_label_qa') }}</option>
                                    <option value="other">{{ trans('installer_messages.environment.wizard.form.app_environment_label_other') }}</option>
                                </select>
                                <div id="environment_text_input" style="display: none;">
                                    <input type="text" name="environment_custom" id="environment_custom" placeholder="{{ trans('installer_messages.environment.wizard.form.app_environment_placeholder_other') }}"/>
                                </div>
                            </div>

                            <div class="d-none form-group {{ $errors->has('app_debug') ? ' has-error ' : '' }}">
                                <label for="app_debug">
                                    {{ trans('installer_messages.environment.wizard.form.app_debug_label') }}
                                </label>
                                <label for="app_debug_true">
                                    <input type="radio" name="app_debug" id="app_debug_true" value=true />
                                    {{ trans('installer_messages.environment.wizard.form.app_debug_label_true') }}
                                </label>
                                <label for="app_debug_false">
                                    <input type="radio" name="app_debug" id="app_debug_false" value=false checked />
                                    {{ trans('installer_messages.environment.wizard.form.app_debug_label_false') }}
                                </label>
                                @if ($errors->has('app_debug'))
                                    <span class="error-block">
                                <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                {{ $errors->first('app_debug') }}
                            </span>
                                @endif
                            </div>

                            <div class="d-none form-group {{ $errors->has('app_log_level') ? ' has-error ' : '' }}">
                                <label for="app_log_level">
                                    {{ trans('installer_messages.environment.wizard.form.app_log_level_label') }}
                                </label>
                                <select name="app_log_level" id="app_log_level">
                                    <option value="info" selected>{{ trans('installer_messages.environment.wizard.form.app_log_level_label_info') }}</option>
                                    <option value="debug">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_debug') }}</option>
                                    <option value="notice">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_notice') }}</option>
                                    <option value="warning">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_warning') }}</option>
                                    <option value="error">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_error') }}</option>
                                    <option value="critical">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_critical') }}</option>
                                    <option value="alert">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_alert') }}</option>
                                    <option value="emergency">{{ trans('installer_messages.environment.wizard.form.app_log_level_label_emergency') }}</option>
                                </select>
                                @if ($errors->has('app_log_level'))
                                    <span class="error-block">
                                <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                {{ $errors->first('app_log_level') }}
                            </span>
                                @endif
                            </div>

                            <div class="form-group {{ $errors->has('broadcast_driver') ? ' has-error ' : '' }}">
                                <label for="broadcast_driver">{{ trans('installer_messages.environment.wizard.form.app_tabs.broadcasting_label') }}
                                    <sup>
                                        <a href="https://laravel.com/docs/5.4/broadcasting" target="_blank" title="{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}">
                                            <i class="fa fa-info-circle fa-fw" aria-hidden="true"></i>
                                            <span class="sr-only">{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}</span>
                                        </a>
                                    </sup>
                                </label>
                                <input type="text" name="broadcast_driver" id="broadcast_driver" value="log" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.broadcasting_placeholder') }}" />
                                @if ($errors->has('broadcast_driver'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('broadcast_driver') }}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group {{ $errors->has('cache_driver') ? ' has-error ' : '' }}">
                                <label for="cache_driver">{{ trans('installer_messages.environment.wizard.form.app_tabs.cache_label') }}
                                    <sup>
                                        <a href="https://laravel.com/docs/5.4/cache" target="_blank" title="{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}">
                                            <i class="fa fa-info-circle fa-fw" aria-hidden="true"></i>
                                            <span class="sr-only">{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}</span>
                                        </a>
                                    </sup>
                                </label>
                                <input type="text" name="cache_driver" id="cache_driver" value="file" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.cache_placeholder') }}" />
                                @if ($errors->has('cache_driver'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('cache_driver') }}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group {{ $errors->has('session_driver') ? ' has-error ' : '' }}">
                                <label for="session_driver">{{ trans('installer_messages.environment.wizard.form.app_tabs.session_label') }}
                                    <sup>
                                        <a href="https://laravel.com/docs/5.4/session" target="_blank" title="{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}">
                                            <i class="fa fa-info-circle fa-fw" aria-hidden="true"></i>
                                            <span class="sr-only">{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}</span>
                                        </a>
                                    </sup>
                                </label>
                                <input type="text" name="session_driver" id="session_driver" value="file" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.session_placeholder') }}" />
                                @if ($errors->has('session_driver'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('session_driver') }}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group {{ $errors->has('queue_driver') ? ' has-error ' : '' }}">
                                <label for="queue_driver">{{ trans('installer_messages.environment.wizard.form.app_tabs.queue_label') }}
                                    <sup>
                                        <a href="https://laravel.com/docs/5.4/queues" target="_blank" title="{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}">
                                            <i class="fa fa-info-circle fa-fw" aria-hidden="true"></i>
                                            <span class="sr-only">{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}</span>
                                        </a>
                                    </sup>
                                </label>
                                <input type="text" name="queue_driver" id="queue_driver" value="sync" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.queue_placeholder') }}" />
                                @if ($errors->has('queue_driver'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('queue_driver') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="block">

                        <div class="info">
                            <div class="form-group {{ $errors->has('redis_hostname') ? ' has-error ' : '' }}">
                                <label for="redis_hostname">
                                    {{ trans('installer_messages.environment.wizard.form.app_tabs.redis_host') }}
                                    <sup>
                                        <a href="https://laravel.com/docs/5.4/redis" target="_blank" title="{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}">
                                            <i class="fa fa-info-circle fa-fw" aria-hidden="true"></i>
                                            <span class="sr-only">{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}</span>
                                        </a>
                                    </sup>
                                </label>
                                <input type="text" name="redis_hostname" id="redis_hostname" value="127.0.0.1" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.redis_host') }}" />
                                @if ($errors->has('redis_hostname'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('redis_hostname') }}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group {{ $errors->has('redis_password') ? ' has-error ' : '' }}">
                                <label for="redis_password">{{ trans('installer_messages.environment.wizard.form.app_tabs.redis_password') }}</label>
                                <input type="password" name="redis_password" id="redis_password" value="null" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.redis_password') }}" />
                                @if ($errors->has('redis_password'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('redis_password') }}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group {{ $errors->has('redis_port') ? ' has-error ' : '' }}">
                                <label for="redis_port">{{ trans('installer_messages.environment.wizard.form.app_tabs.redis_port') }}</label>
                                <input type="number" name="redis_port" id="redis_port" value="6379" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.redis_port') }}" />
                                @if ($errors->has('redis_port'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('redis_port') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="block">

                        <div class="info">
                            <div class="form-group {{ $errors->has('mail_driver') ? ' has-error ' : '' }}">
                                <label for="mail_driver">
                                    {{ trans('installer_messages.environment.wizard.form.app_tabs.mail_driver_label') }}
                                    <sup>
                                        <a href="https://laravel.com/docs/5.4/mail" target="_blank" title="{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}">
                                            <i class="fa fa-info-circle fa-fw" aria-hidden="true"></i>
                                            <span class="sr-only">{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}</span>
                                        </a>
                                    </sup>
                                </label>
                                <input type="text" name="mail_driver" id="mail_driver" value="smtp" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_driver_placeholder') }}" />
                                @if ($errors->has('mail_driver'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('mail_driver') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('mail_host') ? ' has-error ' : '' }}">
                                <label for="mail_host">{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_host_label') }}</label>
                                <input type="text" name="mail_host" id="mail_host" value="smtp.mailtrap.io" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_host_placeholder') }}" />
                                @if ($errors->has('mail_host'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('mail_host') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('mail_port') ? ' has-error ' : '' }}">
                                <label for="mail_port">{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_port_label') }}</label>
                                <input type="number" name="mail_port" id="mail_port" value="2525" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_port_placeholder') }}" />
                                @if ($errors->has('mail_port'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('mail_port') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('mail_username') ? ' has-error ' : '' }}">
                                <label for="mail_username">{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_username_label') }}</label>
                                <input type="text" name="mail_username" id="mail_username" value="null" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_username_placeholder') }}" />
                                @if ($errors->has('mail_username'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('mail_username') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('mail_password') ? ' has-error ' : '' }}">
                                <label for="mail_password">{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_password_label') }}</label>
                                <input type="text" name="mail_password" id="mail_password" value="null" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_password_placeholder') }}" />
                                @if ($errors->has('mail_password'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('mail_password') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('mail_encryption') ? ' has-error ' : '' }}">
                                <label for="mail_encryption">{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_encryption_label') }}</label>
                                <input type="text" name="mail_encryption" id="mail_encryption" value="null" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_encryption_placeholder') }}" />
                                @if ($errors->has('mail_encryption'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('mail_encryption') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('mail_from_address') ? ' has-error ' : '' }}">
                                <label for="mail_from_address">{{ __('Mail From Address') }}</label>
                                <input type="text" name="mail_from_address" id="mail_from_address" value="null" placeholder="{{ __('Mail From Address') }}" />
                                @if ($errors->has('mail_from_address'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('mail_from_address') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('mail_from_name') ? ' has-error ' : '' }}">
                                <label for="mail_from_name">{{ __('Mail From Name') }}</label>
                                <input type="text" name="mail_from_name" id="mail_from_name" value="" placeholder="{{ __('Mail From Name') }}" />
                                @if ($errors->has('mail_from_name'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('mail_from_name') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="block margin-bottom-2">

                        <div class="info">
                            <div class="form-group {{ $errors->has('pusher_app_id') ? ' has-error ' : '' }}">
                                <label for="pusher_app_id">
                                    {{ trans('installer_messages.environment.wizard.form.app_tabs.pusher_app_id_label') }}
                                    <sup>
                                        <a href="https://pusher.com/docs/server_api_guide" target="_blank" title="{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}">
                                            <i class="fa fa-info-circle fa-fw" aria-hidden="true"></i>
                                            <span class="sr-only">{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}</span>
                                        </a>
                                    </sup>
                                </label>
                                <input type="text" name="pusher_app_id" id="pusher_app_id" value="" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.pusher_app_id_palceholder') }}" />
                                @if ($errors->has('pusher_app_id'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('pusher_app_id') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('pusher_app_key') ? ' has-error ' : '' }}">
                                <label for="pusher_app_key">{{ trans('installer_messages.environment.wizard.form.app_tabs.pusher_app_key_label') }}</label>
                                <input type="text" name="pusher_app_key" id="pusher_app_key" value="" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.pusher_app_key_palceholder') }}" />
                                @if ($errors->has('pusher_app_key'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('pusher_app_key') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('pusher_app_secret') ? ' has-error ' : '' }}">
                                <label for="pusher_app_secret">{{ trans('installer_messages.environment.wizard.form.app_tabs.pusher_app_secret_label') }}</label>
                                <input type="password" name="pusher_app_secret" id="pusher_app_secret" value="" placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.pusher_app_secret_palceholder') }}" />
                                @if ($errors->has('pusher_app_secret'))
                                    <span class="error-block">
                                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                                        {{ $errors->first('pusher_app_secret') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="buttons">
            <button class="btn btn-primary btn-block d-inline-block align-items-center mt-3 py-2" type="submit">
                <span class="d-inline-flex align-items-center mx-auto">
                    {{ __('Install') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron_left' : 'icons.chevron_right'), ['class' => 'icon-chevron fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])
                </span>
            </button>
        </div>
    </form>

@endsection

@include('vendor.installer.menu')