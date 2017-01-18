<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function GetAssignedJobs($flatmates, $jobs, $dayNumber, $weekNumber, &$assignedJobs)
{    
    $flatmateOffset = $weekNumber % count($flatmates);

    $flatmateCount = count($flatmates);
    
    $numJobsAssigned = 0;
    
    //go through all of the jobs and assign to a flat mate
    foreach ($jobs as $loopedJob)
    {
        if (!isset($assignedJobs[$flatmateOffset]))
        {
            $assignedJobs[$flatmateOffset] = array();
        }
        
        if ($loopedJob->Day == $dayNumber)
        {
            array_push($assignedJobs[$flatmateOffset], $loopedJob);

            $flatmateOffset = ($flatmateOffset + 1) % $flatmateCount;
            
            $numJobsAssigned++;
        }
    }
    
    //if the last flatmate didn't get any jobs, unset their array
    if (isset($assignedJobs[$flatmateOffset]) && count($assignedJobs[$flatmateOffset]) == 0)
    {
        unset($assignedJobs[$flatmateOffset]);   
    }
    
    return $numJobsAssigned;
}
