php user manager 2.0+
=====================
updated 12/29/2014

This repo contains an open source LAMP user management application.

It is designed with the ARCV paradigm (Application-Router-Controller-View).

---
<h3>new in 2.0+</h3>
<ul>
<li>remote login and data retreival API</li>
</ul>

---

<h3>Database Features</h3>
<ul>
<li>auto initialization</li>
<li>update logging</li>
</ul>

<h3>User Features</h3>
<ul>
<li>2-step registration</li>
<li>sign in via email and password</li>
<li>user data edit page</li>
<li>forgot password function</li>
</ul>

<h3>Mobile Features</h3>
<ul>
<li>remote login and data retreival</li>
</ul>

<h3>Security Features</h3>
<ul>
<li>activation after email verification</li>
<li>encrypted email links</li>
<li>brute force cracking deterence</li>
<li>php 5.4 password_hash &amp; password_verify</li>
<li>inaccessable core files</li>
<li>immune to SQL injection</li>
<li>seperated logs for database updates, sql errors, and other errors</li>
<li>masterlog path for realtime monitoring</li>
</ul>

<h4><b>NOTE:</b> POST data is not encrypted, SSL should be integrated to ensure communication security</h4>

---

<h2>ARCV Design</h2>

Application - <i>app.php</i><br>
~ The application file contains all data specific to the application, including domain info, database credentials, database configurations, encryption keys, regex validations, email templates, and common strings. The only file that requires editing for deployment of included functionality.

Router - <i>root/index.php</i><br>
~ The index file handles general templating and contains a router for all paths.

Controller - <i>controller.php</i><br>
~ Paths that utilize POST data, take parameters, or perform multiple functions have a controller function located in the controller file.

View - <i>inc/</i><br>
~ All modular HTML/PHP segments are contained in view files.

---

<h3>Requirements</h3>

~ Server running PHP 5.4+ (may work with earlier versions, but not tested)<br>
~ MySQL Database v5.5 (may work with other versions, but not tested)

---

<h3>Installation</h3>

1) Create a new MySQL database<br>

2) Rename 'app-default.php' to 'app.php'<br>

3) Open 'app.php' and customize the data<br>

4) Set up a new domain path pointing to the folder 'your_site/root/'<br>

5) Upload all php-user-manager files to the folder 'your_site'<br>

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