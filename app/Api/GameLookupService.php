<?php

namespace App\Api;

use App\Models\GameData;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GameLookupService
{
    private $access_token = '';
    const CACHE_KEY = 'game_lookup_acccess_token';
    const API_URL = 'https://api.igdb.com/v4/';
    const AUTH_URL = 'https://id.twitch.tv/oauth2/token';
    const GENERIC_ERROR_MSG = "Error loading Game Data";
    const EMPTY_ARRAY_MSG = "Non Array response from getGameData API call";

    public function __construct()
    {
        $this->access_token = $this->getAccessToken();
    }

    private function getAccessToken(): string
    {
        if ($this->access_token = Cache::get(self::CACHE_KEY)) {
            return $this->access_token;
        }

        try {
            $data = [
                'client_id' => config('igdb.client_id'),
                'client_secret' => config('igdb.secret_id'),
                'grant_type' => 'client_credentials'

            ];

            $r = Http::post(self::AUTH_URL, $data)->throw()->json();
            if (is_array($r) && !empty($r['access_token'])) {
                Cache::put(self::CACHE_KEY, $r['access_token'], (int) $r['expires_in'] - 120);
                $this->access_token = $r['access_token'];
            }
        } catch (Exception $e) {
            Log::error(sprintf("Error optaining key: %s", $e->getMessage()));
        }

        return $this->access_token ?? '';
    }

    private function apiRequest(string $endpoint, string $body): array
    {
        try {
            $r = Http::withHeader('Client-ID', config('igdb.client_id'))
                ->withToken($this->access_token)
                ->withBody($body)
                ->acceptJson()
                ->post(self::API_URL . $endpoint)
                ->throw()
                ->json();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            if (defined("DEBUG_ERRORS")) {
                throw new Exception($e->getMessage());
            } else {
                throw new Exception(self::GENERIC_ERROR_MSG);
            }
        }

        if (!is_array($r)) {
            Log::error(self::EMPTY_ARRAY_MSG);
            throw new Exception(self::EMPTY_ARRAY_MSG);
        }

        return $r;
    }

    public function getGameData(string $search): array
    {
        $body = sprintf('search "%s";fields *, platforms.*, cover.*; where version_parent = null;', $search);
        $games = [];
        $r = $this->apiRequest('games',  $body);
        if (!is_array($r)) {
            return $games;
        }

        foreach ($r as $data) {
            $games[] = new GameData($data);
        }

        return $games;
    }
}
