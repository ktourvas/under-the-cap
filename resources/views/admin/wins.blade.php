@extends('mma::layouts.main')

@section('content')

    <nav class="breadcrumb" aria-label="breadcrumbs">
        <ul>
            <li>
                <a href="{{ config('laravel-admin.root_url') }}">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ config('laravel-admin.root_url') }}/utc/wins/{{ $promo->slug() }}">
                    Draws - {{ $promo->title() }}
                </a>
            </li>
        </ul>
    </nav>

    <nav class="navbar" role="navigation" aria-label="main navigation">

        <div class="navbar-item">

            <f-post inline-template action="/api/utc/draws/{{ $promo->slug() }}">

                <div class="field has-addons has-addons-centered">
                    <p class="control">
                        <span class="select">
                            <select v-model="added.draw">
                                @foreach($promo->adhocDraws() as $key => $draw)
                                    <option value="{{ $key }}">{{ $draw['title'] }}</option>
                                @endforeach
                            </select>
                        </span>
                    </p>
                    <p class="control">
                        <button class="button is-success" @click="onSubmit" :disabled="sending">
                            <span class="icon is-small" v-if="sending">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                            <span>Κλήρωση</span>
                            <span class="icon is-small">
                                <i class="fas fa-random"></i>
                            </span>
                        </button>
                    </p>
                </div>

            </f-post>

        </div>

        @if($participations->count())

            <div class="navbar-item">

                <f-download inline-template action="/api/utc/draws/{{ $promo->slug() }}/download">
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

        <table class="table is-fullwidth is-size-7">
            <thead>
            <tr>

                <th><abbr title="Position">ID (Local)</abbr></th>

                @foreach(config('under-the-cap.current.participation_fields') as $field)
                    <th>{{ $field['title'] }}</th>
                @endforeach

                <th>Ημ./Ώρα Δημιουργίας Συμμετοχής</th>

                <th>Κλήρωση</th>
                <th>Τύπος Νίκης</th>
                <th>Δώρο</th>
                <th>Ημερομηνία Νίκης</th>

                <th>Approved</th>
                <th>Upgraded</th>
                <th>Ημ./Ώρα Δημιουργίας Νίκης</th>

            </tr>
            </thead>

            @foreach($participations as $index => $win)

                @if($index == 0 || $win->associated_date !== $participations[$index-1]->associated_date)

                    <tr>
                        <th colspan="16" class="is-success">
                            Date: {{ $win->associated_date }}
                        </th>
                    </tr>

                @endif

                <tr id="part{{ $win->id }}">

                    <th>
                        {{ $win->participation->id }}
                    </th>

                    @foreach( $promo->participationFields() as $key => $field )

                        @if(!empty($field['is_id']))
                            <td>
                                {{ $win->participation->getDynamicField($key) }}
                            </td>
                        @elseif(!empty($field['relation']))
                            <td>
                                {{ $win->participation[$field['relation'][0]][$field['relation'][1]] }}
                            </td>
                        @else
                            <td>
                                {{ $win->participation[$key] }}
                            </td>
                        @endif

                    @endforeach

                    <td>{{ $win->participation->created_at }}</td>
                    <td>{{ $win->draw_name }}</td>
                    <td>{{ $win->type_name }}</td>

                    <td>
                        @if($win->present()->exists())
                            {{ $win->present->title }}
                        @endif
                    </td>


                    <td>{{ $win->associated_date }}</td>

                    <td>{{ $win->confirmed }}</td>
                    <td>{{ $win->bumped }}</td>
                    <td>{{ $win->created_at }}</td>

                    <td>
                        <div class="dropdown is-hoverable is-small is-right">
                            <div class="dropdown-trigger">
                                <button class="button" aria-haspopup="true" aria-controls="dropdown-menu">
                                    <span>Actions</span>
                                    <span class="icon is-small"><i class="fas fa-angle-down" aria-hidden="true"></i></span>
                                </button>
                            </div>
                            <div class="dropdown-menu" id="dropdown-menu" role="menu">
                                <div class="dropdown-content">
                                    @if($win->runnerup == 1 && $win->bumped == 0)
                                        <div class="dropdown-item">
                                            <f-put inline-template action="/api/utc/draws/{{ $promo->slug() }}/{{ $win->id }}/upgrade" confirmation="true">
                                                <button class="button is-success is-small is-fullwidth" @click="onSubmit">
                                                    <span>Upgrade</span>
                                                    <span class="icon is-small">
                                                        <i class="fas fa-arrow-up"></i>
                                                    </span>
                                                </button>
                                            </f-put>
                                        </div>
                                    @elseif($win->runnerup == 1 && $win->bumped == 1)
                                        <div class="dropdown-item">
                                            <f-delete inline-template action="/api/utc/draws/{{ $promo->slug() }}/{{ $win->id }}/upgrade" confirmation="true">
                                                <button class="button is-warning is-small is-fullwidth" @click="onSubmit">
                                                    <span>Downgrade</span>
                                                    <span class="icon is-small">
                                                        <i class="fas fa-arrow-down"></i>
                                                    </span>
                                                </button>
                                            </f-delete>
                                        </div>
                                    @endif
                                    <div class="dropdown-item">
                                        <f-delete inline-template del-item="part{{ $win->id }}" action="/api/utc/draws/{{ $promo->slug() }}/{{ $win->id }}">
                                            <form method="post" class="f-delete confirm" @submit.prevent="onSubmit">
                                                <input type="hidden" name="_method" value="delete">
                                                <button class="button is-danger is-small is-fullwidth">
                                                    <span>Delete</span>
                                                    <span class="icon is-small"><i class="fas fa-times"></i></span>
                                                </button>
                                            </form>
                                        </f-delete>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </table>

    @endif

@endsection

@section('js')

@stop