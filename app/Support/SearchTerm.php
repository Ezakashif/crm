<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

class SearchTerm
{
    /**
     * Build a LIKE pattern that treats user input as literals (escapes % and _).
     */
    public static function like(string $term): string
    {
        $escaped = str_replace(
            ['\\', '%', '_'],
            ['\\\\', '\\%', '\\_'],
            trim($term),
        );

        return '%'.$escaped.'%';
    }

    /**
     * Apply an escaped LIKE comparison that works on MySQL and SQLite.
     */
    public static function whereEscaped(Builder $query, string $column, string $term, string $boolean = 'and'): Builder
    {
        $pattern = self::like($term);

        $method = $boolean === 'or' ? 'orWhereRaw' : 'whereRaw';

        return $query->{$method}("{$column} LIKE ? ESCAPE '\\'", [$pattern]);
    }
}
