<?php

namespace App\Actions;

use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class GetBestArticles
{
    use AsAction;

    public function handle($message): Collection
    {
        $questionEmbedding = Http::withHeaders(['Authorization' => 'Bearer ' . config('openai.key')])
            ->post('https://api.openai.com/v1/embeddings', [
                "model" => "text-embedding-ada-002",
                "input" => $message,
            ])['data'];

        $articles = Article::all();

        $articleSimilarities = [];
        foreach ($articles as $article) {
            $articleEmbedding = $article->embedding->data;

            $similarity = $this->calculateCosineSimilarity($questionEmbedding[0]['embedding'], $articleEmbedding[0]['embedding']);

            $articleSimilarities[$article->id] = $similarity;
        }

        arsort($articleSimilarities);

        $topArticles = array_keys($articleSimilarities);

        // get the top 10 articles
        $topArticles = array_slice($topArticles, 0, 4);

        return Article::whereIn('id', $topArticles)->get();
    }

    function calculateCosineSimilarity($vector1, $vector2) {
        // Assurez-vous que les vecteurs ont la même longueur.
        if (count($vector1) !== count($vector2)) {
            throw new Exception("Les vecteurs doivent avoir la même longueur");
        }

        // Calcul du produit scalaire.
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;

        foreach ($vector1 as $index => $value) {
            $dotProduct += $value * $vector2[$index];
            $magnitude1 += $value * $value;
            $magnitude2 += $vector2[$index] * $vector2[$index];
        }

        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);

        // Éviter une division par zéro.
        if ($magnitude1 * $magnitude2 == 0) {
            return 0;
        }

        // Calcul de la similarité cosinus.
        $similarity = $dotProduct / ($magnitude1 * $magnitude2);

        return $similarity;
    }
}
