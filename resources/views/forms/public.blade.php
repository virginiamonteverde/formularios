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

    {{-- Errores de validación --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <p class="mb-1"><strong>Hay errores en el formulario:</strong></p>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('forms.public.submit', $form->slug) }}" novalidate>

        @csrf

        {{-- Campos generados dinámicamente desde el schema --}}
        @foreach ($form->schema ?? [] as $field)
            @php
                $name = $field['name'] ?? null;
                $label = $field['label'] ?? $name;
                $required = !empty($field['required']);
                $type = $field['type'] ?? 'text';
                $options = $field['options'] ?? [];
            @endphp

            {{-- Campo de texto --}}
            @if ($type === 'text')
                <div class="mb-3">
                    <label class="form-label">{{ $label }}</label>
                    <input
                        type="text"
                        name="{{ $name }}"
                        class="form-control @error($name) is-invalid @enderror"
                        value="{{ old($name) }}"
                        @if($required) required @endif
                    >
                    @error($name)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            {{-- Campo email --}}
            @if ($type === 'email')
                <div class="mb-3">
                    <label class="form-label">{{ $label }}</label>
                    <input
                        type="email"
                        name="{{ $name }}"
                        class="form-control @error($name) is-invalid @enderror"
                        value="{{ old($name) }}"
                        @if($required) required @endif
                    >
                    @error($name)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            {{-- Textarea --}}
            @if ($type === 'textarea')
                <div class="mb-3">
                    <label class="form-label">{{ $label }}</label>
                    <textarea
                        name="{{ $name }}"
                        class="form-control @error($name) is-invalid @enderror"
                        rows="4"
                        @if($required) required @endif
                    >{{ old($name) }}</textarea>
                    @error($name)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            {{-- Select --}}
            @if ($type === 'select')
                <div class="mb-3">
                    <label class="form-label">{{ $label }}</label>
                    <select
                        name="{{ $name }}"
                        class="form-select @error($name) is-invalid @enderror"
                        @if($required) required @endif
                    >
                        <option value="" disabled selected>Seleccionar...</option>

                        @foreach ($options as $value => $text)
                            <option
                                value="{{ $value }}"
                                @if(old($name) == $value) selected @endif
                            >
                                {{ $text }}
                            </option>
                        @endforeach
                    </select>

                    @error($name)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            {{-- Checkbox --}}
            @if ($type === 'checkbox')
                <div class="form-check mb-3">
                    <input
                        type="checkbox"
                        name="{{ $name }}"
                        value="1"
                        class="form-check-input @error($name) is-invalid @enderror"
                        @if(old($name)) checked @endif
                    >
                    <label class="form-check-label">{{ $label }}</label>

                    @error($name)
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            @endif
        @endforeach


        <button type="submit" class="btn btn-primary">
            Enviar
        </button>
    </form>

</div>
</body>
</html>
