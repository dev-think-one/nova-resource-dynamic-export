<?php

namespace NovaResourceDynamicExport;

use NovaResourceDynamicExport\Export\CustomExport;

class CustomResourcesExport
{
    /**
     * @var array<CustomResourcesExport>
     */
    public static array $exports = [];

    public static function use(string|CustomExport|array $export): static
    {
        if (is_array($export)) {
            foreach ($export as $exportItem) {
                static::use($exportItem);
            }

            return new static;
        }

        if (!is_string($export)) {
            $export = $export::class;
        }

        if (!is_subclass_of($export, CustomExport::class)) {
            throw new \Exception('Custom export should be subclass of ' . CustomExport::class);
        }

        static::$exports[] = $export;

        return new static;
    }

    public static function options(): array
    {
        $exportsList   = [];
        $exports       = static::$exports;
        if(!empty($exports)) {
            /** @var CustomExport $export */
            foreach ($exports as $export) {
                $exportsList[$export::key()] = $export::name();
            }
        }

        return $exportsList;
    }

    public static function fingByKey(string $key): ?CustomExport
    {
        $customExports = static::$exports;
        if(!empty($customExports)) {
            /** @var CustomExport $export */
            foreach ($customExports as $export) {
                if($export::key() != $key) {
                    continue;
                }

                return new $export;
            }
        }

        return null;
    }
}
