<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Mokam;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MokamController extends Controller
{
    protected string $permission_for = 'mokam';
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
        $mokams = Mokam::all();
        return Inertia::render('Mokam/Index', compact('mokams'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'name' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        Mokam::create($data);

        return redirect()->back()->with('success', 'Mokam created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        $mokam = Mokam::findOrFail($id);
        $mokam->update($data);

        return redirect()->back()->with('success', 'Mokam updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->hasPermission('delete');
        Mokam::find($id)->delete();
        return back();
    }

    /**
     * get all mokams
     * @return \Illuminate\Http\JsonResponse
     */
    public function allMokams()
    {
        return response()->json(Mokam::all(), 200);
    }
}
