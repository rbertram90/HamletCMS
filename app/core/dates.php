<?php
namespace rbwebdesigns;

/******************************************************************
    Class DateFormatter
    - Date Formatting
******************************************************************/

class DateFormatter {
    
    public function __construct() {
    }
    
    /**
        function getAgeInYears
        @param date <string> DD-MM-YYYY
        @return <integer> Difference in years from now
    **/
    public function getAgeInYears($date) {
        $targetYear = date("Y", strtotime($date));
        $targetMonthDay = 0 + date("nd", strtotime($date));
        $currentMonthDay = 0 + date("nd");
        
        // Find the difference in years
        $targetDate = date("Y") - $targetYear;

        if($targetMonthDay > $currentMonthDay) {
            // If months are not equal
            $targetDate--;
        }
        
        return $targetDate;
    }
    
    public static function formatFriendlyTime($pdTimestamp) {
        
        $now = new \DateTime('now');
        $target = new \DateTime($pdTimestamp);
        
        // Find Differences
        $difference = $target->diff($now);
        $diff_years = $difference->format('%y');
        $diff_months = $difference->format('%m');
        $diff_days = $difference->format('%a');
        $diff_hours = $difference->format('%H');
        $diff_minutes = $difference->format('%i');
        
        // Is this in the future?
        if($now > $target) {
            // past
        
            if($diff_years > 1) {
                // Many Years
                return $diff_years.' years ago';
                
            } elseif($diff_years == 1) {
                // One Year
                return '1 year ago';
                
            } elseif($diff_months > 1) {
                // Many Months
                return $diff_months.' months ago';
                
            } elseif($diff_months == 1) {
                // One Month
                return '1 month ago';
                
            } elseif($diff_days > 1) {
                // Many Days
                return $diff_days.' days ago';
                
            } elseif($diff_days == 1) {
                // One Days
                return '1 day ago';
                
            } elseif($diff_hours > 1) {
                // Many Hours
                return $diff_hours.' hours ago';
                
            } elseif($diff_hours == 1) {
                // One Hour
                return '1 hour ago';
                
            } elseif($diff_minutes > 1) {
                // Many Hours
                return $diff_minutes.' minutes ago';
                
            } elseif($diff_minutes == 1) {
                // One Hour
                return '1 minute ago';
                
            } else {
                // An number of seconds - who cares how many!
                return 'A few seconds ago';
            }
        
        } else {
             // future
            if($diff_years > 1) {
                // Many Years
                return 'In '.$diff_years.' years\' from now';
                
            } elseif($diff_years == 1) {
                // One Year
                return 'In a year from now';
                
            } elseif($diff_months > 1) {
                // Many Months
                return 'In '.$diff_months.' months\' from now';
                
            } elseif($diff_months == 1) {
                // One Month
                return 'In 1 month from now';
                
            } elseif($diff_days > 1) {
                // Many Days
                return 'In '.$diff_days.' days\' from now';
                
            } elseif($diff_days == 1) {
                // One Days
                return 'In 1 day from now';
                
            } elseif($diff_hours > 1) {
                // Many Hours
                return 'In '.$diff_hours.' hours\' from now';
                
            } elseif($diff_hours == 1) {
                // One Hour
                return 'In an hour from now';
                
            } elseif($diff_minutes > 1) {
                // Many Hours
                return 'In '.$diff_minutes.' minutes\' from now';
                
            } elseif($diff_minutes == 1) {
                // One Hour
                return 'In a minute from now';
                
            } else {
                // An number of seconds - who cares how many!
                return 'In a few second\'s from now';
            }
        }
    }
    
    public function quickFormatDate($date, $format = "D, jS F Y") {
        return date($format, strtotime($date));
    }
    
    public function quickFormatTime($time, $format = "g:ia") {
        return date($format, strtotime($time));
    }
}
?>