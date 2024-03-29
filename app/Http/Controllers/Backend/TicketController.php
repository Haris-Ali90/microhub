<?php

namespace App\Http\Controllers\Backend;

use Config;

use Illuminate\Http\Request;

// use App\Http\Requests;
use App\Http\Requests\Backend\CategoryRequest;
use Illuminate\Support\Facades\Request as FacadeRequest;
use App\Http\Controllers\Backend\BackendController;
use Illuminate\Support\Facades\Auth;
//use Validator;

use App\Area;
use App\Category;
use App\Zone;
use App\Ticket;
use App\UserEntities;

class TicketController extends BackendController
{
    public function getIndex()
    {
        
        $ticket = Ticket::with('zone','category','area')->get();

        return backend_view( 'ticket.index')->with('ticket',$ticket);
    }

    public function edit(Ticket $ticket)
    {
        return backend_view('ticket.edit', compact('ticket'));
    }

    public function add()
    {
        return backend_view('ticket.add');
    }

    public function create(Request $request)
    {
        $data = $request->all();

        Ticket::create( $data );

        session()->flash('alert-success', 'Ticket has been created successfully!');
            return redirect( 'backend/ticket/add');
    }

    public function profile($id)
    {
        $users        = Ticket::where(['id' => $id])->get();
        $users = $users[0];
        
        return backend_view( 'ticket.profile');
    }

    public function update(Request $request, Ticket $ticket)
    {
        $data = $request->all();
        $ticket->update( $data );

        session()->flash('alert-success', 'Ticket has been updated successfully!');
        return redirect( 'backend/ticket/edit/' . $ticket->id );
    }

    public function destroy(Ticket $ticket)
    {   
        $id = $ticket->id ;   
        $ticket->delete();
        Ticket::where('id',$id)->delete();

        session()->flash('alert-success', 'Ticket has been deleted successfully!');
        return redirect( 'backend/ticket' );
    }

}
