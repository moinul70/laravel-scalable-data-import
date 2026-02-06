<!DOCTYPE html>
<html>
<head>
    <title>Posts</title>
</head>
<body>
<h1>Posts</h1>

@foreach ($posts as $post)
    <article>
        <h3>{{ $post->title }}</h3>
        <p>{{ $post->body }}</p>
        <hr>
    </article>
@endforeach

{{ $posts->links() }}
</body>
</html>
