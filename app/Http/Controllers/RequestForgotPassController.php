<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest_Forgot_PassRequest;
use App\Http\Requests\UpdateRequest_Forgot_PassRequest;
use App\Models\Request_Forgot_Pass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class RequestForgotPassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest_Forgot_PassRequest $request): RedirectResponse
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request_Forgot_Pass $request_Forgot_Pass): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request_Forgot_Pass $request_Forgot_Pass): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest_Forgot_PassRequest $request, Request_Forgot_Pass $request_Forgot_Pass): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request_Forgot_Pass $request_Forgot_Pass): RedirectResponse
    {
        //
    }
}
