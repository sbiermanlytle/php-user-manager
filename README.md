php user manager
================

This repo contains an open source php user management application. It is minimal but robust, and very easily extendable. 

The software is NOT based on the MVC model. It is designed with a paradigm I call Application-Router-Controller-View, or ARCV.

The application has a debugger that tracks and displays all data interactions and failures, including SQL errors

---

User Features
~ sign in via email or username
~ user data edit page
~ password reset function
~ 2-step registration and activation

Security Features
~ email verification after registration
~ REGEX email, username, and name validation
~ customizable password hashing routine
~ unique password salt for each user
~ variable database column and field names
~ inaccessable php files
~ hidden debug logs
~ 404 routing for all gaurded paths
~ immune to SQL injection
~ protection against Session Hijacking
~ protection against Session Fixation

---

ARCV Design Paradigm

Application - obj/app.php
~ One Application object is instantiated and utilized for all data retreival and manipulation

Router - root/index.php
~ One index file contains routers for all paths and head elements

Controller - ctr/
~ All complex functions have seperate controllers that handle user input, function responses, and view routing

View - inc/
~ All modular HTML segments are contained in reusable view files

---

Requirements

~ Server running PHP 5.4+ (may work with 5.3, but not tested)
~ MYSQL Database v5.5 (may work with 5.0, but not tested)

---

Installation

1) Set up a new MYSQL database and run the code contained in 'db.sql'
~ NOTE: it would be wise to change the field labels, and especially the 'users' column name

2) Open 'inc/origin.inc' and set your database login credentials
~ NOTE: you can also change the timezone here if desired

3) Open 'obj/app.php' and customize the SITE_KEY and hostname
~ NOTE: also change the database labels if necessary 

4) Open 'root/index.php' and customize the site title
~ NOTE: a head router can be written to make the title dynamic

5) Open 'inc/emails.inc' and customize the email sender

6) Set up a new domain path pointing to the folder 'your_site/root/'

7) Upload all php-user-manager files to the folder 'your_site'

---

php-user-manager is licensed under the BSD 2-clause Open Source license

Copyright (c) 2014, Sebastian Bierman-Lytle
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, 
are permitted provided that the following conditions are met:

Redistributions of source code must retain the above copyright notice, this list 
of conditions and the following disclaimer.

Redistributions in binary form must reproduce the above copyright notice, this
list of conditions and the following disclaimer in the documentation and/or other 
materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND 
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, 
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT 
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, 
OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
POSSIBILITY OF SUCH DAMAGE.
