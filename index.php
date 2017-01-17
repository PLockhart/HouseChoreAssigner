<?php

require('Twillio/Services/Twilio.php'); 
require("twillioDetails.php");
require("sendTwillioTextToNumber.php");

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

$todaysDate = new \DateTime();
$weekNumber = $todaysDate->format('W');
$dayNumber = $todaysDate->format('N');

if ($jobsFile)
{
    while (($line = fgetcsv($jobsFile)) !== FALSE) {
        
        $dayIndex = $dowMap[$line[0]];

        if (!$dayIndex)
        {
            throw new Exception("Could not work out what day of the week $line[1] is");
        }
        if ($dayIndex == $dayNumber)
        {
            $newJob = new Job($line[1], $dayIndex);

            if (isset($line[2]) && isset($line[3]))
            {
                $newJob->SpecialWeekOffset = $line[2];
                $newJob->SpecialWeekText = $line[3];
            }
            array_push($jobs, $newJob);
        }
    }
}
else
{
    throw new Exception("Could not load Jobs file");
}

if (!count($jobs))
{
    echo "No jobs to do today";
}

if (!count($flatmates))
{
    throw new Exception("No flatmates could be loaded from Flatmates.txt");
}

if (count($jobs) && count($flatmates))
{
    $flatmateOffset = $weekNumber % count($flatmates);

    //shift through the flatmates so each time they take turns doing chores
    while ($flatmateOffset > 0) {

        $poppedRoomate = array_pop($flatmates);
        array_unshift($flatmates, $poppedRoomate);

        $flatmateOffset--;
    }
    
    $flatmateIndex = 0;
    $flatmateCount = count($flatmates);
    
    //go through all of the jobs and assign to a flat mate
    $assignedJobs = array();
    
    foreach ($jobs as $loopedJob)
    {
        if (!isset($assignedJobs[$flatmateIndex]))
        {
            $assignedJobs[$flatmateIndex] = array();
        }
        
        array_push($assignedJobs[$flatmateIndex], $loopedJob);
        
        $flatmateIndex = ($flatmateIndex + 1) % $flatmateCount;
    }
    
    //go through each flat mate and tell them of their new duties
    foreach ($assignedJobs as $flatmateIndex => $jobsToDo)
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
            echo "<h2>" . $flatemate->Name . "</h2><br>";
            echo $message . "<br><br>";
        }
    }
}