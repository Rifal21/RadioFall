<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\SongRequest;

class RadioController extends Controller
{
    public function index()
    {
        // Fetch external songs
        try {
            $response = \Illuminate\Support\Facades\Http::get('https://radio.fkstudio.my.id/api/station/radio_fkstudio/requests');
            $externalSongs = $response->successful() ? $response->json() : [];

            // Map to a usable format
            $songs = collect($externalSongs)->map(function ($item) {
                return [
                    'id' => $item['song']['id'], // External ID
                    'title' => $item['song']['title'],
                    'artist' => $item['song']['artist'],
                    'art' => $item['song']['art'] ?? null,
                    'request_url' => 'https://radio.fkstudio.my.id' . $item['request_url']
                ];
            });
        } catch (\Exception $e) {
            $songs = [];
        }



        $messages = Message::latest()->take(50)->get()->reverse();
        $this->syncRequestStatuses();
        $recentRequests = SongRequest::where('status', 'pending')->latest()->take(5)->get();
        return view('welcome', compact('songs', 'messages', 'recentRequests'));
    }

    private function syncRequestStatuses()
    {
        try {
            $response = \Illuminate\Support\Facades\Http::get('https://radio.fkstudio.my.id/api/nowplaying/radio_fkstudio');
            if ($response->successful()) {
                $data = $response->json();

                // Get currently playing and history
                $nowPlayingTitle = $data['now_playing']['song']['title'] ?? '';
                $history = collect($data['song_history'] ?? [])->map(fn($h) => $h['song']['title'])->toArray();

                // Also check queue/playing next
                $nextSong = $data['playing_next']['song']['title'] ?? '';

                $activeTitles = array_merge([$nowPlayingTitle, $nextSong], $history);

                // Update local requests that are now playing or played
                SongRequest::where('status', 'pending')
                    ->whereIn('song_title', $activeTitles)
                    ->update(['status' => 'played']);
            }
        } catch (\Exception $e) {
            // Silently fail
        }
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_name' => auth()->user()->name,
            'sender_avatar' => auth()->user()->avatar,
            'message' => $request->message,
        ]);

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function getMessages()
    {
        $messages = Message::latest()->take(50)->get()->reverse()->values();
        return response()->json($messages);
    }

    public function requestSong(Request $request)
    {
        $request->validate([
            'song_title' => 'required|string',
            'song_artist' => 'nullable|string',
            'message' => 'nullable|string',
            'request_url' => 'nullable|string'
        ]);

        $apiMessage = 'Request saved locally.';
        $isSuccess = true;

        // Actually send the request to AzuraCast if URL is provided
        if ($request->request_url) {
            try {
                $apiRes = \Illuminate\Support\Facades\Http::withHeaders([
                    'Accept' => 'application/json',
                ])->post($request->request_url);

                $resData = $apiRes->json();
                $apiMessage = $resData['message'] ?? ($apiRes->successful() ? 'Request submitted safely!' : 'Radio API error.');
                $isSuccess = $apiRes->successful();

                if (!$apiRes->successful()) {
                    \Illuminate\Support\Facades\Log::error('AzuraCast Request Failed: ' . $apiRes->body());
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('AzuraCast Request Exception: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Connection to radio server failed.'], 500);
            }
        }

        if ($isSuccess) {
            SongRequest::create([
                'requester_name' => auth()->user()->name,
                'song_title' => $request->song_title,
                'song_artist' => $request->song_artist,
                'message' => $request->message,
                'status' => 'pending'
            ]);
        }

        return response()->json([
            'success' => $isSuccess,
            'message' => $apiMessage
        ]);
    }

    public function getRequests()
    {
        $this->syncRequestStatuses();
        $requests = SongRequest::where('status', 'pending')->latest()->take(5)->get();
        return response()->json($requests);
    }
}
