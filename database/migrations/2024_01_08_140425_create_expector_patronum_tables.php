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
        Schema::create($gr = (new Group)->getTable(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color');
            $table->timestamps();
        });
        Schema::create($ep = (new ExpectationPlan)->getTable(), function (Blueprint $table) use ($gr) {
            $table->id();
            $table->string('name')->index();
            $table->string('schedule');
            $table->foreignIdFor(Group::class)->constrained($gr)->cascadeOnDelete();
            $table->text('rules');
            $table->timestamps();
        });

        Schema::create($t = (new Task)->getTable(), function (Blueprint $table) use ($ep) {
            $table->id();
            $table->foreignIdFor(ExpectationPlan::class)->constrained($ep)->cascadeOnDelete();
            $table->string('uuid')->index();
            $table->dateTime('started_at')->index();
            $table->dateTime('ended_at')->index()->nullable();
            $table->timestamps();
        });

        Schema::create((new Expectation)->getTable(), function (Blueprint $table) use ($ep, $t) {
            $table->id();
            $table->foreignIdFor(ExpectationPlan::class)->constrained($ep)->cascadeOnDelete();
            $table->foreignIdFor(Task::class)->nullable()->constrained($t)->cascadeOnDelete();
            $table->integer('status');
            $table->dateTime('expected_start_date');
            $table->dateTime('expected_end_date')->nullable();
            $table->text('checks_results');
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
        Schema::dropIfExists((new Expectation)->getTable());
        Schema::dropIfExists((new Task)->getTable());
        Schema::dropIfExists((new ExpectationPlan)->getTable());
        Schema::dropIfExists((new Group)->getTable());
    }
};
