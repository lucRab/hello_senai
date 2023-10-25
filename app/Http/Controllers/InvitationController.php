<?php

namespace App\Http\Controllers;

use App\Http\Resources\V1\InviteResource;
use App\Models\Invitation;
use Illuminate\Http\Request;
/**
 *  Classe responsavel pelo controle do convite
 * @version ${1:1.0.0
 */
class InvitationController extends Controller
{
    private Invitation $invite;

    public function __construct() {
        $this->invite = new Invitation;
    }
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $invites = $this->invite->paginate();
        return InviteResource::collection($invites);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $data = $request->all();
        return $this->invite->createInvite($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invitation $invitation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invitation $invitation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $idinvite) {
        $data = $request->all();
        $this->invite->updateInvite($idinvite, $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($idInvite) {
        $this->invite->deleteInvite($idInvite);
    }

}
