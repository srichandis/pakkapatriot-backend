<?php

namespace App\Http\Controllers;

use App\Data\LegalDocs;
use Illuminate\View\View;

class LegalController extends Controller
{
    public function privacy(): View
    {
        return $this->render('privacy');
    }

    public function terms(): View
    {
        return $this->render('terms');
    }

    /**
     * Render one of the static legal documents.
     */
    protected function render(string $doc): View
    {
        $document = LegalDocs::get($doc);

        abort_if($document === null, 404);

        return view('legal.show', ['doc' => $document]);
    }
}
