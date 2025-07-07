<?php

namespace App\Http\Controllers;

  use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Member;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function generateCarte($id)
    {
        $member = Member::findOrFail($id);

        $pdf = Pdf::loadView('carte.pdf', compact('member'));

        // Dimensions précises : 1012 x 638 pixels (à 96 DPI ≈ 10.5x6.6 pouces)
        $pdf->setPaper([0, 0, 1012, 638], 'landscape'); // Custom size, no margin

        return $pdf->download('carte_membre_'.$member->numero.'.pdf');
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
