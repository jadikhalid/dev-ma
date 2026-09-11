<?php

namespace Database\Seeders;

use App\Services\Library\LibraryCategoryService;
use Illuminate\Database\Seeder;

class LibraryCategorySeeder extends Seeder
{
    public function run(): void
    {
        $tree = require __DIR__.'/data/library_categories.php';

        app(LibraryCategoryService::class)->syncTree($tree);
    }
}
