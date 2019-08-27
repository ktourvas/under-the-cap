@extends('mma::layouts.main')

@section('content')

    <nav class="breadcrumb" aria-label="breadcrumbs">
        <ul>
            <li><a href="{{ config('laravel-admin.root_url') }}">Dashboard</a></li>
            <li><a href="{{ config('laravel-admin.root_url') }}/utc/participations/{{ $promo->slug() }}">Participations - {{ $promo->title() }}</a></li>
        </ul>
    </nav>

    <nav class="navbar" role="navigation" aria-label="main navigation">

        <div class="navbar-item">
            Current Count: {{ $participations->total() }}
        </div>

        <div class="navbar-item">
            <form action="" method="get">
                <div class="field">
                    <div class="control">
                        <input name="q" class="input" type="text" placeholder="Αναζήτηση" value="{{ $q }}">
                    </div>
                </div>
            </form>
        </div>

        <div class="navbar-item has-dropdown is-hoverable">
            <a class="navbar-link">
                More
            </a>
            <div class="navbar-dropdown">
                <f-download inline-template action="/api/utc/participations/{{ $promo->slug() }}/download">
                    <a class="navbar-item" @click.prevent="onSubmit">
                    {{--<a class="navbar-item" onclick="event.preventDefault();document.getElementById('download-form').submit();">--}}
                        Download
                        {{--<form id="download-form" action="/api/utc/participations/{{ $promo }}/download" method="POST" style="display: none;">--}}
                            {{--@csrf--}}
                        {{--</form>--}}
                    </a>
                </f-download>

                {{--<a class="navbar-item">--}}
                    {{--Jobs--}}
                {{--</a>--}}
                {{--<a class="navbar-item">--}}
                    {{--Contact--}}
                {{--</a>--}}
                {{--<hr class="navbar-divider">--}}
                {{--<a class="navbar-item">--}}
                    {{--Report an issue--}}
                {{--</a>--}}
            </div>
        </div>

    </nav>

    @if($participations->count() > 0)

        <table class="table is-fullwidth">

            <thead>
            <tr>

                <th>
                    <abbr title="Position">ID (Local)</abbr>
                </th>

{{--                @foreach(config('under-the-cap.current.participation_fields') as $field)--}}
                @foreach( $promo->participationFields() as $key => $field )
                    <th>
                        {{ $field['title'] }}
                    </th>
                @endforeach

                <th>Ημ./Ώρα Δημιουργίας</th>
            </tr>
            </thead>

            @foreach($participations as $participation)

                <tr id="part{{ $participation->id }}">

                    <th>{{ $participation->id }}</th>

{{--                    @foreach( config('under-the-cap.current.participation_fields') as $field => $info )--}}

                    @foreach( $promo->participationFields() as $field => $info )

                        @if(!empty($info['is_id']))
                            <td>{{ $participation->getDynamicField($field) }}</td>
                        @elseif(!empty($info['relation']))
                            <td>{{ $participation[$info['relation'][0]][$info['relation'][1]] }}</td>
                        @else
                            <td>{{ $participation[$field] }}</td>
                        @endif

                    @endforeach

                    <td>{{ $participation->created_at }}</td>
                    <td>
                        <f-delete inline-template del-item="part{{ $participation->id }}" action="/api/utc/{{ $promo->slug() }}/participations/{{ $participation->id }}" message="Πρόκειται να σβήσετε μία συμμετοχή. Σίγουρα;">
                            <form method="post" class="f-delete confirm" @submit.prevent="onSubmit">
                                <input type="hidden" name="_method" value="delete">
                                <button class="button is-danger">
                                    <span>Delete</span>
                                    <span class="icon is-small">
                                        <i class="fas fa-times"></i>
                                    </span>
                                </button>
                            </form>
                        </f-delete>
                    </td>
                </tr>
            @endforeach

        </table>

    @endif

    {{ $participations->links('mma::partials.pagination') }}

@endsection

@section('js')

@stop