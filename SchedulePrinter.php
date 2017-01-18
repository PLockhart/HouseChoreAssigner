<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function PrintSchedule($assignedJobs, $flatmates, $weekNumber, $newLineString)
{
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

            $message .= $newLineString;
        }

        echo "<h2>" . $flatemate->Name . "</h2>";
        echo $message;
    }
}