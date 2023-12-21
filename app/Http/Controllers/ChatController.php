<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function __invoke(Request $request)
    {
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . config('openai.key')])
            ->post('https://api.openai.com/v1/chat/completions', [
                "model" => "gpt-3.5-turbo",
                "messages" => [
                    [
                        "role" => "system",
                        "content" => "Tu es un chatbot"
                    ],
                    [
                        "role" => "user",
                        "content" => $request->input('message')
                    ]
                ],
            ]);
        $chatResponse = $response->json()['choices'][0]['message']['content'];
        return response()->json(['message' => $chatResponse]);
    }
}
