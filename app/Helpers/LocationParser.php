<?php

namespace App\Helpers;


class LocationParser
{
    public function parse($input)
    {
        $input = $this->cleanInput($input);

        if ($this->isUS($input)) {
            return "US";
        }

        return null;
    }

    private function isUS($input)
    {
        return (bool) preg_match('@Must be authorized to work in the (U.S.|United states)@i', $input);
    }

    private function cleanInput($input)
    {
        return strip_tags($input);
    }
}
