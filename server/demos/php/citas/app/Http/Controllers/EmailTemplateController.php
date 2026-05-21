<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::all();
        return view('settings.email_templates.index', compact('templates'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $emailTemplate->update([
            'subject' => $request->subject,
            'body' => $request->body,
        ]);

        return redirect()->route('settings.email_templates.index')
            ->with('success', 'Plantilla actualizada correctamente.');
    }
}
