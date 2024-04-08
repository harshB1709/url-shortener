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
        $user = $request->user();
        if($user->cannot('create', Url::class)) {
            abort(403, 'You cannot create any more urls. Kindly upgrade your quota if required');
        }

        $request->validateWithBag('urlCreate', [
            'original_url' => ['required', 'url'],
        ]);

        $new_url = null;

        DB::transaction(function () use(&$new_url, $user, $request) {
            $new_url = $user->urls()->create([
                'original_url' => $request->input('original_url'),
            ]);
            $new_url->short_code = str_pad($this->base62_encode($new_url->id), config('app.short_code_length'), '0', STR_PAD_LEFT);
            $new_url->save();
        });

        if($new_url) {
            if($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'newUrl' => $new_url
                ]);
            }
            else {
                return redirect()->route('url.index')->with('success', 'URL created successfully.');
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'An error occurred. Try again in some time'
            ], 500);
        } else {
            return redirect()->back()->withInput()->withErrors(['error' => 'An error occurred. Try again in some time']);
        }
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

        if($request->expectsJson()) {
            return response()->json([
                'success' => true
            ]);
        }
        else {
            return redirect()->back()->with('success', 'URL created successfully.');
        }
    }

    public function deactivate(Request $request, Url $url) {
        $user = $request->user();
        if ($user->cannot('update', $url)) {
            return abort(403);
        }
        $url->is_active = false;
        $url->save();

        if($request->expectsJson()) {
            return response()->json([
                'success' => true
            ]);
        }
        else {
            return redirect()->back()->with('success', 'URL deactivated successfully.');
        }
    }

    public function delete(Request $request, Url $url) {
        $user = $request->user();
        if ($user->cannot('delete', $url)) {
            return abort(403);
        }
        $url->delete();

        if($request->expectsJson()) {
            return response()->json([
                'success' => true
            ]);
        }
        else {
            return redirect()->back()->with('success', 'URL deleted successfully.');
        }
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
