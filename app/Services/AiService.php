<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    protected string $host;
    protected string $model;

    public function __construct()
    {
        $this->host  = config('services.ollama.host', 'http://localhost:11434');
        $this->model = config('services.ollama.model', 'llama3');
    }

    /**
     * Send a chat message to Ollama and get a streamed or full response.
     *
     * @param  array  $messages  [['role' => 'user'|'assistant'|'system', 'content' => '...']]
     * @return string  The AI response text
     */
    public function chat(array $messages): string
    {
        try {
            $response = Http::timeout(120)
                ->post("{$this->host}/api/chat", [
                    'model'    => $this->model,
                    'messages' => $messages,
                    'stream'   => false,
                ]);

            if ($response->failed()) {
                Log::error('Ollama chat failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                throw new \RuntimeException('AI service unavailable. Please try again later.');
            }

            return $response->json('message.content', '');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Ollama connection failed', ['error' => $e->getMessage()]);
            throw new \RuntimeException('Could not connect to AI service. Is Ollama running?');
        }
    }

    /**
     * Simple generate (non-chat) endpoint.
     */
    public function generate(string $prompt): string
    {
        try {
            $response = Http::timeout(120)
                ->post("{$this->host}/api/generate", [
                    'model'  => $this->model,
                    'prompt' => $prompt,
                    'stream' => false,
                ]);

            if ($response->failed()) {
                throw new \RuntimeException('AI service unavailable.');
            }

            return $response->json('response', '');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \RuntimeException('Could not connect to AI service. Is Ollama running?');
        }
    }

    public function getModel(): string
    {
        return $this->model;
    }
}
