<?php

namespace Nikaia\TranslationSheet\Translation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;

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
     * @var TranslationsSheet
     */
    protected $translationSheet;

    /**
     * Reader.
     *
     * @param Application $app
     * @param Filesystem $files
     */
    public function __construct(Application $app, Filesystem $files)
    {
        $this->app = $app;
        $this->files = $files;
    }

    /**
     * Set the translation sheets for reader.
     *
     * @param TranslationsSheet $translationSheet
     * @return Reader
     */
    public function setTranslationsSheet(TranslationsSheet $translationSheet)
    {
        $this->translationSheet = $translationSheet;

        return $this;
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
        $this->translations = new Collection;

        $this->scanDirectory($this->translationSheet->getPath());

        return $this->translations;
    }

    /**
     * Scan a directory.
     *
     * @param string $path to directory to scan
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

        foreach ($this->files->files($path) as $file) {
            $this->loadTranslations($file->getBasename('.json'), '*', '*', $file);
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
            $group = $info['filename'];
            $this->loadTranslations($locale, $group, $namespace, $file);
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
        if ($this->translationSheet->isExtraSheet()) {
            $translations = Arr::dot(json_decode(file_get_contents($file), true));
        } else {
            $translations = Arr::dot($this->app['translator']->getLoader()->load($locale, $group, $namespace));
        }

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
            $entity->value = (string)$value;
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
            . $group . '.'
            . $key;
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
        $relative = str_replace($this->translationSheet->getPath() . DIRECTORY_SEPARATOR, '', $path);
        $relative = str_replace('\\', '/', $relative);

        return $relative;
    }

    /**
     *  Determine if a found locale is requested for scanning.
     *  If $this->locale is not set, we assume that all the locales were requested.
     *
     * @param string $locale the locale to check
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
