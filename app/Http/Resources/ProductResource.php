<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => (string)$this->id,
            'name' => $this->name,
            'unit_id' => $this->unit_id,
            'sub_category_id' => $this->sub_category_id,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'sale_price' => $this->sale_price,
            'purchase_price' => $this->purchase_price,
            'dhaka_price' => $this->dhaka_price,
            'price_type' => $this->price_type,
            'stock_alert' => $this->stock_alert,
            'quantity_in_unit' => $this->quantity_in_unit,
            'type' => $this->type,
            'barcode' => $this->barcode,
            'status' => $this->status,
            'brand' => new BrandResource($this->brand),
            'category' => new CategoryResource($this->category),
            'unit' => new UnitResource($this->unit),
        ];
    }
}
