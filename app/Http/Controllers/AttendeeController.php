<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use App\Ticket;
use App\Organizer;
use App\Session;
use App\Channel;
use App\Room;
use Illuminate\Support\Facades\Redirect;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Symfony\Component\Console\Input\Input;

class AttendeeController extends Controller
{
    private $_api_context;
    public function __construct()
    {
        $paypal_conf = \Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
            $paypal_conf['client_id'],
            $paypal_conf['secret']
        ));
        $this->_api_context->setConfig($paypal_conf['settings']);
    }
    public function payWithpaypal(Request $request, $slug){
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $item1 = new Item();
        $item1->setName('Item1')
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice($request -> get('totalCost'));

        $itemList = new ItemList();
        $itemList->setItems(array($item1));

        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($request -> get('totalCost'));


        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("Payment");


        $baseUrl = getBaseUrl();
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl("/attendee/home")
            ->setCancelUrl("/attendee/event_register/".$slug);

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));

        $request = clone $payment;

        try {
            $payment->create($this->_api_context);
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
           if(\Config::get('app.debug')){
               \Session::put('error', 'Connection timeout');
               return Redirect::to('/attendee/event_register/'.$slug);
           }else{
               \Session::put('error', 'Some error occur, sorry for inconvenient');
               return Redirect::to('/attendee/event_register/'.$slug);
           }
        }
        foreach ($payment->getLinks() as $link){
            if($link->getRel() == 'approval_url'){
                $redirect_url = $link->getHref();
                break;
            }
        }
        Session::put('paypal_payment_id', $payment->getId());
        if(isset($redirect_url)){
            return Redirect::away($redirect_url);
        }
        }
        public function getPaymentStatus($slug){
        $payment_id = Session::get('paypal_payment_id');
        Session::forget('paypal_payment_id');
        if(empty(Input::get('PaterID')) || empty(Input::get('token'))){
            \Session::put('error', 'Payment failed');
            return Redirect::to('/attendee/event_register/'.$slug);
        }
        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId(Input::get('PayerID'));

        $result = $payment->execute($execution, $this->_api_context);
        if($result->getState() == 'approved'){
            \Session::put('success', 'Payment success');
            return Redirect::to('/attendee/home');
        }
        \Session::put('error', 'Payment Failed');
        return Redirect::to('/attendee/event_register/'.$slug);
        }


    public function dashboard()
    {
        $list = DB::table('organizers')
            ->select('organizers.name', 'events.*')
            ->join('events', 'organizers.id', 'events.organizer_id')
            ->get();

        return view('AttendeeDashBoard',compact('list'));

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

        return view('AttendeeEventRegistration', compact(['slug','eventName','findSessionByEvent','findTicketByEvent','findTicketsLeftByEvent', 'findEventBySlug']));

    }
    public function update(Request $request, $slug)
    {
//
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

                $result = "Congrats you have successfully registered!";
                return redirect('/attendee/home')->with('alertmessage', $result);
//            return response()->json(['success'=>true,'url'=> route('/attendee/home')]);
            }


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
