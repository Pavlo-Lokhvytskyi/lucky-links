@extends('layout')

@section('title', 'History')

@section('content')
    @if ($draws->isEmpty())
        <p class="empty">No draws yet.</p>
    @else
        <table>
            <tr>
                <th>Number</th>
                <th>Result</th>
                <th>Win amount</th>
                <th>Played at</th>
            </tr>
            @foreach ($draws as $draw)
                <tr>
                    <td>{{ $draw->number }}</td>
                    <td>
                        <span class="badge {{ $draw->is_win ? 'badge--win' : 'badge--lose' }}">
                            {{ $draw->is_win ? 'Win' : 'Lose' }}
                        </span>
                    </td>
                    <td>{{ $draw->payout() }}</td>
                    <td>{{ $draw->created_at->toDateTimeString() }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <a class="back-link" href="{{ $link->url() }}">&larr; Back</a>
@endsection
