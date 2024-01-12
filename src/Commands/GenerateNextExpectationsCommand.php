<?php

namespace Kima92\ExpectorPatronum\Commands;

use Kima92\ExpectorPatronum\Expector;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class GenerateNextExpectationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expector-patronum:generate-next-expectations';

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
    public function handle(Expector $expector)
    {
        $startTime = CarbonImmutable::today()->setSeconds(0)->setMinutes(0);

        $expector->generateNextExpectations($startTime, $startTime->addDay());

        return Command::SUCCESS;
    }
}
