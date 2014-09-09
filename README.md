php user manager
================

This repo contains an open source php user management application. It is minimal but robust, and very easily extendable. 

The software is NOT based on the MVC model. It is designed with a paradigm I call Application-Router-Controller-View, or ARCV.

The application has a debugger that tracks and displays all data interactions and failures, including SQL errors

---

<h3>User Features</h3>
<ul>
<li>sign in via email or username</li>
<li>user data edit page</li>
<li>password reset function</li>
<li>2-step registration and activation</li>
</ul>

<h3>Security Features</h3>
<ul>
<li>email verification after registration</li>
<li>REGEX email, username, and name validation</li>
<li>customizable password hashing routine</li>
<li>unique password salt for each user</li>
<li>variable database column and field names</li>
<li>inaccessable php files</li>
<li>hidden debug logs</li>
<li>404 routing for all gaurded paths</li>
<li>immune to SQL injection</li>
<li>protection against Session Hijacking</li>
<li>protection against Session Fixation</li>
</ul>

---

<h2>ARCV Design Paradigm</h2>

Application - <i>obj/app.php</i><br>
~ One Application object is instantiated and utilized for all data retreival and manipulation

Router - <i>root/index.php</i><br>
~ One index file contains routers for all paths and head elements

Controller - <i>ctr/</i><br>
~ All complex functions have seperate controllers that handle user input, function responses, and view routing

View - <i>inc/</i><br>
~ All modular HTML segments are contained in reusable view files

---

<h3>Requirements</h3>

~ Server running PHP 5.4+ (may work with 5.3, but not tested)<br>
~ MYSQL Database v5.5 (may work with 5.0, but not tested)

---

<h3>Installation</h3>

<ol>
<li>Set up a new MYSQL database and run the code contained in 'db.sql'<br>
~ NOTE: it would be wise to change the field labels, and especially the 'users' column name</li>

<li>Open 'inc/origin.inc' and set your database login credentials<br>
~ NOTE: you can also change the timezone here if desired</li>

<li>Open 'obj/app.php' and customize the SITE_KEY and hostname<br>
~ NOTE: also change the database labels if necessary</li>

<li>Open 'root/index.php' and customize the site title<br>
~ NOTE: a head router can be written to make the title dynamic</li>

<li>Open 'inc/emails.inc' and customize the email sender</li>

<li>Set up a new domain path pointing to the folder 'your_site/root/'</li>

<li>Upload all php-user-manager files to the folder 'your_site'</li>
</ol>

---

<b>php-user-manager is licensed under the BSD 2-clause Open Source license</b>

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
