@extends('layouts.attendeeApp')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2>{{$event[0]->event_name}}</h2>
                {{--                <h1>{{$event->event_name}}</h1>--}}
                <a class="btn btn-primary float-right" href="{{route('attendee.event_register', ["slug"=>$event[0]->event_slug])}}">Register for this event</a>
                <h5 class="font-weight-bold mt-5 mb-3">Channels</h5>
                <table class="table table-striped mb-5">
                    <thead>
                    <tr>
                        <th>Channel names</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($channel as $c)
                        <tr>
                            <td>
                                {{$c->channel_name}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <h5 class="font-weight-bold mb-3">Rooms</h5>
                <table class="table table-striped mb-5">
                    <thead>
                    <tr>
                        <th>Room names</th>
                        <th>Room capacity</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($room as $r)
                        <tr>
                            <td>
                                {{$r->room_name}}
                            </td>
                            <td>
                                {{$r->room_capacity}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <h5 class="font-weight-bold mb-3">Sessions</h5>
                <table class="table table-striped mb-5" style="width: 780px">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Channel Name</th>
                        <th>Room Name</th>
                        <th>Speaker</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($session as $s)
                        <tr>
                            <td>
                                {{$s->title}}
                            </td>
                            <td>
                                {{$s->channel_name}}
                            </td>
                            <td>
                                {{$s->room_name}}
                            </td>
                            <td>
                                {{$s->speaker}}
                            </td>
                            <td>
                                {{$s->type}}
                            </td>
                            <td>
                                {{$s->description}}
                            </td>
                            <td>
                                {{substr($s->start_time, 0, 16)}}
                            </td>
                            <td>
                                {{substr($s->end_time, 0 , 16)}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                {{--                            <table class="table">--}}
                {{--                                @foreach($channelData -> $channel)--}}
                {{--                                    $name = {{$channel ->channel_name}}--}}
                {{--                                    @endforeach--}}

                {{--                                <tbody>--}}
                {{--                                <tr>--}}
                {{--                                    <th scope="row">{{$name}}</th>--}}
                {{--                                    <td>Mark</td>--}}
                {{--                                    <td>Otto</td>--}}
                {{--                                    <td>@mdo</td>--}}
                {{--                                </tr>--}}
                {{--                                <tr>--}}
                {{--                                    <th scope="row">2</th>--}}
                {{--                                    <td>Jacob</td>--}}
                {{--                                    <td>Thornton</td>--}}
                {{--                                    <td>@fat</td>--}}
                {{--                                </tr>--}}
                {{--                                </tbody>--}}
                {{--                            </table>--}}

            </div>
        </div>
    </div>
@endsection
