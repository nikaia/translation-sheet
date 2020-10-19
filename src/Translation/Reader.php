<?php

namespace Nikaia\TranslationSheet\Translation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Reader
{
    /**
     * Translation items.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $translations;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var string specify locale that we need to scan.
     * Returns all if no locale specified.
     */
    private $locale;
    
    /**
     * @var Path
     */
    private $path;

    /**
     * Reader.
     *
     * @param  Application  $app
     * @param  Filesystem  $files
     */
    public function __construct(Application $app, Filesystem $files)
    {
        $this->app = $app;
        $this->files = $files;
        $this->path = $this->app->make('path.lang');
    }

    /**
     * Set reader locale.
     *
     * @param $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Scan modules, app and overridden packages lang
     * and return all defined translations.
     *
     * @return Collection
     * @return array
     */
    public function scan()
    {
        // Reset
        $this->translations = new Collection;

        // App directory
        $this->scanDirectory($this->path);

        // Scan for JSON files
        $this->scanJson();

        return $this->translations;
    }

    protected function scanJsonFiles($directory)
    {
        collect($this->files->files($directory))
            ->filter(function ($fileName) {
                return Str::endsWith($fileName, ['.json']);
            })
            ->each(function (\SplFileInfo $file) {
                $locale = $file->getBasename('.json');

                $path = str_replace([resource_path('lang'), $file->getFilename()], ['', '{locale}.json'],
                    $file->getRealPath());
                $translations = json_decode(file_get_contents($file->getRealPath()), true);


                foreach ($translations as $key => $value) {

                    if (is_array($value)) {
                        dd($key, $value, 'ARRAY');
                    }
                    $entity = new Item();
                    $entity->namespace = '';
                    $entity->group = '';
                    $entity->locale = $locale;
                    $entity->key = $key;
                    $entity->full_key = $key;
                    $entity->value = (string) $value;
                    $entity->source_file = $path;

                    $this->translations->push($entity);
                }
            });
    }

    protected function scanJson()
    {

        collect($this->files->directories($this->app->make('path.lang')))
            ->each(function ($directory) {
                if ($this->isVendorDirectory($directory)) {
                    collect($this->files->directories($directory))
                        ->each(function ($directory) {
                            $this->scanJsonFiles($directory);
                        });
                }
            });

        $this->scanJsonFiles($this->app->make('path.lang'));
    }

    /**
     * Scan a directory.
     *
     * @param  string  $path  to directory to scan
     */
    protected function scanDirectory($path)
    {
        foreach ($this->files->directories($path) as $directory) {
            if ($this->isVendorDirectory($directory)) {
                $this->scanVendorDirectory($directory);
            } else {
                $this->loadTranslationsInDirectory($directory, $this->getLocaleFromDirectory($directory), null);
            }
        }
    }

    /**
     * Scan overridden packages lang.
     *
     * @param $vendorsDirectory
     */
    private function scanVendorDirectory($vendorsDirectory)
    {
        foreach ($this->files->directories($vendorsDirectory) as $vendorPath) {
            $namespace = basename($vendorPath);
            foreach ($this->files->directories($vendorPath) as $localePath) {
                $this->loadTranslationsInDirectory($localePath, basename($localePath), $namespace);
            }
        }
    }

    /**
     * Load all directory file translation (multiple group) into translations collection.
     *
     * @param $directory
     * @param $locale
     * @param $namespace
     */
    private function loadTranslationsInDirectory($directory, $locale, $namespace)
    {
        if (!$this->requestedLocale($locale)) {
            return;
        }

        foreach ($this->files->files($directory) as $file) {
            $info = pathinfo($file);
            $sub_folder = explode($this->path."/".$locale."/", $directory)[1] ?? false;
            $group = $sub_folder ? $sub_folder."/".$info['filename'] : $info['filename'];
            $this->loadTranslations($locale, $group, $namespace, $file);
        }
        
        foreach($this->files->directories($directory) as $sub_directory){
            $this->loadTranslationsInDirectory($sub_directory, $locale, $namespace);
        }
    }

    /**
     * Load file translation (group) into translations collection.
     *
     * @param $locale
     * @param $group
     * @param $namespace
     * @param $file
     */
    private function loadTranslations($locale, $group, $namespace, $file)
    {
        $translations = Arr::dot($this->app['translator']->getLoader()->load($locale, $group, $namespace));

        foreach ($translations as $key => $value) {

            // Avoid break in this case :
            // Case of 'car.messages = []', the key 'messages' is specified in the translation
            // file but no items defined inside.
            if (is_array($value) && count($value) === 0) {
                continue;
            }

            $entity = new Item;
            $entity->namespace = $namespace;
            $entity->locale = $locale;
            $entity->group = $group;
            $entity->key = $key;
            $entity->full_key = $this->fullKey($namespace, $group, $key);
            $entity->value = (string) $value;
            $entity->source_file = $this->sourceFile($file);

            $this->translations->push($entity);
        }
    }

    /**
     * Return a full lang key.
     *
     * @param $namespace
     * @param $group
     * @param $key
     * @return string
     */
    private function fullKey($namespace, $group, $key)
    {
        return
            ($namespace ? "$namespace::" : '')
            .$group.'.'
            .$key;
    }

    /**
     * Return relative path of language file.
     *
     * @param $file
     * @return mixed
     */
    private function sourceFile($file)
    {
        return $this->toRelative(realpath($file));
    }

    /**
     * Return relative path related to base_path().
     *
     * @param $path
     * @return string
     */
    private function toRelative($path)
    {
        $relative = str_replace($this->app->make('path.lang').DIRECTORY_SEPARATOR, '', $path);
        $relative = str_replace('\\', '/', $relative);

        return $relative;
    }

    /**
     *  Determine if a found locale is requested for scanning.
     *  If $this->locale is not set, we assume that all the locales were requested.
     *
     * @param  string  $locale  the locale to check
     * @return bool
     */
    private function requestedLocale($locale)
    {
        if (empty($this->locale)) {
            return true;
        }

        return $locale === $this->locale;
    }

    /**
     * Return locale from directory
     *  ie. resources/lang/en -> en.
     *
     * @param $directory
     * @return string
     */
    private function getLocaleFromDirectory($directory)
    {
        return basename($directory);
    }

    /**
     * Return true if it is the vendor directory.
     *
     * @param $directory
     * @return bool
     */
    private function isVendorDirectory($directory)
    {
        return basename($directory) === 'vendor';
    }
}
