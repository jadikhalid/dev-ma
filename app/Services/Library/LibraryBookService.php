<?php

namespace App\Services\Library;

use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use App\Models\User;
use App\Support\LibraryBookStorage;
use App\Support\LibraryCoverStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class LibraryBookService
{
    /**
     * @param  array{library_category_id:int,title:string,author?:string|null,description?:string|null,is_published?:bool}  $data
     */
    public function store(array $data, UploadedFile $file, UploadedFile $cover, User $uploader): LibraryBook
    {
        $category = LibraryCategory::query()->findOrFail($data['library_category_id']);
        $this->assertLeafCategory($category);

        $path = LibraryBookStorage::storeUpload($file);
        $coverPath = LibraryCoverStorage::storeUpload($cover);

        return LibraryBook::query()->create([
            'library_category_id' => $category->id,
            'title' => $data['title'],
            'author' => $data['author'] ?? null,
            'description' => $data['description'] ?? null,
            'cover_path' => $coverPath,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType() ?: 'application/pdf',
            'size_bytes' => (int) $file->getSize(),
            'download_count' => 0,
            'is_published' => (bool) ($data['is_published'] ?? true),
            'uploaded_by' => $uploader->id,
        ]);
    }

    /**
     * @param  array{library_category_id:int,title:string,author?:string|null,description?:string|null,is_published?:bool}  $data
     */
    public function update(LibraryBook $book, array $data, ?UploadedFile $file = null, ?UploadedFile $cover = null): LibraryBook
    {
        $category = LibraryCategory::query()->findOrFail($data['library_category_id']);
        $this->assertLeafCategory($category);

        if ($file) {
            LibraryBookStorage::delete($book->file_path);
            $book->file_path = LibraryBookStorage::storeUpload($file);
            $book->original_filename = $file->getClientOriginalName();
            $book->mime = $file->getMimeType() ?: 'application/pdf';
            $book->size_bytes = (int) $file->getSize();
        }

        if ($cover) {
            LibraryCoverStorage::delete($book->cover_path);
            $book->cover_path = LibraryCoverStorage::storeUpload($cover);
        }

        $book->fill([
            'library_category_id' => $category->id,
            'title' => $data['title'],
            'author' => $data['author'] ?? null,
            'description' => $data['description'] ?? null,
            'is_published' => (bool) ($data['is_published'] ?? $book->is_published),
        ])->save();

        return $book->refresh();
    }

    public function delete(LibraryBook $book): void
    {
        $book->delete();
    }

    public function incrementDownload(LibraryBook $book): LibraryBook
    {
        $book->increment('download_count');

        return $book->refresh();
    }

    private function assertLeafCategory(LibraryCategory $category): void
    {
        if (! $category->isLeaf()) {
            throw ValidationException::withMessages([
                'library_category_id' => [__('talenma.admin.library.book_requires_leaf')],
            ]);
        }
    }
}
