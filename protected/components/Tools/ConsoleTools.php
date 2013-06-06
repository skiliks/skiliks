<?php

class ConsoleTools
{
    public static function table(array $columns, array $data)
    {
        $rows = ['+', '|', '+'];
        $widths = [];
        foreach ($columns as $column => $width) {
            $widths[] = $width = max(strlen($column), $width);
            $rows[0] .= str_pad('+', $width + 3, '-', STR_PAD_LEFT);
            $rows[1] .= str_pad($column, $width + 2, ' ', STR_PAD_BOTH) . '|';
            $rows[2] .= str_pad('+', $width + 3, '-', STR_PAD_LEFT);
        }

        foreach ($data as $i => $line) {
            $rows[$i + 3] = '|';
            foreach ($line as $k => $cell) {
                $rows[$i + 3] .= ' ' . str_pad($cell, $widths[$k]) . ' |';
            }
        }

        $rows[] = $rows[2];
        return implode("\n", $rows);
    }
}