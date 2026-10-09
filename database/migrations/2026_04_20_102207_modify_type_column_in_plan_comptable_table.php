<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTypeColumnInPlanComptableTable extends Migration
{
    public function up()
    {
        Schema::table('plan_comptable', function (Blueprint $table) {
            $table->string('type', 50)->change();
        });
    }

    public function down()
    {
        Schema::table('plan_comptable', function (Blueprint $table) {
            $table->string('type', 20)->change();
        });
    }
}