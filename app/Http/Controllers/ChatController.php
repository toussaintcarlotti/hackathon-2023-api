<?php

namespace App\Http\Controllers;

use App\Actions\GetBestArticles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'conversation' => $request->session()->get('conversation'),
        ]);
    }

    public function store(Request $request)
    {
        // use session to store the conversation
        if (!$request->session()->has('conversation')) {
            $request->session()->put('conversation', []);
        }

        $articles = GetBestArticles::run($request->input('message'));


        $request->session()->push('conversation', [
            'role' => 'user',
            'content' => $request->input('message'),
        ]);
        $fullPrompt = "Tu es un agent conversationnel qui répond a des question à l'aide de ces informations (réponse rapide):";
        foreach ($articles as $article) {
            $fullPrompt .= "\n\n" . $article->title . "\n" . $article->summary;
        }


        $response = Http::timeout(-1)->withHeaders(['Authorization' => 'Bearer ' . config('openai.key')])
            ->post('https://api.openai.com/v1/chat/completions', [
                "model" => "gpt-3.5-turbo-1106",
                "messages" => array_merge([
                    [
                        'role' => 'system',
                        'content' => $fullPrompt,
                    ]
                ], $request->session()->get('conversation')),
            ]);

        $request->session()->push('conversation', [
            'role' => 'assistant',
            'content' => $response['choices'][0]['message']['content'],
        ]);

        return response()->json([
            'conversation' => $request->session()->get('conversation'),
        ]);
    }
}
