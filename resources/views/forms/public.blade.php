<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $form->title }} | Formularios</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap opcional para que se vea lindo desde ya --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">

    <h1 class="mb-3">{{ $form->title }}</h1>

    @if($form->description)
        <p class="text-muted">{{ $form->description }}</p>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- ⚠️ Por ahora ponemos campos fijos de ejemplo
         Más adelante los vamos a generar dinámicamente desde $form->schema --}}
    <form method="POST" action="{{ route('forms.public.submit', $form->slug) }}">
        @csrf

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input
                type="text"
                id="nombre"
                name="nombre"
                class="form-control"
                required
            >
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-control"
                required
            >
        </div>

        <div class="mb-3">
            <label for="mensaje" class="form-label">Mensaje</label>
            <textarea
                id="mensaje"
                name="mensaje"
                class="form-control"
                rows="4"
            ></textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            Enviar
        </button>
    </form>

</div>
</body>
</html>
