# HouseChoreAssigner
House Chores sent out to room mates throughout the week

Set up a cronjob like so php /home/website/public_html/ChoreRepoDirectory/index.php SMS to send texts according to the schedule

Set up a twillioDetails.php file to set up your Twillio account details:
$account_sid = 'exampleSID'; 
$auth_token = 'exampleAuthToken'; 
$myNumber = "+1337number";

Set up 2 text files, Flatmates.txt and Jobs.txt which define the jobs and room mates to perform them

eg Flatmates.txt
Peter, 64864654654
Bejal, 4987987984
Lucy, 46545465465
Sabah, +447897987979

eg Jobs.txt
Thursday, Take Out Recycling, 0, and take recycling bin down the street for collection
Thursday, Take Out General Waste, 1, and take general waste bin down the street for collection
Tuesday, Feed the hamster
Monday, Do the laundry

The first 2 comma seperated values in a jobs line are mandatory. The 2nd set of variables are optional, and dictate what is to happen on even/odd weeks (eg bin collection that happens every other week)
