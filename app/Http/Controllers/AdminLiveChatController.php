<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class AdminLiveChatController extends Controller
{
    public function index(Request $request)
    {
        $chats = Chat::orderBy('created_at', 'asc')->take(15);
        if ($request->user) {
            $chats->where(function ($query) use ($request) {
                $query->where('user_id', $request->user);
            });
        } else {
            $terbaru = Chat::orderBy('created_at', 'desc')->whereNot('user_id', auth()->id())->first();
            if(!empty($terbaru)) {
                $chats->where(function ($query) use ($terbaru) {
                    $query->where('user_id', $terbaru->user_id);
                });
            }
        }
        $chats = $chats->get();
        $users = User::role('customer')->whereHas('chats')->get();
        return view('admintoko.pengaduan.index', compact('chats', 'users'));
    }

    public function store(Request $request)
    {
        Chat::create([
            'user_id' => $request->user,
            'pesan' => $request->pesan,
            'is_admin' => 1
        ]);

        return back();
    }

    public function destroy(Request $request)
    {
        Chat::where('user_id', $request->user)->delete();
        Alert::success("Berhasil Bershikan chat");
        return back();
    }
}
