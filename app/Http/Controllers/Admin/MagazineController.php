<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MagazineRequest;
use App\Models\Magazine;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;

class MagazineController extends Controller
{
    public function index()
    {
        return view('admin.magazines.index', ['magazines' => Magazine::latest()->paginate(20)]);
    }

    public function create()
    {
        return view('admin.magazines.create');
    }

    public function store(MagazineRequest $request)
    {
        $magazine = Magazine::create($this->data($request));

        return redirect()->route('admin.magazines.edit', $magazine)->with('success', __('Magazine created.'));
    }

    public function edit(Magazine $magazine)
    {
        return view('admin.magazines.edit', compact('magazine'));
    }

    public function update(MagazineRequest $request, Magazine $magazine)
    {
        $magazine->update($this->data($request));

        return back()->with('success', __('Magazine updated.'));
    }

    public function destroy(Magazine $magazine)
    {
        $magazine->delete();

        return redirect()->route('admin.magazines.index')->with('success', __('Magazine deleted.'));
    }

    private function data(MagazineRequest $request): array
    {
        $data = $request->validated();
        unset($data['cover_upload'], $data['pdf_upload']);

        if ($request->file('cover_upload') instanceof UploadedFile) {
            $data['cover_image'] = $this->storeCoverUpload($request->file('cover_upload'));
        }

        if ($request->file('pdf_upload') instanceof UploadedFile) {
            $data['pdf_path'] = $request->file('pdf_upload')->store('magazines', 'local');
        }

        return $data;
    }

    private function storeCoverUpload(UploadedFile $file): string
    {
        $directory = $this->projectRoot().DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'magazines';

        File::ensureDirectoryExists($directory, 0755, true);

        $filename = $file->hashName();
        $file->move($directory, $filename);

        return 'storage/uploads/magazines/'.$filename;
    }

    private function projectRoot(): string
    {
        return dirname(__DIR__, 4);
    }
}
