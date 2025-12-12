E-Ballot System

A secure, dynamic, full-featured Online Voting System built using PHP & MySQL.
Supports candidate applications, admin approval, voting, and result publishing, offering a complete digital election solution.

___________________________________________________________________________________________________________________________________________
***KEY FEATURES***
___________________________________________________________________________________________________________________________________________
  1.Admin Panel
  --------------
  -Create and manage elections
  -Add posts (Chairman, Secretary, Treasurer, etc.)
  -Review candidate applications
  -Approve or reject candidates
  -Manage voters
  -Prevent multiple votes per user
  -Publish results after the election end date
  -View total votes per candidate
  

  2.User (Voter) Panel
  ---------------------
  -Login using email & password
  -View eligible elections
  -View approved candidates
  -Vote once per post
  -Check election results (only after admin publishes)
  

  3.Candidate Panel
  ------------------
  -Apply to contest for a post
  -Upload photos & required details
  -Select post they want to contest
  -Wait for admin approval
  -Only approved candidates appear on the voting screen

___________________________________________________________________________________________________________________________________________
***SYSTEM WORKFLOW***
__________________________________________________________________________________________________________________________________________
  User Registers → Logs In  
            ↓
  Candidate Applies for a Post
            ↓
  Admin Verifies Application
            ↓
  Admin Approves / Rejects Candidate
            ↓
  Only Approved Candidates Shown to Voters
            ↓
  Voters Vote Once per Post
            ↓
  Votes Stored in Database (Total Count Only)
            ↓
  Admin Publishes Result
            ↓
  Users Can View Result

  
___________________________________________________________________________________________________________________________________________
***TECH STACK***
___________________________________________________________________________________________________________________________________________
| Layer           | Technology            |
| --------------- | --------------------- |
| Frontend        | HTML, CSS, JavaScript |
| Backend         | PHP                   |
| Database        | MySQL                 |
| Server          | XAMPP                 |
| Version Control | Git & GitHub          |


___________________________________________________________________________________________________________________________________________
***PROJECT STRUCTURE***
___________________________________________________________________________________________________________________________________________
E-Ballot/


├── admin/               → Admin dashboard, approvals, results  
├── user/                → Voter pages (vote, view result)  
├── candidate/           → Candidate application pages  
├── post_details/        → Posts for each election  
├── uploads/             → Candidate photos  
├── database/            → SQL database files  
├── config.php           → DB connection  
├── index.php            → Home/Login page  
└── README.md            → Documentation



    Developer
    Gouri Nandana G
    MCA Student
    GitHub: https://github.com/Gouri4666
