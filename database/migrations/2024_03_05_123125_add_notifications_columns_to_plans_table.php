<?php

use Kima92\ExpectorPatronum\Models\Expectation;
use Kima92\ExpectorPatronum\Models\ExpectationPlan;
use Kima92\ExpectorPatronum\Models\Group;
use Kima92\ExpectorPatronum\Models\Task;
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

        Schema::table((new ExpectationPlan)->getTable(), function (Blueprint $table) {
            $table->string('notification_email_address')->nullable();
            $table->string('notification_phone_number')->nullable();
            $table->string('notification_slack_webhook')->nullable();
            $table->string('notification_webhook')->nullable();
            $table->string('notification_pager_duty')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table((new ExpectationPlan)->getTable(), function (Blueprint $table) {
            $table->dropColumn([
                'notification_email_address',
                'notification_phone_number',
                'notification_slack_webhook',
                'notification_webhook',
                'notification_pager_duty',
            ]);
        });
    }
};
