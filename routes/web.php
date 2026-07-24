<?php

use App\Livewire\Pages\Agentes\Admin;
use App\Livewire\Pages\Agentes\Dashboard;
use App\Livewire\Pages\Agentes\Ticket\Index;
use App\Livewire\Pages\Agentes\Ticket\TicketsDetail;
use App\Livewire\Pages\Agentes\Time;
use App\Livewire\Pages\Auth\Login;
use App\Livewire\Pages\Cliente\CreateTicket;
use App\Livewire\Pages\Cliente\Portal;
use App\Livewire\Pages\Cliente\TicketDetail;
use App\Services\AuthService;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:agent,gerente'])->group(function () {
    Route::livewire('/dashboard', Dashboard::class)->name('dashboard');
    Route::livewire('/tickets', Index::class)->name('tickets');
    Route::livewire('/tickets/{id}', TicketsDetail::class)->name('tickets-detail');
    Route::livewire('/time', Time::class)->name('time');
    Route::livewire('/settings', 'pages::agentes.settings')->name('configuracoes');
});

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::livewire('/portal', Portal::class)->name('portal-cliente');
    Route::livewire('/create-ticket', CreateTicket::class)->name('create-ticket');
    Route::livewire('/ticket/{id}', TicketDetail::class)->name('ticket-detail');
});

Route::middleware(['auth', 'role:gerente'])->group(function () {
    Route::livewire('/admin', Admin::class)->name('portal-admin');
});

Route::livewire('/', Login::class)->name('login');

Route::post('/logout', [AuthService::class, 'logout'])->name('logout');
