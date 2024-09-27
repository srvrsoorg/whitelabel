<?php

namespace App\Interfaces;

interface TicketRepositoryInterface 
{
	public function index($request, $user);
	public function show($ticket);
	public function storeReply($request, $ticket);
	public function replyIndex($ticket, $user);
	public function statusUpdate($ticket, $action);
}