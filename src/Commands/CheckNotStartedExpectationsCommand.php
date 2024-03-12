<?php

namespace Kima92\ExpectorPatronum\Commands;

use Kima92\ExpectorPatronum\Expector;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Kima92\ExpectorPatronum\Patronum;

class CheckNotStartedExpectationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expector-patronum:check-not-started';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Patronum $patronum)
    {
        if (config("expector-patronum.isActive")) {
            $patronum->markNotStartedAsFailed();
        }

        return Command::SUCCESS;
    }
}
