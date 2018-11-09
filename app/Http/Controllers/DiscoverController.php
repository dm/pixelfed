<?php

namespace App\Http\Controllers;

use App\{
  Follower,
  Hashtag,
  Profile,
  Status, 
  UserFilter
};
use Auth, DB, Cache;
use Illuminate\Http\Request;

class DiscoverController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function home(Request $request)
    {
        return view('discover.home');
    }

    public function showTags(Request $request, $hashtag)
    {
        $this->validate($request, [
          'page' => 'nullable|integer|min:1|max:10',
      ]);

        $tag = Hashtag::with('posts')
          ->withCount('posts')
          ->whereSlug($hashtag)
          ->firstOrFail();

        $posts = $tag->posts()
          ->withCount(['likes', 'comments'])
          ->whereIsNsfw(false)
          ->whereVisibility('public')
          ->has('media')
          ->orderBy('id', 'desc')
          ->simplePaginate(12);

        return view('discover.tags.show', compact('tag', 'posts'));
    }
}
