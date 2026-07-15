@extends('layout')

@section('title', 'History')

@section('content')
    @if ($draws->isEmpty())
        <p>No draws yet.</p>
    @else
        <table border="1">
            <tr>
                <th>Number</th>
                <th>Result</th>
                <th>Win amount</th>
                <th>Played at</th>
            </tr>
            @foreach ($draws as $draw)
                <tr>
                    <td>{{ $draw->number }}</td>
                    <td>{{ $draw->is_win ? 'Win' : 'Lose' }}</td>
                    <td>{{ $draw->payout() }}</td>
                    <td>{{ $draw->created_at->toDateTimeString() }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <p><a href="{{ $link->url() }}">Back</a></p>
@endsection
