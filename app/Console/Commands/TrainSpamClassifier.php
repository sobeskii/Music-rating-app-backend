<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use stdClass;
use TeamTNT\TNTSearch\Classifier\TNTClassifier;

class TrainSpamClassifier extends Command
{

    private const PATH_TO_TRAIN_FILE = '/train_data/spam.csv';
    private const PATH_TO_CLASSIFIER = 'classifiers/spam.cls';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'train:spam-classifier';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trains spam classifier';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $comments = $this->loadCSV();

        $classifier = new TNTClassifier();

        foreach ($comments as $comment) {
            $classifier->learn($comment->text, ($comment->category == 'spam') ? 1 : 0);
        }

        $classifier->save(self::PATH_TO_CLASSIFIER);
    }

    private function loadCSV(): array
    {
        $csvData = [];
        $category = 'category';
        $text = 'text';
        if (($open = fopen(storage_path() . self::PATH_TO_TRAIN_FILE, "r")) !== FALSE) {

            while (($data = fgetcsv($open, 1000, ",")) !== FALSE) {
                $object = new stdClass();
                $object->{$category} = $data[0];
                $object->{$text} = $data[1];

                $csvData[] = $object;
            }

            fclose($open);
        }
        return $csvData;
    }
}
