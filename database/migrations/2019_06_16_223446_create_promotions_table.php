<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title');
            $table->text('description');
            $table->text('rules');
            $table->double('unit_cost');
            $table->bigInteger('location_id');
            $table->string('internal_title', 200)->nullable();
            $table->dateTime('end_datetime')->nullable();
            $table->boolean('enabled')->default(false);
            $table->boolean('new_leads_exclusive')->default(true);
            $table->integer('qty_limit')->nullable();
            $table->string('lp_link_alias', 200);
            $table->string('mch_tag_reserved_id')->nullable();
            $table->string('mch_tag_reserved_name')->nullable();
            $table->string('mch_tag_sold_id')->nullable();
            $table->string('mch_tag_sold_name')->nullable();
            $table->string('mch_pre_sale_automation_id')->nullable();
            $table->string('mch_after_sale_automation_id')->nullable();
            $table->string('image_hash_name', 200)->nullable();
            $table->string('mailchimp_campaign_id');
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
        Schema::dropIfExists('promotions');
    }
}
