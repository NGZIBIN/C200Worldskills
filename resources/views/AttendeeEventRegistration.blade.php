
@extends('layouts.attendeeApp')

@section('content')
    <div class="panel-heading">

        <h2 class="mt-3 mb-3" id="event_name">{{$eventName}}</h2>
        <div id="msg" style="color: green"></div>
        <div id="redirect"></div>
        <hr/>
        {{--        <div class="container">--}}
        {{--            @if($message = \App\Session::get('success'))--}}
        {{--                <div class=""--}}

        {!! Form::open(['method'=>'POST', 'id' => 'form','action'=>['AttendeeController@update', $slug]]) !!}
        @csrf
        <div class="grid-container-events">
            <div class="row">
                <div class="col-8 mb-5" style="display: flex">


                    <div class="grid-container-events">
                        @if(count($findTicketsLeftByEvent) < 1)
                            <div class="alert alert-warning" role="alert">
                                All tickets are sold out..
                            </div>
                        @else
                            @foreach($findTicketsLeftByEvent as $ticket)
                                <div class="card grid-item" >
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $ticket->ticket_name}}</h5>
                                        {{--                                        <h6 class="card-subtitle mb-2 text-muted"> {{Form::checkbox('name', $ticket->id}}<span>{{ $ticket->ticket_cost}}.-</span></h6>--}}
                                        <h6>{{Form::checkbox('ticketCostCB[]', $ticket->id)}}{{$ticket->ticket_cost}}
                                            <span>
                                        {{Form::hidden($ticket->ticket_name, $ticket->ticket_cost, ["id"=>$ticket->id])}}</span>
                                        </h6>
                                        <br>
                                        <h6 class="card-subtitle mb-2 text-muted">{{ $ticket->tickets_left }} tickets available</h6>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        {{--                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />--}}
                        {{--                            <input type="submit" class="btn btn-primary mr-5" value="Save" style="float: right;"/>--}}


                    </div>

                </div>
                <div class="col-5 mt-3">
                    <div class="card grid-item">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="title">Select Additional Workshops you want to book:</label><br><br/>
                                @foreach($findSessionByEvent as $i)
                                    {{Form::checkbox('session', $i->cost)}}
                                    {{$i->title}}<br/>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4 form-group">
                    <div class="float-right align-content-around">
                        <label>Event Ticket:</label><div id="ticketName"></div>

                        Additional Workshops:<div id="sessionCost"></div> <br>
                        <hr>
                        Total: <div id="totalCost"></div>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <input class=" float-right btn btn-block btn-primary"   id="btnSubmit" value="Purchase"/>
                        <input class=" float-right btn btn-block btn-primary" type="submit"  id="btnConfirm" value="Confirm"/>

                    </div>
                </div>
            </div>
        </div>


        @csrf
        <div class="modal fade" id="item_modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Ticket Details</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="form-group">
                                <label>Ticket Name</label>
                                <input name="itemname"  class="form-control" type="text" readonly>
                            </div>
                            <div class="form-group">
                                <label>Item Price</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">SGD</span>
                                    </div>
                                    <input name="itemprice" class="form-control" type="text" readonly>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <div type="submit" id="paypal-button"></div>

                        </div>
                    </div><!-- /.modal-body -->
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!-- End Bootstrap modal -->

    </div>

    {!! Form::close() !!}
@endsection
