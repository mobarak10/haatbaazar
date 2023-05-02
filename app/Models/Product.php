<?php

namespace App\Models;

use App\Helpers\Converter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'unit_id',
        'sub_category_id',
        'category_id',
        'brand_id',
        'sale_price',
        'purchase_price',
        'price_type',
        'stock_alert',
        'quantity_in_unit',
        'type',
        'barcode',
        'status',
        'divisor_number',
    ];

    protected $casts = ['quantity_in_unit' => 'array'];

    /**
     * Get Warehouse with quantity
     * @return BelongsToMany
     */
    public function showrooms(): BelongsToMany
    {
        return $this->belongsToMany(Showroom::class, 'stocks')
            ->withPivot('quantity','stocks.id', 'average_purchase_price')
            ->as('stock');
    }

    /**
     * get product category details
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * return product brand details
     * @return BelongsTo
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }


    /**
     * Unit
     * @return BelongsTo
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * get sale details product data
     * @return HasMany
     */
    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetails::class);
    }

    /**
     * get sale return details product data
     * @return HasMany
     */
    public function saleReturnDetails(): HasMany
    {
        return $this->hasMany(SaleReturnDetails::class);
    }

    /**
     * get purchase details product data
     * @return HasMany
     */
    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(PurchaseDetails::class);
    }

    /**
     * get purchase return details product data
     * @return HasMany
     */
    public function purchaseReturnDetails(): HasMany
    {
        return $this->hasMany(PurchaseReturnDetail::class);
    }

    /**
     * get damage details product data
     * @return HasMany
     */
    public function damageDetails(): HasMany
    {
        return $this->hasMany(DamageDetails::class);
    }

    /**
     * get production details product data
     * @return HasMany
     */
    public function productionDetails(): HasMany
    {
        return $this->hasMany(ProductionDetails::class);
    }

    /**
     * @return HasMany Stock
     */
    public function stock(): HasMany
    {
        return $this->hasMany(Stock::class);
    }


    /* ==== scope start ==== */
    // get all finish product
    public function scopeFinishProduct($query)
    {
        return $query->where('type', 'finish_product');
    }

    // get all raw product
    public function scopeRawProduct($query)
    {
        return $query->where('type', 'raw_material');
    }

    // get all bag
    public function scopeBag($query)
    {
        return $query->where('type', 'bag');
    }

    /**
     * Add total quantity
     * @param Builder $query
     * @return Builder
     */
    public function scopeAddTotalQuantity(Builder $query, $showroom_id = null): Builder
    {
        if ($showroom_id) {
            return $query->addSelect([
                'total_quantity' => Stock::selectRaw("IF (ISNULL(SUM(quantity)), 0, SUM(quantity))")
                    ->where('showroom_id', $showroom_id)
                    ->whereColumn('product_id', 'products.id')
            ]);
        }else{
            $user_showroom_id = UserShowroom::where('user_id', Auth::id())->pluck('showroom_id');
            return $query->addSelect([
                'total_quantity' => Stock::selectRaw("IF (ISNULL(SUM(quantity)), 0, SUM(quantity))")
                    ->whereIn('showroom_id', $user_showroom_id)
                    ->whereColumn('product_id', 'products.id')
            ]);
        }
    }


    /**
     * Add total quantity
     * @param Builder $query
     * @return Builder
     */
    public function scopeAddAveragePurchasePrice(Builder $query, $showroom_id = null): Builder
    {
        if ($showroom_id) {
            return $query->addSelect([
                'average_purchase_price' => Stock::select('average_purchase_price')
                    ->where('showroom_id', $showroom_id)
                    ->whereColumn('product_id', 'products.id')
                    ->limit(1)
            ]);
        }else{
            return $query->addSelect([
                'average_purchase_price' => Stock::selectRaw("IF (ISNULL(AVG(average_purchase_price)), 0, AVG(average_purchase_price))")
                    ->whereColumn('product_id', 'products.id')
            ]);
        }
    }

    /**
     * get product unit name
     * @param Builder $query
     * @return Builder
     */
    public function scopeAddUnitName(Builder $query) :Builder
    {
        return $query->addSelect([
            'unit_name' => Unit::select('name')
                ->whereColumn('id', 'products.unit_id')
        ]);
    }

    /**
     * get product unit label
     * @param Builder $query
     * @return Builder
     */
    public function scopeAddUnitLabel(Builder $query) :Builder
    {
        return $query->addSelect([
            'unit_label' => Unit::select('label')
                ->whereColumn('id', 'products.unit_id')
        ]);
    }

    /**
     * get product unit relation
     * @param Builder $query
     * @return Builder
     */
    public function scopeAddUnitRelation(Builder $query) :Builder
    {
        return $query->addSelect([
            'unit_relation' => Unit::select('relation')
                ->whereColumn('id', 'products.unit_id')
        ]);
    }

    /**
     * get product brand name
     * @param Builder $query
     * @return Builder
     */
    public function scopeAddBrandName(Builder $query) :Builder
    {
        return $query->addSelect([
            'brand_name' => Brand::select('name')
                ->whereColumn('id', 'products.brand_id')
        ]);
    }

    /**
     * get product category name
     * @param Builder $query
     * @return Builder
     */
    public function scopeAddCategoryName(Builder $query) :Builder
    {
        return $query->addSelect([
            'category_name' => Category::select('name')
                ->whereColumn('id', 'products.brand_id')
        ]);
    }

    /**
     * Add total sale quantity
     * @param Builder $query
     * @return Builder
     */
    public function scopeAddTotalSaleQuantity(Builder $query, $fromDate = null, $toDate = null): Builder
    {
        if ($fromDate && $toDate) {
            return $query->addSelect([
                'total_sale_quantity' => SaleDetails::selectRaw("IF (ISNULL(SUM(quantity)), 0, SUM(quantity))")
                    ->whereBetween('date', [$fromDate, $toDate])
                    ->whereColumn('product_id', 'products.id')
            ]);
        }else{
            return $query->addSelect([
                'total_sale_quantity' => SaleDetails::selectRaw("IF (ISNULL(SUM(quantity)), 0, SUM(quantity))")
                    ->whereColumn('product_id', 'products.id')
            ]);
        }
    }

    /**
     * Add total purchase quantity
     * @param Builder $query
     * @return Builder
     */
    public function scopeAddTotalPurchaseQuantity(Builder $query, $fromDate = null, $toDate = null): Builder
    {
        if ($fromDate && $toDate) {
            return $query->addSelect([
                'total_purchase_quantity' => PurchaseDetails::selectRaw("IF (ISNULL(SUM(quantity)), 0, SUM(quantity))")
                    ->whereBetween('date', [$fromDate, $toDate])
                    ->whereColumn('product_id', 'products.id')
            ]);
        }else{
            return $query->addSelect([
                'total_purchase_quantity' => PurchaseDetails::selectRaw("IF (ISNULL(SUM(quantity)), 0, SUM(quantity))")
                    ->whereColumn('product_id', 'products.id')
            ]);
        }
    }

    /* ==== scope end ==== */
}
