@extends('layouts.admin', [
    'title' => 'Demande de '.$demoRequest->full_name,
    'pageHeading' => 'Demande de démonstration',
    'moduleLabel' => 'Demandes de démonstration',
])

@section('content')
    <section class="admin-card">
        @if (session('status'))
            <div class="alert alert-success mb-6">{{ session('status') }}</div>
        @endif

        <div class="request-detail">
            <article>
                <p class="eyebrow">Contact</p>
                <h2>{{ $demoRequest->full_name }}</h2>
                <dl class="detail-list">
                    <div>
                        <dt>Email</dt>
                        <dd><a href="mailto:{{ $demoRequest->email }}">{{ $demoRequest->email }}</a></dd>
                    </div>
                    <div>
                        <dt>Téléphone</dt>
                        <dd>{{ $demoRequest->phone ?: 'Non renseigné' }}</dd>
                    </div>
                    <div>
                        <dt>Entreprise</dt>
                        <dd>{{ $demoRequest->company ?: 'Non renseignée' }}</dd>
                    </div>
                    <div>
                        <dt>Type d'événement</dt>
                        <dd>{{ $demoRequest->event_type ?: 'Non renseigné' }}</dd>
                    </div>
                    <div>
                        <dt>Solution souhaitée</dt>
                        <dd>{{ $demoRequest->selected_solution ?: 'À préciser' }}</dd>
                    </div>
                </dl>

                <div class="message-box">
                    <p class="eyebrow">Message</p>
                    <p>{{ $demoRequest->message }}</p>
                </div>
            </article>

            <aside>
                <form method="POST" action="{{ route('admin.demo-requests.update', $demoRequest) }}" class="admin-form">
                    @csrf
                    @method('PUT')

                    <div class="form-field">
                        <label for="status">Statut</label>
                        <select id="status" name="status" class="form-input">
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $demoRequest->status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-field">
                        <label for="internal_notes">Notes internes</label>
                        <textarea id="internal_notes" name="internal_notes" class="form-input form-textarea" rows="8">{{ old('internal_notes', $demoRequest->internal_notes) }}</textarea>
                        @error('internal_notes') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    <a href="{{ route('admin.demo-requests.index') }}" class="btn btn-secondary">Retour aux demandes</a>
                </form>
            </aside>
        </div>
    </section>
@endsection
