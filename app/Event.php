<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
  protected $table = 'events';
  public $timestamps = false;

  public function tickets(){
  	return $this->hasMany('App\Ticket');
  }

  public function registrations(){
  	return $this->belongsToMany('App\Attendee', 'attendee_register_event');
  }

  public function sessions(){
  	return $this->hasMany('App\Session');
  }
    public function channels(){
        return $this->hasMany('App\Channel');
    }

}
