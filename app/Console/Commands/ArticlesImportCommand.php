<?php

namespace App\Console\Commands;

use App\Actions\GetGbData;
use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ArticlesImportCommand extends Command
{
    protected $signature = 'articles:import';

    protected $description = 'Command description';

    public function handle(): void
    {
        $articles = GetGbData::run(40);
        foreach ($articles as $article) {
            if ($article['summary'] !== "") {
                Article::create([
                    'type' => $article['type'],
                    'title' => $article['title'],
                    'summary' => $article['summary'],
                ]);
            }
        }
    }
}
