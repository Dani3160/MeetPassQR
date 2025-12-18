<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateEventSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:generate-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate slugs for existing events that don\'t have slugs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating slugs for events...');

        $events = Event::whereNull('slug')->orWhere('slug', '')->get();
        
        if ($events->isEmpty()) {
            $this->info('No events need slug generation.');
            return 0;
        }

        $bar = $this->output->createProgressBar($events->count());
        $bar->start();

        $generated = 0;
        foreach ($events as $event) {
            $slug = $this->generateUniqueSlug($event->name_event, $event->id_event);
            $event->slug = $slug;
            $event->save();
            $generated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully generated {$generated} slug(s).");

        return 0;
    }

    /**
     * Generate unique slug from name
     */
    protected function generateUniqueSlug(string $name, int $excludeId): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Event::where('slug', $slug)
            ->where('id_event', '!=', $excludeId)
            ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
