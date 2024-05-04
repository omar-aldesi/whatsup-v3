<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group_id',
        'contact_no',
        'name',
        'status'
    ];


    public function group()
    {
    	return $this->belongsTo(Group::class, 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     *scope filter
     */

     public function scopefilter($q,$request){
       
        return $q->when($request->status &&  $request->status !='All', function($q) use($request) {

            return $q->where('status', $request->status);
            })->when($request->search !=null,function ($q) use ($request) {
              
            return $q->where('name', 'like', '%' .$request->search.'%')
            ->orWhere('contact_no', 'like', '%' .$request->search.'%');
        
            
        });
    }
}
