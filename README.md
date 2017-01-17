# HouseChoreAssigner
House Chores sent out to room mates throughout the week<br />

Set up a cronjob like so php /home/website/public_html/ChoreRepoDirectory/index.php SMS to send texts according to the schedule<br /><br />

Set up a twillioDetails.php file to set up your Twillio account details:<br />
$account_sid = 'exampleSID'; <br />
$auth_token = 'exampleAuthToken'; <br />
$myNumber = "+1337number";<br />
<br />
Set up 2 text files, Flatmates.txt and Jobs.txt which define the jobs and room mates to perform them<br />

eg Flatmates.txt<br />
Peter, 64864654654<br />
Bejal, 4987987984<br />
Lucy, 46545465465<br />
Sabah, +447897987979<br />

eg Jobs.txt<br />
Thursday, Take Out Recycling, 0, and take recycling bin down the street for collection<br />
Thursday, Take Out General Waste, 1, and take general waste bin down the street for collection<br />
Tuesday, Feed the hamster<br />
Monday, Do the laundry<br />
<br />
The first 2 comma seperated values in a jobs line are mandatory. The 2nd set of variables are optional, and dictate what is to happen on even/odd weeks (eg bin collection that happens every other week)
