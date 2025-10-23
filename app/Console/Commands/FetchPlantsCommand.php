<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PerenualPlantService;

class FetchPlantsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plants:fetch {--max= 100 Number of plants to fetch from the API}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store plants from the Perenual API';

    /**
     * Execute the console command.
     */
    public function handle(PerenualPlantService $plantService)
    {
        $maxRequests = (int) $this->option('max');

        $this->info("Starting plant sync from Perenual API...");

        $stats = $plantService->fetchAndStorePlants($maxRequests);

        $this->info("✅ Plants fetched successfully");
        $this->line("Processed: {$stats['processed']}");
        $this->line("Created: {$stats['created']}");
        $this->line("Updated: {$stats['updated']}");
        $this->line("Errors: {$stats['errors']}");

        return Command::SUCCESS;
    }
}
