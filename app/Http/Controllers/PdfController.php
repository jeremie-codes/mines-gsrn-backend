<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Member;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PdfController extends Controller
{
    public function generatePDF(Request $request)
    {
        $member = Member::findOrFail($request->member_id);

        $positions = [];
        foreach (['nom', 'postnom', 'prenom', 'fonction', 'categorie', 'site', 'numero', 'photo'] as $field) {
            $positions[$field] = [
                'top' => $request->input($field . '_top'),
                'left' => $request->input($field . '_left')
            ];
        }

        $photoWidth = $request->input('photo_width');
        $photoHeight = $request->input('photo_height');

        // ✅ Génération du QR Code en SVG encodé en base64
        $qrContent = "Coopefemac - {$member->membershipNumber} - {$member->firstname} {$member->lastname}";
        // $qrCodeSvg = QrCode::format('png')->size(165)->generate($qrContent);
        // $qrCodeBase64 = base64_encode($qrCodeSvg);

        // ✅ Pas besoin de Intervention/Image ni stockage

        return Pdf::loadView('carte.pdf', [
            'member' => $member,
            'positions' => $positions,
            'photoWidth' => $photoWidth,
            'photoHeight' => $photoHeight,
            'qrContent' => $qrContent
        ])->setPaper([0, 0, 1012, 638]) // dimensions exactes en points
          ->stream("carte-{$member->membershipNumber}.pdf");
    }    

    public function previewCarte($id)
    {
        $member = Member::findOrFail($id);
        $qrContent = "Coopefemac: \n{$member->membershipNumber}\n{$member->firstname} {$member->lastname}";
        $qrCodeSvg = QrCode::format('svg')->size(165)->generate($qrContent);

        return view('carte.preview', compact('member', 'qrCodeSvg'));
    }


}
