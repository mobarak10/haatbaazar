<?php

namespace App\Http\Controllers;

use App\Models\Cash;
use App\Models\Showroom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Str;

class CashController extends Controller
{
    protected string $permission_for = 'cash';
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
        return Inertia::render('Cash/Index', [
            'cashes' => Cash::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $this->hasPermission('create');

        $showrooms = Showroom::select('id', 'name')->get();
        return Inertia::render('Cash/Create', compact('showrooms'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->hasPermission('create');
        $data = $request->validate([
            'title' => 'required|string',
            'balance' => 'required|numeric',
            'showroom_id' => 'required|integer|exists:App\Models\Showroom,id',
        ]);

        $data['slug'] = Str::slug($request->title);

        Cash::create($data);

        return Redirect::route('cash.index')->with('success', 'Cash created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->hasPermission('show');
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function edit($id)
    {
        $this->hasPermission('update');

        $oldCash = Cash::findOrFail($id);
        $showrooms = Showroom::select('id', 'name')->get();
        return Inertia::render('Cash/Create', compact('oldCash', 'showrooms'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->hasPermission('update');
        $cash = Cash::findOrFail($id);
        $data = $request->validate([
            'title' => 'required|string',
            'balance' => 'required|numeric',
            'showroom_id' => 'required|integer|exists:App\Models\Showroom,id',
        ]);
        $data['slug'] = Str::slug($request->title);
        $cash->update($data);
        return Redirect::route('cash.index')->with('success', 'Cash updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->hasPermission('delete');

        $cash = Cash::findOrFail($id);
        $cash->delete();
        return Redirect::route('cash.index')->with('success', 'Cash Delete successfully');
    }

    /**
     * All cashes (response)
     * @return \Illuminate\Http\JsonResponse
     */
    public function allCashes()
    {
        return response(Cash::all(), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cashDetails(Request $request) {
        $record = Cash::find($request->id);
        return response()->json($record, 200);
    }
}
