<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\Request;

class PublicFormController extends Controller
{
    // Mostrar formulario público
    public function show($slug)
    {
        $form = Form::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('forms.public', compact('form'));
    }

    // Recibir envío del formulario
    public function submit(Request $request, $slug)
    {
        $form = Form::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $schema = $form->schema ?? [];
        $rules = [];

        if (is_array($schema)) {
            foreach ($schema as $field) {
                if (empty($field['name'])) {
                    continue;
                }

                $name = $field['name'];
                $required = !empty($field['required']);
                $type = $field['type'] ?? 'text';

                // --- CONDICIÓN: si este campo depende de otro, vemos si se cumple ---
                $conditionField = $field['condition_field'] ?? null;
                $conditionValue = $field['condition_value'] ?? null;
                $shouldValidate = true;

                if ($conditionField && $conditionValue) {
                    $currentValue = $request->input($conditionField);

                    if (is_array($currentValue)) {
                        // checkbox_group como campo controlador
                        $shouldValidate = in_array($conditionValue, $currentValue);
                    } else {
                        $shouldValidate = ($currentValue == $conditionValue);
                    }
                }

                // Si la condición NO se cumple, no agregamos reglas ni validamos este campo
                if (! $shouldValidate) {
                    continue;
                }

                // --- Reglas según tipo de campo ---
                // checkbox_group se valida como array de strings
                if ($type === 'checkbox_group') {
                    $baseRule = $required ? 'required' : 'nullable';

                    // El grupo completo
                    $rules[$name] = $baseRule . '|array';

                    // Cada valor dentro del grupo
                    $rules[$name . '.*'] = 'string';

                    continue;
                }

                $ruleParts = [];

                if ($required) {
                    $ruleParts[] = 'required';
                } else {
                    $ruleParts[] = 'nullable';
                }

                switch ($type) {
                    case 'email':
                        $ruleParts[] = 'email';
                        $ruleParts[] = 'max:255';
                        break;

                    case 'select':
                    case 'radio':
                        $ruleParts[] = 'string';
                        $ruleParts[] = 'max:255';
                        break;

                    case 'checkbox':
                        $ruleParts[] = 'boolean';
                        break;

                    case 'textarea':
                    case 'text':
                    default:
                        $ruleParts[] = 'string';
                        break;
                }

                $rules[$name] = implode('|', $ruleParts);
            }
        }

        // Validamos solo los campos que tienen reglas (es decir: visibles según condición)
        if (!empty($rules)) {
            $data = $request->validate($rules);
        } else {
            $data = $request->except('_token');
        }

        FormSubmission::create([
            'form_id'     => $form->id,
            'data'        => $data,
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        return back()->with('success', '¡Formulario enviado correctamente!');
    }
}
