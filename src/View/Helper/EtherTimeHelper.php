<?php
namespace App\View\Helper;

use Cake\I18n\Time;
use Cake\View\Helper\TimeHelper;

class EtherTimeHelper extends TimeHelper
{
    public function abbreviatedTimeAgoInWords($time)
    {
        $time = new Time($time);
        $timeAgo = $time->timeAgoInWords(['end' => '+10 years']);
        if (strpos($timeAgo, ',') !== false) {
            $firstCommaPos = strpos($timeAgo, ',');

            // If the largest unit is years, display "Y years ago" or "Y years, M months ago"
            if (stripos($timeAgo, 'year') !== false) {
                $offset = $firstCommaPos + 1;
                $secondCommaPos = strpos($timeAgo, ',', $offset);
                $comma_pos = $secondCommaPos === false ? $firstCommaPos : $secondCommaPos;
                $timeAgo = substr($timeAgo, 0, $comma_pos);

            // Otherwise, only use the largest applicable unit of time (e.g. "4 hours ago")
            } else {
                $timeAgo = substr($timeAgo, 0, $firstCommaPos);
            }
            if (stripos($timeAgo, 'ago') === false) {
                $timeAgo .= ' ago';
            }
        }
        return $timeAgo;
    }
}