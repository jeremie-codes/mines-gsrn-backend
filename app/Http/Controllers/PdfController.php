<?php

namespace App\Http\Controllers;

  use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Member;
use Illuminate\Http\Request;

class PdfController extends Controller
{
//     use Barryvdh\DomPDF\Facade\Pdf;
// use App\Models\CartePosition;
// use App\Models\Membre;

    public function generatePDF(Request $request)
    {
        $member = Member::findOrFail($request->member_id);

        // Récupérer ou stocker les positions et dimensions
        $positions = [];

        foreach (['nom', 'postnom', 'prenom', 'fonction', 'categorie', 'site', 'numero', 'photo'] as $field) {
            $positions[$field] = [
                'top' => $request->input($field . '_top'),
                'left' => $request->input($field . '_left')
            ];
        }

        // Récupérer les dimensions de l'image photo
        $photoWidth = $request->input('photo_width', 310);
        $photoHeight = $request->input('photo_height', 430);

        return Pdf::loadView('carte.pdf', [
            'member' => $member,
            'positions' => $positions,
            'photoWidth' => $photoWidth,
            'photoHeight' => $photoHeight
        ])->setPaper('a4', 'landscape') 
        // ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
        ->stream("carte-{$member->membershipNumber}.pdf");
        
        // ->setPaper('a4', 'landscape') // <--- Ici le mode paysage est défini
        // ->stream("carte-{$member->membershipNumber}.pdf");

    }
    

    public function generateFromPreview(Request $request)
    {
        $member = Member::findOrFail($request->member_id);

        $positions = $request->except(['_token', 'member_id']);

        return PDF::loadView('carte.pdf', compact('member', 'positions'))
            ->setPaper([0, 0, 1012, 638], 'landscape')
            ->download('carte_membre_' . $member->numero . '.pdf');
    }


    public function previewCarte($id)
    {
        $member = Member::findOrFail($id);

        return view('carte.preview', compact('member'));
    }


}
