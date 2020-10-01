<?php

namespace App\Jobs;

use App\Models\BidBondTemplate;
use Illuminate\Support\Facades\File;

class DeleteBidTemplates extends Job
{

    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $templates = BidBondTemplate::pluck('secret');
        $generated_directory = resource_path() . "/views/generated/bidbond_templates";
        $generated_templates = File::allFiles($generated_directory);
        $preview_directory = resource_path() . "/views/preview/bidbond_templates";
        $preview_templates = File::allFiles($preview_directory);


        $this->deleteTemplate($templates, $generated_directory, $generated_templates);

        $this->deleteTemplate($templates, $preview_directory, $preview_templates);
    }


    private function deleteTemplate($templates, string $directory, array $bid_templates): void
    {
        $templates->each(function ($template) use ($directory, &$paths) {
            $paths[] = "$directory/$template.blade.php";
        });

        foreach ($bid_templates as $template) {
            if (!in_array($template->getPathname(), $paths)) {
                File::delete($template->getPathname());
            }
        }
    }


}
