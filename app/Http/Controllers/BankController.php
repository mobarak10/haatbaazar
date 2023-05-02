<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BankController extends Controller
{
    protected string $permission_for = 'bank';
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
//        $banks = Bank::paginate(2);
        return Inertia::render('Bank/Index', [
            'banks' => Bank::with('bankAccounts')->orderBy('name')->paginate(30)
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
        return Inertia::render('Bank/Create');
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
            'name' => 'required|string',
            'description' => 'nullable|string'
        ]);

        $data['slug'] = Str::slug($request->name);

        Bank::create($data);

        return Redirect::route('bank.index')->with('success', 'Bank created successfully.');
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->hasPermission('update');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->hasPermission('update');
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string'
        ]);

        $data['slug'] = Str::slug($request->name);

        $bank = Bank::findOrFail($request->id);
        $bank->update($data);

        return Redirect::route('bank.index')->with('success', 'Bank update successfully.');
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
        $bank = Bank::findOrFail($id);
        $bank->delete();
        return Redirect::route('bank.index')->with('success', 'Bank Delete successfully');
    }
}
