<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Vtv\Media\Models\File;
use Vtv\News\Repositories\Interfaces\NewInterface;
use Vtv\News\Models\News;

class ScheduleRenderFileName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'render:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $fileRepository;
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(NewInterface $fileRepository)
    {
        parent::__construct();
        $this->fileRepository = $fileRepository;

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $news = News::all();
        foreach($news as $new){
            $image = str_replace('http://api-base.local.ub/storage/', '', $new->image);
            $data['image'] = $image;
            $this->fileRepository->update(['id' => $new->id], $data);
        }
        $this->info('Done');
    }
}
