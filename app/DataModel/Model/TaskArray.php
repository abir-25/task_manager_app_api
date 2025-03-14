<?php

namespace App\DataModel\Model;


class TaskArray {
    public static $uniqueKey = '3295f05e676d4099bad8b26953cf858b';
    public static $apiSecretKey = 'tms@25';
    public static $apiTimeToLive = 63113852;

    public static $taskStatus = array(
        'To Do' => 0,
        'In Progress' => 1,
        'Done' => 2
    );
}
