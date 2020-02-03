@extends('layouts.attendeeApp')

@section('content')
    <div class="container">
        @if(session('alertmessage'))
            <p style="color: green">{{session('alertmessage')}}</p>
        @endif
            @include('layouts.flash-messages')
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" style="font-size: x-large">{{ __('Event List') }}</div>
                    <div class="card-body">


                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            @foreach($event_date as $event_data)
                                <div class="eventsDetails">
                                    <a href="{{ url('attendee/event_agenda/'.$event_data->event_slug) }}" ><div class="title">{{$event_data->event_name}}</div><br></a>
                                    <label>Organizer:</label>  {{$event_data->name}}, {{$event_data->event_date}}<br>
                                </div>

                                {{--                                <a href="{{route('eventAgenda')}}" <div class="title">{{$i->event_name}}<br></div>--}}
                                {{--                                Organizer {{$i->name}} {{$i->event_date}}/>--}}
                                {{--                                <div class="form-group form-inline">--}}
                                {{--                                    {!! Form::submit('<div class="title">{{$i->event_name}}<br></div>--}}
                                {{--                                   Organizer {{$i->name}} {{$i->event_date}}',['class'=>'btn btn-primary mr-5','id'=>'submit']) !!}--}}
                                {{--                                    <a href="{{route('eventAgenda')}}">Cancel</a>--}}
                                {{--                                </div>--}}
                            @endforeach

                            {{--                            @foreach($event_date_name as $i)--}}
                            {{--                                @foreach($i as $event_date_date)--}}
                            {{--                                    <button class="eventsDetails">--}}
                            {{--                                        {{$event_date_date->event_name}}<br>--}}
                            {{--                                        Organizer: {{$event_date_date->organizer_name}}--}}
                            {{--                                        {{$event_date_date->event_date}}--}}
                            {{--                                    </button>--}}
                            {{--                                @endforeach--}}
                            {{--                            @endforeach--}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
