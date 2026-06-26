<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Keyword;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KeywordController extends Controller
{
    public function suggest(Request $request): JsonResponse
    {
        $q      = $request->get('q', '');
        $locale = $request->get('locale', 'id');

        if (strlen($q) < 2) {
            return response()->json(['items' => []]);
        }

        $keywords = Keyword::where('locale', $locale)
            ->where('is_approved', true)
            ->where('keyword', 'like', '%' . $q . '%')
            ->orderByDesc('usage_count')
            ->limit(15)
            ->pluck('keyword');

        return response()->json(['items' => $keywords]);
    }
}
