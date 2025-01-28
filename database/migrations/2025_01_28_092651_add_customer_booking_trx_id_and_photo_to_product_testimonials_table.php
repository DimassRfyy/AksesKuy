<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_testimonials', function (Blueprint $table) {
            $table->string('customer_booking_trx_id')->after('product_id')->nullable();
            $table->string('photo')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_testimonials', function (Blueprint $table) {
            $table->dropColumn('customer_booking_trx_id');
            $table->dropColumn('photo');
        });
    }
};
