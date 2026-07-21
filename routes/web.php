<?php

use App\Services\AuthService;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:agent,gerente'])->group(function () {
    Route::livewire('/dashboard', 'pages::agentes.dashboard')->name('dashboard');
    Route::livewire('/tickets', 'pages::agentes.ticket.index')->name('tickets');
    Route::livewire('/tickets/{id}', 'pages::agentes.ticket.tickets-detail')->name('tickets-detail');
    Route::livewire('/time', 'pages::agentes.time')->name('time');
    Route::livewire('/settings', 'pages::agentes.settings')->name('configuracoes');
});

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::livewire('/portal', 'pages::client.portal')->name('portal-cliente');
    Route::livewire('/create-ticket', 'pages::client.create-ticket')->name('create-ticket');
    Route::livewire('/ticket/{id}', 'pages::client.ticket-detail')->name('ticket-detail');
});

Route::middleware(['auth', 'role:gerente'])->group(function () {
    Route::livewire('/admin', 'pages::agentes.admin')->name('portal-admin');
});

Route::livewire('/', 'pages::auth.login')->name('login');

Route::post('/logout', [AuthService::class, 'logout'])->name('logout');
