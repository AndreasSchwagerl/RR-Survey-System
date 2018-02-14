# Web-based Survey System

<p align="center">
<b>
DROP TABLE team;
</b>
</p>
<p align="center">
<b>
Andreas Schwägerl,
Billy Leggett,
Christian Orr,
Gerald Cordova
</b>
</p>
<p align="center">
<a href="https://docs.google.com/document/d/1zyVqgEqLQ-gNRHQOU-UYxR8vgN_Kmk0Xd-oDxtj_oXE/edit">GoogleDoc</a> 
</p>

## Project Definition 

The purpose of this project is to develop a web-based survey system that handles questions containing both a positive and negative statement. Participants will be given a range of choices pertaining to which statement they most agree with. Each participant will only be able to submit the survey once and their responses will be kept anonymous.

The system will facilitate the breakdown of surveys by department. Reports can then be generated to observe the results for each department and for the company as a whole. This will provide administrators with a way to measure employee satisfaction and concerns.

There is currently no survey system on the market with the above feature set. This project aims to address that hole in the market, providing companies with a way to obtain metrics on performance through a unique approach to questions.

The project will be realized through a combination of web technologies (HTML, CSS) and scripting languages (JavaScript, PHP).


## Project Requirements  

The system should fulfill the following functional requirements:

- Surveys are to be conducted on a per company basis, further broken down by department.
- Surveys are generated using a list of comma delimited emails, broken down by department.
- Emails containing unique survey links are automatically sent to participants.
- Reminder emails can be generated and sent out to ensure maximum participation.
- Surveys will need to be fully completed before they can be submitted.
- Responses are to be store anonymously to protect employees.
- Excel reports can be generated to observe results, both ongoing and finalized.

The system should be designed with a user interface that is approachable and intuitive. Navigation between pages should be seamless and system functions should execute quickly.

The project will require a website to host the system and an online database to host the data. Both of these assets will need to be password protected to maintain security. The database will also feature IP blocking, ensuring that only permitted IP addresses can connect remotely.


## Project Specification 

As explained in the project definition, this project will be a survey system. This means entering a market along other systems such as Survey Monkey. While the core idea of being able to ask questions to a specific set of individuals remains the same here, there are a couple of notable differences between our system and other systems available. This includes:

- A statement on both sides of each “question” - one encompassing a positive response and the other encompassing a negative response.
- Removal of neutral, uncertain, or indifferent responses.

For this system, the database will store survey questions, survey statements, and participant response information. This database will be constructed using mySQL. Furthermore, we will need to provide a survey page for those completing the survey, as well as an administration subsystem to be able to manage surveys. This survey page and subsystem will all be accessible via modern browsers, therefore, we will need to use HTML and CSS to present information on-screen to a user. Beyond that, we will need to use JavaScript, as well as JQuery, and PHP, to build the logic behind this system.

This system will effectively be a web-based application. This means we have the liberty of not being concerned with specific hardware constraints, however, we will need to ensure that our system is fully operational on all modern browsers. This means our system must function normally on:

- Internet Explorer
- Microsoft Edge
- Safari
- Opera
- Firefox
- Google Chrome


## System – Design Perspective 

### Subsystems:
- Admin Page
  - Manage Survey Page
  - New Survey Page
  - Manage Questions Page
- Survey Page
- Database

![erm](https://user-images.githubusercontent.com/35355922/36083599-2e928444-0f82-11e8-8a64-5291fe21c0df.png)
  
![dataflow](https://user-images.githubusercontent.com/35355922/36083729-efef1a70-0f83-11e8-994e-a53e07504e08.png)
  



