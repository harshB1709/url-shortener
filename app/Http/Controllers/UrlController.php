<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UrlController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();
        $urls = $user->urls()->paginate(10);
        return view('urls.index', compact('urls'));
    }

    public function store(Request $request) {
        $request->validateWithBag('urlCreate', [
            'original_url' => ['required', 'url'],
        ]);

        $user = $request->user();
        $new_url = null;

        DB::transaction(function () use(&$new_url, $user, $request) {
            $new_url = $user->urls()->create([
                'original_url' => $request->input('original_url'),
            ]);
            $new_url->short_code = str_pad($this->base62_encode($new_url->id), config('app.short_code_length'), '0', STR_PAD_LEFT);
            $new_url->save();
        });

        if($new_url) {
            return response()->json([
                'success' => true,
                'newUrl' => $new_url
            ]);
        }
        return response()->json([
            'message' => 'An error occured. Try again in some time'
        ], 500);
    }

    public function update(Request $request, Url $url) {
        $request->validateWithBag('urlUpdate', [
            'original_url' => ['required', 'url'],
        ]);

        $user = $request->user();
        if ($user->cannot('update', $url)) {
            return abort(403);
        }

        $url->original_url = $request->get('original_url');
        $url->is_active = $request->get('is_active');
        $url->save();
        return response()->json([
            'success' => true
        ]);
    }

    public function deactivate(Request $request, Url $url) {
        $user = $request->user();
        if ($user->cannot('update', $url)) {
            return abort(403);
        }
        $url->is_active = false;
        $url->save();
        return redirect()->back();
    }

    public function delete(Request $request, Url $url) {
        $user = $request->user();
        if ($user->cannot('delete', $url)) {
            return abort(403);
        }
        $url->delete();
        return redirect()->back();
    }

    public function accessUrl(Request $request, Url $url) {
        if($url->is_active) {
            return redirect($url->original_url);
        }
        return abort(404);
    }

    private function base62_encode($val) {
        $base62Chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base = strlen($base62Chars);

        $result = '';
        while ($val > 0) {
            $result = $base62Chars[$val % $base] . $result;
            $val = (int)($val / $base);
        }

        return $result;
    }
}
