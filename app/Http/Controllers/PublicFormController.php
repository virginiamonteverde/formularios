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

        // Por ahora guardamos TODO lo enviado
        // (más adelante validamos dinámicamente según el schema)
        FormSubmission::create([
            'form_id' => $form->id,
            'data' => $request->except('_token'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', '¡Formulario enviado correctamente!');
    }
}
