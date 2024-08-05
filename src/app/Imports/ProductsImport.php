<?php

namespace App\Imports;

use App\Models\Pos\Product\Brand\Brand;
use App\Models\Pos\Product\Category\Category;
use App\Models\Pos\Product\Group\Group;
use App\Models\Pos\Product\SubCategory\SubCategory;
use App\Models\Pos\Product\Unit\Unit;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Import\ProductImportService;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
{
    use Importable, SkipsFailures;

    /**
     * Processes a row of data and creates a new product and its variants.
     *
     * @param array $row The row of data containing the product information.
     * @throws \Exception If there is an error during the transaction.
     * @return void
     */
    public function model(array $row)
    {
        $category = Category::query()->where('name', $row['category'])->first()->id ?? null;
        // $subcategory = SubCategory::query()->where('name', $row['subcategory'])->first()->id ?? null;
        // $brand = Brand::query()->where('name', $row['brand'])->first()->id ?? null;
        // $group = Group::query()->where('name', $row['group'])->first()->id ?? null;
        // $unit = Unit::query()->where('name', $row['unit'])->first()->id ?? null;
        $status_id =  resolve(StatusRepository::class)->productActive() ?? null;

        DB::transaction(
            fn () => resolve(ProductImportService::class)
                ->setAttrs(array_merge($row, [
                    'category_id' => $category,
                    // 'sub_category_id' => $subcategory,
                    // 'brand_id' => $brand,
                    // 'unit_id' => $unit,
                    // 'group_id' => $group,
                    'status_id' => $status_id
                ]))
                ->storeProduct()
                ->storeVariantData()
        );
    }


    public array $requiredHeading = [
        "stock_no",
        "category",
        "name",
        "selling_price",
        "unit_price",
        "date",
        "description",
        // "product_type",

        // "nos_pcs",
        // "weight",
        // "material",

    ];

    /**
     * Returns an array of validation rules for each row in the imported data.
     *
     * @return array An associative array where the keys are column names and the values are arrays of validation rules.
     */
    public function rules(): array
    {
        return [
            '*.name' => ['required', 'string'],
            '*.category' => ['required', 'string'],
            // '*.product_type' => ['required', 'string'],
            // '*.selling_price' => ['required', 'integer'],
            // '*.stock_reminder_quantity' => ['required', 'integer'],
            // '*.description' => ['required', 'string'],
            // '*.subcategory' => ['required', 'string'],
            // '*.brand' => ['required', 'string'],
            // '*.unit' => ['required', 'string'],
            // '*.group' => ['required', 'string'],
            // '*.product_type' => ['required', 'string'],


        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
