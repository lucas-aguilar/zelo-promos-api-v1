<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromoSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('promotion_id');
            $table->string('mailchimp_lead_id');
            $table->string('lead_email');
            $table->string('lead_name');
            $table->string('lead_lastname');
            $table->string('lead_phone');
            $table->string('total_sale_with_discount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promo_sales');
    }
}
