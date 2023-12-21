<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class EmbeddingsDataCommand extends Command
{
    protected $signature = 'embeddings:data';

    protected $description = 'Command description';

    public function handle(): void
    {
        foreach (Article::all() as $article) {
            $response = Http::withHeaders(['Authorization' => 'Bearer ' . config('openai.key')])
                ->post('https://api.openai.com/v1/embeddings', [
                    "model" => "text-embedding-ada-002",
                    "input" => $article->title . "\n" . $article->summary,
                ]);

            $article->embedding()->create([
                'data' => $response['data'],
                'model' => $response['model'],
                'object' => $response['object'],
                'usage' => $response['usage'],
            ]);
        }
    }
}
