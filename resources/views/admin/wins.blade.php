@extends('mma::layouts.main')

@section('content')

    <nav class="breadcrumb" aria-label="breadcrumbs">
        <ul>
            <li><a href="{{ config('laravel-admin.root_url') }}">Dashboard</a></li>
            <li><a href="{{ config('laravel-admin.root_url') }}/utc/wins">UTC Draws</a></li>
        </ul>
    </nav>

    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-item">
            <f-post inline-template action="/api/utc/draws/{{ $promo }}">
                <button class="button" @click="onSubmit">
                    <span>Κλήρωση</span>
                    <span class="icon is-small">
                        <i class="fas fa-random"></i>
                    </span>
                </button>
            </f-post>
        </div>

        @if($participations->count())

            <div class="navbar-item">

                {{--<f-download inline-template action="/api/utc/participations/{{ $promo }}/download">--}}
                    {{--<a class="navbar-item" @click.prevent="onSubmit">--}}
                        {{--<a class="navbar-item" onclick="event.preventDefault();document.getElementById('download-form').submit();">--}}
                        {{--Download--}}
                        {{--<form id="download-form" action="/api/utc/participations/{{ $promo }}/download" method="POST" style="display: none;">--}}
                        {{--@csrf--}}
                        {{--</form>--}}
                    {{--</a>--}}
                {{--</f-download>--}}

                <f-download inline-template action="/api/utc/draws/{{ $promo }}/download">
                    <button class="button" @click="onSubmit">
                        <span>Download</span>
                        <span class="icon is-small">
                        <i class="fas fa-download"></i>
                    </span>
                    </button>
                </f-download>
            </div>

        @endif

    </nav>

    @if($participations->count() > 0)
        <table class="table is-fullwidth">
            <thead>
            <tr>

                <th><abbr title="Position">ID (Local)</abbr></th>
                <th>Redemption Code</th>

                @foreach(config('under-the-cap.current.participation_fields') as $field)
                    <th>{{ $field['title'] }}</th>
                @endforeach

                <th>Ημ./Ώρα Δημιουργίας Συμμετοχής</th>

                <th>Τύπος Νίκης</th>
                <th>Ημερομηνία Νίκης</th>

                <th>Approved</th>
                <th>Upgraded</th>
                <th>Ημ./Ώρα Δημιουργίας Νίκης</th>

            </tr>
            </thead>
            @foreach($participations as $win)

                <tr id="part{{ $win->id }}">

                    <th>{{ $win->participation->id }}</th>

                    <td>
                        @if(!empty($win->participation->redemptionCode))
                            {{ $win->participation->redemptionCode->code }}
                        @endif
                    </td>


                    @foreach(config('under-the-cap.current.participation_fields') as $field => $info )

                        @if(!empty($info['is_id']))
                            <td>{{ $win->participation->getDynamicField($field) }}</td>
                        @else
                            <td>{{ $win->participation[$field] }}</td>
                        @endif

                    @endforeach

                    <td>{{ $win->participation->created_at }}</td>

                    <td>{{ $win->type_name }}</td>
                    <td>{{ $win->associated_date }}</td>

                    <td>{{ $win->confirmed }}</td>
                    <td>{{ $win->bumped }}</td>
                    <td>{{ $win->created_at }}</td>

                    <td>
                    @if(!empty($wintypes[$win->type_id]['bumpable']) && !$win->bumped)
                        <f-put inline-template action="/api/utc/draws/{{ $promo }}/{{ $win->id }}/upgrade" confirmation="true">
                            <button class="button is-success" @click="onSubmit">
                                <span>Upgrade</span>
                                <span class="icon is-small">
                                    <i class="fas fa-arrow-up"></i>
                                </span>
                            </button>
                        </f-put>
                    @elseif(!empty($wintypes[$win->type_id]['bumpable']) && $win->bumped)
                        <f-delete inline-template action="/api/utc/draws/{{ $promo }}/{{ $win->id }}/upgrade" confirmation="true">
                            <button class="button is-warning" @click="onSubmit">
                                <span>Downgrade</span>
                                <span class="icon is-small">
                                <i class="fas fa-arrow-down"></i>
                            </span>
                            </button>
                        </f-delete>
                    @endif
                        <f-delete inline-template del-item="part{{ $win->id }}" action="/api/utc/draws/{{ $promo }}/{{ $win->id }}">
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

@endsection

@section('js')

@stop