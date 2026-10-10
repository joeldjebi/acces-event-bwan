@extends('layouts.public', [
    'title' => $page->seo_title ?: $page->name,
    'metaDescription' => $page->seo_description ?: $page->summary,
    'canonicalUrl' => route('contact'),
    'bodyClass' => 'page-clean',
])

@section('content')
    {{-- En-tête centré --}}
    <section class="page-intro">
        <div class="page-intro-copy hero-copy">
            <p class="kicker">{{ $page->eyebrow }}</p>
            <h1>{{ $page->heading }}</h1>
            <p class="sol-lead">{{ $page->summary }}</p>
        </div>
    </section>

    <section class="section-clean contact-layout">
        <form method="POST" action="{{ route('demo-requests.store') }}" class="public-form contact-form" aria-label="Formulaire de démonstration" data-reveal>
            <div class="contact-form-head">
                <h2>{{ site_text('contact.form_heading') }}</h2>
                <p>{{ site_text('contact.form_note') }}</p>
            </div>

            @csrf

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="honeypot-field" aria-hidden="true">
                <label for="website">Site web</label>
                <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label for="full_name">{{ site_text('contact.label_name') }} <span class="required" aria-hidden="true">*</span></label>
                    <input id="full_name" name="full_name" class="form-input" value="{{ old('full_name') }}" placeholder="{{ site_text('contact.placeholder_name') }}" required>
                    @error('full_name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field">
                    <label for="company">{{ site_text('contact.label_company') }}</label>
                    <input id="company" name="company" class="form-input" value="{{ old('company') }}" placeholder="{{ site_text('contact.placeholder_company') }}">
                    @error('company') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label for="email">{{ site_text('contact.label_email') }} <span class="required" aria-hidden="true">*</span></label>
                    <input id="email" name="email" type="email" class="form-input" value="{{ old('email') }}" placeholder="{{ site_text('contact.placeholder_email') }}" required>
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field">
                    <label for="phone">{{ site_text('contact.label_phone') }}</label>
                    <input id="phone" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="{{ site_text('contact.placeholder_phone') }}">
                    @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label for="event_type">{{ site_text('contact.label_event') }}</label>
                    <input id="event_type" name="event_type" class="form-input" value="{{ old('event_type') }}" placeholder="{{ site_text('contact.placeholder_event') }}">
                    @error('event_type') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field">
                    <label for="selected_solution">{{ site_text('contact.label_solution') }}</label>
                    <select id="selected_solution" name="selected_solution" class="form-input">
                        <option value="">{{ site_text('contact.option_solution') }}</option>
                        @foreach ($solutions as $solution)
                            <option value="{{ $solution->title }}" @selected(old('selected_solution') === $solution->title)>{{ $solution->title }}</option>
                        @endforeach
                    </select>
                    @error('selected_solution') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-field">
                <label for="message">{{ site_text('contact.label_message') }} <span class="required" aria-hidden="true">*</span></label>
                <textarea id="message" name="message" class="form-input form-textarea" rows="6" placeholder="{{ site_text('contact.placeholder_message') }}" required>{{ old('message') }}</textarea>
                @error('message') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn btn-primary contact-submit">{{ $page->cta_label ?: 'Envoyer la demande' }}</button>
        </form>

        <aside class="contact-aside" data-reveal style="--reveal-delay: 120ms">
            <div class="contact-methods">
                @if ($footerSettings['footer_whatsapp'])
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $footerSettings['footer_whatsapp']) }}" target="_blank" rel="noopener" class="contact-method">
                        <x-icon name="chat" />
                        <span>{{ site_text('contact.method_whatsapp') }}</span>
                        <strong>{{ $footerSettings['footer_whatsapp'] }}</strong>
                    </a>
                @endif
                @if ($footerSettings['footer_phone'])
                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $footerSettings['footer_phone']) }}" class="contact-method">
                        <x-icon name="phone" />
                        <span>{{ site_text('contact.method_phone') }}</span>
                        <strong>{{ $footerSettings['footer_phone'] }}</strong>
                    </a>
                @endif
                @if ($footerSettings['footer_email'])
                    <a href="mailto:{{ $footerSettings['footer_email'] }}" class="contact-method">
                        <x-icon name="mail" />
                        <span>{{ site_text('contact.method_email') }}</span>
                        <strong>{{ $footerSettings['footer_email'] }}</strong>
                    </a>
                @endif
                @if ($footerSettings['footer_address'])
                    <div class="contact-method">
                        <x-icon name="pin" />
                        <span>{{ site_text('contact.method_address') }}</span>
                        <strong>{{ $footerSettings['footer_address'] }}</strong>
                    </div>
                @endif
                @if ($footerSettings['footer_hours'])
                    <div class="contact-method">
                        <x-icon name="clock" />
                        <span>{{ site_text('contact.method_hours') }}</span>
                        <strong>{{ $footerSettings['footer_hours'] }}</strong>
                    </div>
                @endif
            </div>

            <div class="contact-steps">
                <p class="sol-aside-title">{{ site_text('contact.steps_title') }}</p>
                <ol>
                    <li>
                        <span>01</span>
                        <div>
                            <strong>{{ site_text('contact.step_1_title') }}</strong>
                            <p>{{ site_text('contact.step_1_text') }}</p>
                        </div>
                    </li>
                    <li>
                        <span>02</span>
                        <div>
                            <strong>{{ site_text('contact.step_2_title') }}</strong>
                            <p>{{ site_text('contact.step_2_text') }}</p>
                        </div>
                    </li>
                    <li>
                        <span>03</span>
                        <div>
                            <strong>{{ site_text('contact.step_3_title') }}</strong>
                            <p>{{ site_text('contact.step_3_text') }}</p>
                        </div>
                    </li>
                </ol>
            </div>
        </aside>
    </section>
@endsection
