<?php

use Bale\Umpak\Models\Post;

function makePostModel(array $overrides = []): Post
{
    static $counter = 0;
    $counter++;

    $post = new Post;
    $post->forceFill(array_merge([
        'id' => 'post-'.$counter,
        'author' => 'user-1',
        'title' => 'Berita '.$counter,
        'slug' => 'berita-'.$counter,
        'content' => ['blocks' => [['type' => 'paragraph', 'data' => ['text' => 'Halo']]]],
        'published' => true,
        'published_at' => now(),
    ], $overrides));
    $post->save();

    return $post;
}

describe('Post::toData() thumbnail', function () {

    beforeEach(function () {
        config()->set('umpak.cdn.enabled', false);
        config()->set('umpak.cdn.prefix', 'bale');
        config()->set('umpak.cdn.url', 'https://cdn.ponorogo.go.id');
    });

    it('resolves a relative filename to the CDN thumbnails folder', function () {
        $post = makePostModel(['thumbnail' => 'dinas-pendidikan-abc123.jpg']);

        expect($post->toData()->thumbnail)
            ->toBe(asset('bale/thumbnails/dinas-pendidikan-abc123.jpg'));
    });

    it('keeps an absolute thumbnail URL as-is (legacy import)', function () {
        $url = 'https://dindik.ponorogo.go.id/wp-content/uploads/2025/06/WhatsApp-Image-1024x576.jpg';

        $post = makePostModel(['thumbnail' => $url]);

        expect($post->toData()->thumbnail)->toBe($url);
    });

    it('returns null when the thumbnail is empty', function () {
        $post = makePostModel(['thumbnail' => null]);

        expect($post->toData()->thumbnail)->toBeNull();
    });
});
