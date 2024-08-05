<?php

namespace App\Http\Controllers\Pos\Api\Product\import;

use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\HeadingRowImport;

class ProductImportController extends Controller
{
    public function importProducts(Request $request): \Illuminate\Http\Response|array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {

        $request->validate([
            'import_file' => 'file|mimes:csv,txt|required'
        ]);

        

        $file = $request->file('import_file');
        //count row number
        $rows = count(array_map('str_getcsv', file($file)));

        throw_if($rows > 501,
            ValidationException::withMessages([
                'import_file' => [__t('maximum_row_exceeded_message')]
            ])
        );

        $import = new ProductsImport();
        $headings = (new HeadingRowImport)->toArray($file);

        

        $missingField = array_diff($import->requiredHeading, $headings[0][0]);

        if (count($missingField) > 0) {
            return response(collect($missingField)->values(), 423);
        }

        $import->import($file);
        $failures = $import->failures();

       


        //partial import
        if ($failures->count() > 0) {
            $stat = import_failed($file, $failures);

            //store all failed data...

            return [
                'status' => 200,
                'message' => trans('default.partially_imported',[
                    'subject' => __t('products')
                ]),
                'stat' => $stat,
                'failures' => $failures[0]->values(),
            ];
        }

        return [
            'status' => 200,
            'message' => trans('default.has_been_imported_successfully',[
                'subject' => __t('products')
            ])
        ];
    }










}