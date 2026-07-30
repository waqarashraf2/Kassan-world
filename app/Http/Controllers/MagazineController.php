<?php

namespace App\Http\Controllers;

use App\Models\Magazine;
use App\Services\MagazineAccessService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class MagazineController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return view('magazines.index', [
            'magazines' => Magazine::active()->latest('issue_date')->paginate(12),
        ]);
    }

    public function show(Magazine $magazine, MagazineAccessService $access)
    {
        abort_unless($magazine->is_active, 404);

        $purchase = auth()->user()?->magazinePurchases()
            ->where('magazine_id', $magazine->id)
            ->latest()
            ->first();
        $canAccess = $access->canAccess(auth()->user(), $magazine);

        return view('magazines.show', compact('magazine', 'purchase', 'canAccess'));
    }

    public function read(Magazine $magazine)
    {
        $this->authorize('viewPdf', $magazine);
        abort_unless(Storage::disk('local')->exists($magazine->pdf_path), 404);

        return Storage::disk('local')->response($magazine->pdf_path, null, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function download(Magazine $magazine)
    {
        $this->authorize('download', $magazine);
        abort_unless(Storage::disk('local')->exists($magazine->pdf_path), 404);

        return Storage::disk('local')->download($magazine->pdf_path, $magazine->slug.'.pdf');
    }
}
