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

                $conditionField = $field['condition_field'] ?? null;
                $conditionValue = $field['condition_value'] ?? null;
            @endphp

            {{-- Contenedor general del campo, con info de condición --}}
            <div
                class="conditional-field mb-3"
                data-condition-field="{{ $conditionField }}"
                data-condition-value="{{ $conditionValue }}"
            >

                {{-- Campo de texto --}}
                @if ($type === 'text')
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
                @endif

                {{-- Campo email --}}
                @if ($type === 'email')
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
                @endif

                {{-- Textarea --}}
                @if ($type === 'textarea')
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
                @endif

                {{-- Select --}}
                @if ($type === 'select')
                    <label class="form-label">{{ $label }}</label>
                    <select
                        name="{{ $name }}"
                        class="form-select @error($name) is-invalid @enderror"
                        @if($required) required @endif
                    >
                        <option value="" disabled @if(!old($name)) selected @endif>Seleccionar...</option>

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
                @endif

                {{-- Checkbox simple (sí/no) --}}
                @if ($type === 'checkbox')
                    <div class="form-check">
                        <input
                            type="checkbox"
                            name="{{ $name }}"
                            value="1"
                            class="form-check-input @error($name) is-invalid @enderror"
                            @if(old($name)) checked @endif
                            @if($required) required @endif
                        >
                        <label class="form-check-label">{{ $label }}</label>
                        @error($name)
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                {{-- Grupo de checkboxes (múltiple selección) --}}
                @if ($type === 'checkbox_group')
                    @php
                        $oldValues = old($name, []);
                        if (!is_array($oldValues)) {
                            $oldValues = [];
                        }
                    @endphp

                    <p class="form-label mb-1">{{ $label }}</p>

                    @foreach ($options as $value => $text)
                        <div class="form-check">
                            <input
                                type="checkbox"
                                name="{{ $name }}[]"
                                value="{{ $value }}"
                                class="form-check-input @error($name) is-invalid @enderror"
                                @if(in_array($value, $oldValues)) checked @endif
                            >
                            <label class="form-check-label">
                                {{ $text }}
                            </label>
                        </div>
                    @endforeach

                    @error($name)
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                @endif

                {{-- Radio buttons --}}
                @if ($type === 'radio')
                    <p class="form-label mb-1">{{ $label }}</p>

                    @foreach ($options as $value => $text)
                        <div class="form-check">
                            <input
                                type="radio"
                                name="{{ $name }}"
                                value="{{ $value }}"
                                class="form-check-input @error($name) is-invalid @enderror"
                                @if(old($name) == $value) checked @endif
                                @if($required && $loop->first) required @endif
                            >
                            <label class="form-check-label">
                                {{ $text }}
                            </label>
                        </div>
                    @endforeach

                    @error($name)
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                @endif

            </div>
        @endforeach

        <button type="submit" class="btn btn-primary">
            Enviar
        </button>
    </form>

</div>

{{-- JS para manejar campos condicionales en vivo --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const blocks = document.querySelectorAll('.conditional-field');

    function getFieldValue(fieldName) {
        if (!fieldName) return null;

        // checkbox_group: name="campo[]"
        const group = document.querySelectorAll(`[name="${fieldName}[]"]`);
        if (group.length) {
            const values = [];
            group.forEach(cb => {
                if (cb.checked) values.push(cb.value);
            });
            return values;
        }

        // radio
        const radioChecked = document.querySelector(`[name="${fieldName}"]:checked`);
        if (radioChecked) {
            return radioChecked.value;
        }

        // checkbox simple
        const checkbox = document.querySelector(`input[type="checkbox"][name="${fieldName}"]`);
        if (checkbox) {
            return checkbox.checked ? '1' : '0';
        }

        // text / email / select, etc.
        const input = document.querySelector(`[name="${fieldName}"]`);
        if (input) {
            return input.value;
        }

        return null;
    }

    function updateConditions() {
        blocks.forEach(block => {
            const field = block.dataset.conditionField;
            const value = block.dataset.conditionValue;

            // Si no tiene condición, siempre se muestra
            if (!field || !value) {
                block.style.display = '';
                return;
            }

            const current = getFieldValue(field);
            let shouldShow = false;

            if (Array.isArray(current)) {
                shouldShow = current.includes(value);
            } else {
                shouldShow = (current == value);
            }

            block.style.display = shouldShow ? '' : 'none';
        });
    }

    // Actualizar al cargar (para respetar old())
    updateConditions();

    // Y cada vez que cambia algo dentro del formulario
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('change', function (event) {
            updateConditions();
        });
    }
});
</script>

</body>
</html>
