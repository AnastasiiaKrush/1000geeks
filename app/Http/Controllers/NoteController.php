<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Note;
use App\User;

class NoteController extends Controller
{

    /**
     * Create new note.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $data)
    {
        $user = User::where('api_token', '=', $data['token'])->select('id')->first();

        if(!$user) {
            return response()->json([ 'status'=> 0, 'error' => 'There is no user with given token' ]);
        }

        if( !$data['title'] || !$data['description']) {
            return response()->json(['status'=> 0, 'error' => 'Some fields are empty']);
        }

        $note = Note::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'user_id' => $user->id
        ]);

        return response()->json([ 'status'=> 1, 'data'=> [ 'note id' => $note->id] ]);
    }

    /**
     * Update note.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $data)
    {
        $user = User::where('api_token', '=', $data['token'])->select('id')->first();

        if(!$user) {
            return response()->json([ 'status'=> 0, 'error' => 'There is no user with given token' ]);
        }

        $note = Note::where('id', '=', $data['note_id'])
                ->where('user_id', '=', $user->id)
                ->first();
        if(!$note) {
            return response()->json([ 'status'=> 0, 'error' => 'There is no note with given id' ]);
        }

        if(isset($data['title'])) $note->title = $data['title'];
        if(isset($data['description'])) $note->description = $data['description'];

        $note->save();

        return response()->json([ 'status'=> 1, 'data'=> [ 'note id' => $note->id] ]);
    }

    /**
     * Get all notes for user.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function getList(Request $data)
    {
        $user = User::where('api_token', '=', $data['token'])->select('id')->first();

        if(!$user) {
            return response()->json([ 'status'=> 0, 'error' => 'There is no user with given token' ]);
        }

        $notes = Note::where('user_id', '=', $user->id)->select('id', 'title', 'description')->get();

        if(!count($notes)) {
            return response()->json([ 'status'=> 0, 'error' => 'There are no notes found' ]);
        }

        return response()->json([ 'status'=> 1, 'data'=> [ 'note list' => $notes] ]);
    }

}
