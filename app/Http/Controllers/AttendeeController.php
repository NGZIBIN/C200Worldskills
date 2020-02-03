<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use App\Ticket;
use App\Organizer;
use App\Session;
use App\Channel;
use App\Room;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class AttendeeController extends Controller
{
    public function dashboard()
    {
        $list = DB::table('organizers')
            ->select('organizers.name', 'events.*')
            ->join('events', 'organizers.id', 'events.organizer_id')
            ->get();

        $data = $list;
        return view('AttendeeDashBoard')->with(['event_name' => $data,'event_date' => $data, 'name' => $data]);

    }


//    public function eventRegister($slug)
//    {
//       $session = \DB::table('sessions')->all();
//        $ticket = \DB::table('tickets')->all();
//        $sessionData = $session;
//        $ticketData = $ticket;
//        $event = Event::where('id', '=', $slug)->first();
//        return view('AttendeeEventRegistration', compact(['sessionData', 'ticketData', 'event'])) ;
//
//
//    }
    public function eventRegister($slug){

        $findEventBySlug = DB::table('events')->where('event_slug', '=', $slug)->get();
        $eventName = $findEventBySlug[0]->event_name;
        $findSessionByEvent = DB::table('sessions')->where('event_id', '=', $findEventBySlug[0]->id)->get();
        $findTicketByEvent = DB::table('tickets')->where('event_id', '=', $findEventBySlug[0]->id)->get();
        $findTicketsLeftByEvent = DB::table('tickets')->where([['tickets_left', '>', 0],['event_id', '=', $findEventBySlug[0]->id]])->get();

        return view('AttendeeEventRegistration', compact(['slug','eventName','findSessionByEvent','findTicketByEvent','findTicketsLeftByEvent']));

    }
    public function update(Request $request, $slug)
    {
//        $validator = Validator::make($request->all(), [
//            'ticketCostCB[]'=>'required_without_all'
//        ]);
//        if($validator->fails()){
//            return redirect('attendee/event_register/'.$slug)
//                ->withErrors($validator)
//                ->withInput();
//        }

//            if(count($request->ticketCostCB) === 0){
//                $result = "Please select a ticket to purchase";
//                return redirect('/attendee/event_register'.$slug)->with('alertmessage', $result);
//            }else
 {
                 $result = "";
                $getEventIdBySlug = DB::table('events')->where('event_slug', '=', $slug)->get();
                $ticket = DB::table('tickets')->select('*')->where('event_id', '=', $getEventIdBySlug[0]->id)->get();
                $selectedTickets = $request->ticketCostCB;
                foreach ($selectedTickets as $t) {
                    $ticketLeft = DB::table('tickets')->where('id', '=', (int)$t)->get();
                    $tl = $ticketLeft[0]->tickets_left - 1;
                    DB::table('tickets')->where('id', '=', (int)$t)->update(['tickets_left' => $tl]);
                }
                $result = "Purchase success!";
                return redirect('/attendee/home')->with('alertmessage', $result);
            }




//        if ($this->update($slug) === true) {
//            $result = "Purchase Successful";
//            return redirect('/attendee/home')->with('alertmessage', $result);
//        } else {
//            $result = "Sorry Purchase Fail";
//            return redirect('attendee/event_register' . $slug)->with('alertmessage', $result);
//        }
    }


//    public function update(Request $request, $slug)
//    {
//
//        $getEventIdBySlug = DB::table('events')->where('event_slug', '=', $slug)->get();
//        $ticket = DB::table('tickets')->select('*')->where('event_id', '=', $getEventIdBySlug[0]->id)->get();
//
//            $selectedTickets = $request->ticketCostCB;
//        foreach ($selectedTickets as $t) {
//            $ticketLeft = DB::table('tickets')->where('id', '=', (int)$t )->get();
//            $tl= $ticketLeft[0]->tickets_left - 1;
//            DB::table('tickets')->where('id', '=', (int)$t)->update(['tickets_left' => $tl]);
//        }
//        echo $tl;
////            $selectedTickets = $t->id;
//
//
////            if($tickets_left === 0){
////                $("[name=name]:checked").prop("disabled", true);
////            }
//
////            $result = "";
////            if ($this->update($slug) === true) {
////                $result = "Purchase Successful";
////                return redirect('/attendee/home')->with('alertmessage', $result);
////            } else {
////                $result = "Sorry Purchase Fail";
////                return redirect('attendee/event_register' . $slug)->with('alertmessage', $result);
////            }
//
//        }

//        return redirect('/attendee/home')->with('alert', 'Hi');

    public function eventAgenda($slug)
    {

        $event = DB::table('events')->where('event_slug','=', $slug)->get();
        $room = DB::table('rooms')->where('event_id','=', $event[0]->id)->get();
        $channel = DB::table('channels')->where('event_id','=', $event[0]->id)->get();
        $session = DB::table('sessions')
//            $attendeeID = DB::table('attendee_register_event')->where('event_id','=', $event[0]->id)->get()
            ->select('sessions.*', 'channels.channel_name', 'rooms.room_name', 'session_types.type')
            ->join('rooms', 'sessions.room_id', 'rooms.id')
            ->join('channels', 'sessions.channel_id', 'channels.id')
            ->join('session_types', 'sessions.session_type_id', 'session_types.id')
//                ->join('attendee_register_event', 'sessions.id', 'attendee_register_event.sessions_id')
            ->where('sessions.event_id','=', $event[0]->id)
            ->get();


        return view('AttendeeEventAgenda', compact(['session', 'room', 'channel', 'event']));
    }
//    public function eventAgenda($slug){
//        $getEventIdBySlug = DB::table('events')->select('id')->where('event_slug','=', $slug)->get();
//
//        $event_id = $getEventIdBySlug[0]->id;
//        $formatted_timings = [];
//        $groupedData = [];
//
//
//        $getSessionByEvent = DB::table('sessions')->where('event_id','=', $event_id)->get();
//
//        $getChannelByEvent = DB::table('channels')->where('event_id','=', $event_id)->get();
//
//        $getRoomByEvent = DB::table('rooms')->where('event_id','=', $event_id)->get();
//
//        foreach ($getChannelByEvent as $channel){
//            array_push($groupedData, [$channel]);
//        }
//        $count = 0;
//        $selectedIDs = [];
//
//        foreach ($getRoomByEvent as $room){
//            $channel_index = $room->channel_id-1;
//
//            if($count == 0){
//                array_push($groupedData[$channel_index], [$room]);
//            }
//            else{
//                if ($selectedIDs[count($selectedIDs)-1] == $room->channel_id) {
//                    array_push($groupedData[$channel_index][1], $room);
//                }
//                else{
//                    array_push($groupedData[$channel_index], [$room]);
//                }
//            }
//            $count += 1;
//
//            if(in_array($room->channel_id, $selectedIDs) == false){
//                array_push($selectedIDs, $room->channel_id);
//            }
//        }
//        $num = 0;
//        $idStack = [];
//
//        foreach ($getSessionByEvent as $session){
//            $channel_index = $session->channel_id-1;
//
//            if($num == 0){
//                array_push($groupedData[$channel_index], [$session]);
//            }
//            else{
//                if ($idStack[count($idStack)-1] == $session->channel_id) {
//                    array_push($groupedData[$channel_index][2], $session);
//                }
//                else{
//                    array_push($groupedData[$channel_index], [$session]);
//                }
//            }
//            $num += 1;
//
//            if(in_array($session->channel_id, $idStack) == false){
//                array_push($idStack, $session->channel_id);
//            }
//        }
////        dd($groupedData);
//        foreach ($getSessionByEvent as $session){
//            array_push($formatted_timings, substr($session->start_time, 11, 5));
//        }
//
//        return view('AttendeeEventAgenda', compact(['slug', 'formatted_timings', 'groupedData']));
//    }
//    public function eventAgenda($slug){
//        $getEventIdBySlug = DB::table('events')->select('id')->where('event_slug','=', $slug)->get();
//
//        $event_id = $getEventIdBySlug[0]->id;
//        $formatted_timings = [];
//        $groupedData = [];
//
//
//        $getSessionByEvent = DB::table('sessions')->where('event_id','=', $event_id)->get();
//
//        $getChannelByEvent = DB::table('channels')->where('event_id','=', $event_id)->get();
//
//        $getRoomByEvent = DB::table('rooms')->where('event_id','=', $event_id)->get();
//
//        foreach ($getChannelByEvent as $channel){
//            array_push($groupedData, [$channel]);
//        }
//        $count = 0;
//        $selectedIDs = [];
//
//        foreach ($getRoomByEvent as $room){
//            $channel_index = $room->channel_id-1;
//
//            if($count == 0){
//                array_push($groupedData[$channel_index], [$room]);
//            }
//            else{
//                if ($selectedIDs[count($selectedIDs)-1] == $room->channel_id) {
//                    array_push($groupedData[$channel_index][1], $room);
//                }
//                else{
//                    array_push($groupedData[$channel_index], [$room]);
//                }
//            }
//            $count += 1;
//
//            if(in_array($room->channel_id, $selectedIDs) == false){
//                array_push($selectedIDs, $room->channel_id);
//            }
//        }
//        $num = 0;
//        $idStack = [];
//
//        foreach ($getSessionByEvent as $session){
//            $channel_index = $session->channel_id-1;
//
//            if($num == 0){
//                array_push($groupedData[$channel_index], [$session]);
//            }
//            else{
//                if ($idStack[count($idStack)-1] == $session->channel_id) {
//                    array_push($groupedData[$channel_index][2], $session);
//                }
//                else{
//                    array_push($groupedData[$channel_index], [$session]);
//                }
//            }
//            $num += 1;
//
//            if(in_array($session->channel_id, $idStack) == false){
//                array_push($idStack, $session->channel_id);
//            }
//        }
////        dd($groupedData);
//        foreach ($getSessionByEvent as $session){
//            array_push($formatted_timings, substr($session->start_time, 11, 5));
//        }
//
//        return view('AttendeeEventAgenda', compact(['slug', 'formatted_timings', 'groupedData']));
//    }
    public  function sessionDetails($slug)
    {

        $findEventBySlug = DB::table('events')->where('event_slug', '=', $slug)->get();
        $findSessionByEvent = DB::table('sessions')->where('event_id', '=', $findEventBySlug[0]->id)->get();
        return view('AttendeeSessionDetails', compact(['findSessionByEvent']));
    }
//    public function formSubmit(Request $req)
//    {
//        print_r($req->input());
//    }
    public function getSlug($slug){
        // Get slug from database
        // $event = \DB::table('events')->where('event_slug', $slug)->first();
        $event = Event::where('event_slug', '=', $slug)->first();

        // Return view
        return view('AttendeeEventAgenda')->with(['events' => $event]);

    }


}
