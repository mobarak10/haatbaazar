<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\CostCategory;
use App\Models\CostSubcategory;
use Illuminate\Http\Request;

class CostSubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render("CostSubcategory/Index", [
            "costCategories" => CostCategory::orderBy('name')->get(),
            "costSubcategories" => CostSubcategory::query()
                ->withCount("expenses")
                ->addSelect([
                    'cost_category_name' => CostCategory::select('name')
                    ->whereColumn('cost_category_id', 'cost_categories.id')
                    ->limit(1)
                ])
                ->orderBy('cost_category_name')
                ->orderBy('name')
                ->get(),
        ]);
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cost_category_id' => 'required|integer|exists:cost_categories,id',
            'name' => 'required|string|unique:App\Models\CostSubcategory',
            'description' => 'nullable|string',
        ]);

        $data['slug'] = Str::slug($request->name);

        CostSubcategory::create($data);

        return redirect()->back()->with('success', 'Cost subcategory created successfully.');
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:cost_subcategories',
            'cost_category_id' => 'required|integer|exists:cost_categories,id',
            'name' => ['required', 'string', 'unique:App\Models\CostSubcategory,name,' . $id],
            'description' => 'nullable|string',
        ]);

        $data['slug'] = Str::slug($request->name);

        CostSubcategory::find($id)
            ->update($data);

        return redirect()->back()->with('success', 'Cost subcategory updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cost_subcategory = CostSubcategory::query()
            ->withCount('expenses')
            ->find($id);

        if ($cost_subcategory->expenses_count > 0) {
            return redirect()->back()->with('errors', 'Failed to delete cost subcategory. Cost subcategory has some expenses.');
        }

        $cost_subcategory->delete();

        return redirect()->back()->with('success', 'Cost subcategory deleted successfully.');
    }
}
