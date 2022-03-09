<?php

if (!function_exists('electionStatus'))
{
    function electionStatus($status) {
        switch ($status) {
            case "waiting":
                $string = '<span class="badge badge-info">Not Yet Started</span>';
                break;
            case "ongoing":
                $string = '<span class="badge badge-primary">On-going</span>';
            break;
            default:
                $string = '<span class="badge badge-success">Finish</span>';
            break;
        }

        return $string;
    }
}
