@foreach ($posts as $post)
<tr>
    <td>{{ $post->title }}</td>
    <td>{{ $post->content }}</td>
</tr>
@endforeach