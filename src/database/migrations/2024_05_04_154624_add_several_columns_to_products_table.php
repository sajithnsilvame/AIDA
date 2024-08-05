<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('stock_no')->nullable()->after('duration_id');
            $table->string('material')->nullable()->after('stock_no');
            $table->string('nos_pcs')->nullable()->after('material');
            $table->string('weight')->nullable()->after('nos_pcs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock_no');
            $table->dropColumn('material');
            $table->dropColumn('nos_pcs');
            $table->dropColumn('weight');
        });
    }
};
