@extends('layouts.attendeeApp')

@section('content')
    <div class="container">
        @if(session('alertmessage'))
            <p style="color: green">{{session('alertmessage')}}</p>
        @else
            <p style="color: red">"Purchase Fail!"</p>
        @endif



        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    @foreach($list as $i)
                        <div class="card mb-5">
                            <div class="card-header"><a href="{{route('attendee.event_agenda',["slug"=>$i->event_slug])}}"><b>{{$i->event_name}}</b></a></div>

                            <div class="card-body">

                                Organizer {{$i->name}}, {{$i->event_date}}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
@endsection
