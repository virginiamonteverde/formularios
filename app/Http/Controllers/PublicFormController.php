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

        // Construimos reglas de validación dinámicamente según el schema
        if (is_array($schema)) {
            foreach ($schema as $field) {
                if (empty($field['name'])) {
                    continue;
                }

                $fieldName = $field['name'];

                $ruleParts = [];

                // required / nullable según el schema
                if (!empty($field['required'])) {
                    $ruleParts[] = 'required';
                } else {
                    $ruleParts[] = 'nullable';
                }

                // Tipo de campo
                $type = $field['type'] ?? 'text';

                switch ($type) {
                    case 'email':
                        $ruleParts[] = 'email';
                        $ruleParts[] = 'max:255';
                        break;

                    case 'textarea':
                    case 'text':
                    default:
                        $ruleParts[] = 'string';
                        break;
                }

                $rules[$fieldName] = implode('|', $ruleParts);
            }
        }

        // Si tenemos reglas, validamos; si no, aceptamos todo (menos _token)
        if (!empty($rules)) {
            $data = $request->validate($rules);
        } else {
            $data = $request->except('_token');
        }

        // Guardamos el envío
        FormSubmission::create([
            'form_id'     => $form->id,
            'data'        => $data,
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        return back()->with('success', '¡Formulario enviado correctamente!');
    }
}
