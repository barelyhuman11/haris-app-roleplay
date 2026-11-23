<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Telegram\TelegramRouter;

class WebhookController extends Controller
{
    public function __invoke(Request $request, TelegramRouter $router)
    {
        $update = $request->all();

        // Debug purpose
        Storage::disk('local')->put('telegram.txt', json_encode($update));

        // Routing
        $router->handle($update);

        return response()->json([
            'ok' => true,
        ]);
    }
}
