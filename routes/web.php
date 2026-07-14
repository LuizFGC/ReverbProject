<?php

use App\Services\AuthService;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:agent'])->group(function () {
    Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard');
    Route::livewire('/tickets', 'pages::ticket.index')->name('tickets');
    Route::livewire('/tickets/{id}', 'pages::ticket.tickets-detail')->name('tickets-detail');
    Route::livewire('/time', 'pages::time')->name('time');
    Route::livewire('/settings', 'pages::settings')->name('configuracoes');
});

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::livewire('/portal', 'pages::client.portal')->name('portal-cliente');
    Route::livewire('/create-ticket', 'pages::client.create-ticket')->name('create-ticket');
    Route::livewire('/ticket/{id}', 'pages::client.ticket-detail')->name('ticket-detail');
});

Route::livewire('/', 'pages::auth.login')->name('login');

Route::post('/logout', [AuthService::class, 'logout'])->name('logout');
