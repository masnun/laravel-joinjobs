<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AutoDeleteAndCompletedAtFields extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('masnun_joins', function($table)
        {
            $table->boolean('auto_delete');
            $table->dateTime('created_at');
            $table->dateTime('completed_at')->nullable();

        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('masnun_joins', function($table)
        {
            $table->dropColumn('auto_delete', 'created_at', 'completed_at');
        });
	}

}
