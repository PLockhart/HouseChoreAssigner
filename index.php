<?php

require('Twillio/Services/Twilio.php'); 
require("twillioDetails.php");
require("JobProcessor.php");
require("SendTwillioTextToNumber.php");
require("SchedulePrinter.php");

$twillioClient = new Services_Twilio($account_sid, $auth_token); 

class Flatmate {
    
    public $Name;
    public $MobileNum;
    
    function __construct($name, $number) {
        
        $this->Name = $name;
        $this->MobileNum = $number;
    }
}

class Job {
    
    public $Title;
    public $Day;
    public $SpecialWeekOffset;
    public $SpecialWeekText;
    
    function __construct($title, $day, $specialWeekOffset = -1, $specialWeekText = NULL) {
        
        $this->Title = $title;
        $this->Day = $day;
        $this->SpecialWeekOffset = $specialWeekOffset;
        $this->SpecialWeekText = $specialWeekText;
    }
}

$OP_MODE = isset($argv) ? $argv[1] : NULL;

$flatmatesFile = fopen("Flatmates.txt", "r");
$flatmates = array();

if ($flatmatesFile)
{
    while (($line = fgetcsv($flatmatesFile)) !== FALSE) {
        
        array_push($flatmates, new Flatmate($line[0], $line[1]));
    }
}
else
{
    throw new Exception("Could not load Flatmates file");
}

$jobsFile = fopen("Jobs.txt", "r");
$jobs = array();

$dowMap = array('Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6, 'Sunday' => 7, );
$daysOfWeekStrings = array_keys($dowMap);

if ($jobsFile)
{
    while (($line = fgetcsv($jobsFile)) !== FALSE) {
        
        $dayIndex = $dowMap[$line[0]];

        if (!$dayIndex)
        {
            throw new Exception("Could not work out what day of the week $line[1] is");
        }

        $newJob = new Job($line[1], $dayIndex);

        if (isset($line[2]) && isset($line[3]))
        {
            $newJob->SpecialWeekOffset = $line[2];
            $newJob->SpecialWeekText = $line[3];
        }
        array_push($jobs, $newJob);
    }
}
else
{
    throw new Exception("Could not load Jobs file");
}

if (!count($flatmates))
{
    throw new Exception("No flatmates could be loaded from Flatmates.txt");
}

$todaysDate = new \DateTime();
$weekNumber = $todaysDate->format('W');
$dayNumber = $todaysDate->format('N');

$todaysAssignedJobs = array();
$numJobsToday = GetAssignedJobs($flatmates, $jobs, $dayNumber, $weekNumber, $todaysAssignedJobs);

if ($OP_MODE != "SMS")
{
    echo "<h1>Today</h2>";
}

if ($numJobsToday == 0 && $OP_MODE != "SMS")
{
    echo "No jobs to do today";
}
else
{
    //go through each flat mate and tell them of their new duties
    foreach ($todaysAssignedJobs as $flatmateIndex => $jobsToDo)
    {
        $flatemate = $flatmates[$flatmateIndex];
        $message = "";

        foreach ($jobsToDo as $loopedJob)
        {
            $message .= $loopedJob->Title;

            if ($loopedJob->SpecialWeekOffset != -1 && $weekNumber % 2 ==$loopedJob->SpecialWeekOffset)
            {
                $message .= ", " . $loopedJob->SpecialWeekText;
            }

            if ($OP_MODE == "SMS")
            {
                $message .= "\r\n";
            }
            else
            {
                $message .= "<br>";
            }
        }

        if ($OP_MODE == "SMS")
        {
            sendMessageToNumber($flatemate->MobileNum, $message, $twillioClient, $myNumber);
        }
        else
        {
            echo "<h2>" . $flatemate->Name . "</h2>";
            echo $message . "<br><br>";
        }
    }
}

if ($OP_MODE != "SMS")
{
    echo "<br>";
    
    $futureWeekNumber = $weekNumber;
    $futureDayNumber = $dayNumber;
    //print the next 7 days of schedule
    for ($i = 0; $i < 7; ++$i)
    {
        $futureDayNumber++;
        if ($futureDayNumber > 7)
        {
            $futureDayNumber = 1;
            $futureWeekNumber++;
        }
        
        $futureAssignedJobs = array();
        $numJobsFuture = GetAssignedJobs($flatmates, $jobs, $futureDayNumber, $futureWeekNumber, $futureAssignedJobs);
        
        if ($numJobsFuture > 0)
        {
            echo "<h1> " . $daysOfWeekStrings[$futureDayNumber - 1] . "</h2>";
            PrintSchedule($futureAssignedJobs, $flatmates, $futureWeekNumber, "<br>");
            echo "<br><br>";
        }
    }
}

