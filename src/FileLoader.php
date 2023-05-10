<?php

namespace One23\LaravelTranslationYaml;

use RuntimeException;

class FileLoader extends \Illuminate\Translation\FileLoader
{
    protected static array $cache = [];

    protected $yamlPaths = [];

    public function addYamlPath($path)
    {
        $this->yamlPaths[] = $path;
    }

    public function yamlPaths()
    {
        return $this->yamlPaths;
    }

    protected function tryLoad(string $path, string $ext = null) {
        $crc = md5($path);
        if (isset(self::$cache[$crc])) {
            return self::$cache[$crc];
        }

        $res = null;

        if (
            (is_null($ext) || $ext === 'php')
            && $this->files->exists($full = "{$path}.php")
        ) {
            $res = $this->files->getRequire($full);
        }

        //

        if (
            is_null($res)
            && (is_null($ext) || $ext === 'json')
            && $this->files->exists($full = "{$path}.json")
        ) {
            $decoded = json_decode($this->files->get($full), true);

            if (is_null($decoded) || json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException("Translation file [{$full}] contains an invalid JSON structure.");
            }

            $res = $decoded;
        }

        //

        if (
            is_null($res)
            && (is_null($ext) || $ext === 'yaml' || $ext === 'yml')
            && (
                $this->files->exists($full = "{$path}.yaml")
                || $this->files->exists($full = "{$path}.yml")
            )
        ) {
            $decoded = yaml_parse($this->files->get($full));

            if ($decoded === false) {
                throw new RuntimeException("Translation file [{$full}] contains an invalid YAML structure.");
            }

            $res = $decoded;
        }

        //

        self::$cache[$crc] = $res;

        return $res;
    }

    protected function loadYamlPaths(string $locale)
    {
        return collect(array_merge($this->yamlPaths, $this->paths))
            ->reduce(function ($output, $path) use ($locale) {
                $res = $this->tryLoad("{$path}/{$locale}", 'yaml');

                if (is_array($res)) {
                    $output = array_merge($output, $res);
                }

                return $output;
            }, []);
    }

    //

    public function load($locale, $group, $namespace = null)
    {
        if ($group === '*' && $namespace === '*') {
            return $this->loadYamlPaths($locale);
        }

        return parent::load($locale, $group, $namespace);
    }

    protected function loadNamespaceOverrides(array $lines, $locale, $group, $namespace)
    {
        return collect($this->paths)
            ->reduce(function ($output, $path) use ($lines, $locale, $group, $namespace) {
                $res = $this->tryLoad("{$path}/vendor/{$namespace}/{$locale}/{$group}");
                if (is_array($res)) {
                    $lines = array_replace_recursive($lines, $res);
                }

                return $lines;
            }, []);
    }

    protected function loadPaths(array $paths, $locale, $group)
    {
        return collect($paths)
            ->reduce(function ($output, $path) use ($locale, $group) {
                $res = $this->tryLoad("{$path}/{$locale}/{$group}");
                if (is_array($res)) {
                    $output = array_replace_recursive($output, $res);
                }

                return $output;
            }, []);
    }

}
