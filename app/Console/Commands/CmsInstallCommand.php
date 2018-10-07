<?php

namespace App\Console\Commands;

use Artisan;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use DB;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Question\Question;
use Exception;

class CmsInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installation of VTV CMS: Laravel setup, installation of npm packages';


    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    private $username;

    /**
     * @var
     */
    private $database;

    /**
     * @var
     */
    private $password;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * @throws Exception
     */
    public function handle()
    {
        $this->line('------------------');
        $this->line('Welcome to VTV CMS');
        $this->line('------------------');

        $extensions = get_loaded_extensions();
        $require_extensions = ['mbstring', 'openssl', 'curl', 'exif', 'fileinfo', 'tokenizer'];
        foreach (array_diff($require_extensions, $extensions) as $missing_extension) {
            $this->error('Missing ' . ucfirst($missing_extension) . ' extension');
        }

        if (!file_exists('.env')) {
            File::copy('.env-example', '.env');
        }
        // Set database credentials in .env and migrate
        $this->setDatabaseInfo();
        $this->line('------------------');

        Artisan::call('key:generate');

    }

    /**
     * @throws Exception
     * @return void
     * @author Toinn
     */
    private function setDatabaseInfo()
    {
        $this->info('Setting up database (please make sure you created database for this site)...');

        $this->database = env('DB_DATABASE');
        $this->username = env('DB_USERNAME');
        $this->password = env('DB_PASSWORD');
        while (is_null($this->database)) {
            // Ask for database name
            $this->database = $this->ask('Enter a database name', $this->guessDatabaseName());

            $this->username = $this->ask('What is your MySQL username?', 'root');

            $question = new Question('What is your MySQL password?', '<none>');
            $question->setHidden(true)->setHiddenFallback(true);
            $this->password = (new SymfonyQuestionHelper())->ask($this->input, $this->output, $question);
            if ($this->password === '<none>') {
                $this->password = '';
            }

            // Update DB credentials in .env file.
            $contents = $this->getKeyFile();
            $contents = preg_replace('/(' . preg_quote('DB_DATABASE=') . ')(.*)/', 'DB_DATABASE=' . $this->database, $contents);
            $contents = preg_replace('/(' . preg_quote('DB_USERNAME=') . ')(.*)/', 'DB_USERNAME=' . $this->username, $contents);
            $contents = preg_replace('/(' . preg_quote('DB_PASSWORD=') . ')(.*)/', 'DB_PASSWORD=' . $this->password, $contents);

            if (!$contents) {
                throw new Exception('Error while writing credentials to .env file.');
            }
            // Write to .env
            $this->files->put('.env', $contents);

            // Set DB username and password in config
            $this->laravel['config']['database.connections.mysql.username'] = $this->username;
            $this->laravel['config']['database.connections.mysql.password'] = $this->password;

            // Clear DB name in config
            unset($this->laravel['config']['database.connections.mysql.database']);

            if (!$this->check_database_connection()) {
                $this->error('Can not connect to database, please try again!');
            } else {
                $this->info('Connect to database successfully!');
            }
        }

        if (!empty($this->database)) {
            // Force the new login to be used
            DB::purge();

            // Switch to use {$this->database}
            DB::unprepared('USE `' . $this->database . '`');
            DB::connection()->setDatabaseName($this->database);

            $this->info('Import default database...');
            //DB::unprepared(file_get_contents(base_path() . '/database/dump/base.sql'));
            Artisan::call('config:cache');
            $this->line('------------------');
            $this->line('Done. Enjoy VTV CMS!');
        }
    }

    /**
     * @return bool
     */
    private function check_database_connection()
    {
        try {
            DB::connection()->reconnect();
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
    /**
     * Guess database name from app folder.
     *
     * @return string
     * @author Toinn
     */
    private function guessDatabaseName()
    {
        try {
            $segments = array_reverse(explode(DIRECTORY_SEPARATOR, app_path()));
            $name = explode('.', $segments[1])[0];

            return str_slug($name);
        } catch (Exception $e) {
            return '';
        }
    }

    private function getKeyFile()
    {
        return $this->files->exists('.env') ? $this->files->get('.env') : $this->files->get('.env.example');
    }

}
