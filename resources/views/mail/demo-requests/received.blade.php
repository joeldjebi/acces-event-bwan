<h1>Nouvelle demande de démonstration</h1>

<p><strong>Nom :</strong> {{ $demoRequest->full_name }}</p>
<p><strong>Entreprise :</strong> {{ $demoRequest->company ?: 'Non renseignée' }}</p>
<p><strong>Email :</strong> {{ $demoRequest->email }}</p>
<p><strong>Téléphone :</strong> {{ $demoRequest->phone ?: 'Non renseigné' }}</p>
<p><strong>Type d'événement :</strong> {{ $demoRequest->event_type ?: 'Non renseigné' }}</p>
<p><strong>Solution souhaitée :</strong> {{ $demoRequest->selected_solution ?: 'Non renseignée' }}</p>

<p><strong>Message :</strong></p>
<p>{{ $demoRequest->message }}</p>
