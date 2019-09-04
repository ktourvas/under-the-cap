@extends('mma::layouts.main')

@section('content')

    <nav class="breadcrumb" aria-label="breadcrumbs">
        <ul>
            <li><a href="/{{ config('laravel-admin.root_url') }}">Dashboard</a></li>
            <li><a href="{{ config('laravel-admin.root_url') }}/utc/codes/{{ $promo->slug() }}">Redemption Codes - {{ $promo->title() }}</a></li>
        </ul>
    </nav>

    <nav class="navbar" role="navigation" aria-label="main navigation">

        <div class="navbar-item">
            <form action="" method="get">
                <div class="field">
                    <div class="control">
                        <input name="q" class="input" type="text" placeholder="Αναζήτηση" value="{{ $q }}">
                    </div>
                </div>
            </form>
        </div>

    </nav>

    @if($codes->count() > 0)

        <table class="table is-fullwidth">
            <thead>
            <tr>
                <th><abbr title="Position">ID (Local)</abbr></th>
                <th>Κωδικός</th>
                <th>Σχετική συμμετοχή</th>
            </tr>
            </thead>

            @foreach($codes as $code)
                <tr id="part{{ $code->id }}" data-id="{{ $code->id }}">
                    <th>{{ $code->id }}</th>
                    <td>{{ $code->code }}</td>
                    @if( $code->participation()->exists() )
                        <td>
                            {{ $code->participation->name }} {{ $code->participation->surname }} @ {{ $code->participation->created_at }}
                        </td>
                    @else
                        <td>
                            N/A
                        </td>
                    @endif
                </tr>
            @endforeach
        </table>

    @endif

    {{ $codes->links('mma::partials.pagination') }}

@endsection

@section('js')

@stop