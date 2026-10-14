# ConsumerConnect RSA

A Consumer-to-Consumer Marketplace Web Application

ConsumerConnect RSA is a web development project focused on a consumer-to-consumer (C2C) marketplace concept. The application is designed to support user accounts, product listings, buyer and seller workflows, and marketplace administration.

The project explores how web technologies and database-driven application design can be used to build an organised online marketplace.

Project Overview

ConsumerConnect RSA aims to provide a platform where users can interact with marketplace functionality, including browsing products, managing accounts, and creating product listings.

The repository contains PHP application pages, front-end assets, API-related files, and SQL scripts for the database structure and sample data.

Technologies

The repository includes the following technologies and resources:

PHP — Server-side application development.

JavaScript — Client-side scripting and interactive functionality.

CSS — Styling and presentation.

MySQL — Relational database structure and data storage.

SQL — Database schema and seed scripts.

HTML — Web page structure.

Git and GitHub — Source code version control and project hosting.

Features and Application Areas

The repository contains code and resources relating to the following areas:

User accounts: Registration, login, authentication and logout pages.

User profiles: Profile management and account-related functionality.

Product listings: Product browsing and listing-related pages.

Marketplace workflows: Cart, checkout and order-related pages.

Dashboard: A central dashboard interface.

Administration: An administration area for marketplace management.

Information pages: About, contact, help centre, privacy policy, terms of service and safety information.

Database resources: SQL files for defining and populating database tables.

Supporting resources: CSS, JavaScript, images, uploads and API-related files.

The presence of these files does not necessarily mean every feature is fully implemented or tested.

Repository Structure

ConsumerConnect_RSA/
├── Admin/
├── Images/
├── api/
├── css/
├── js/
├── uploads/
├── about.php
├── auth.php
├── cart.php
├── checkout.php
├── config.php
├── contact-us.php
├── dashboard.php
├── index.php
├── login.php
├── logout.php
├── orders.php
├── products.php
├── profile.php
├── register.php
├── schema.sql
├── seed.sql
├── sell.php
├── style.css
└── README.md

The structure above highlights the main files and directories visible in the repository. Additional files are available in the project.

Getting Started

Prerequisites

To run the application locally, you will need:

A local PHP development environment, such as XAMPP.

PHP with PDO and the MySQL PDO driver enabled.

MySQL or a compatible database server.

A web browser.

Git, if you want to clone the repository.

Installation

1. Clone the repository

git clone https://github.com/NtsakoMaluleke/ConsumerConnect_RSA.git

2. Move the project into your web server directory

For XAMPP on Windows, copy or clone the project into the htdocs directory.

For example:

C:\xampp\htdocs\ConsumerConnect_RSA

3. Start the local services

Open the XAMPP Control Panel and start Apache and MySQL.

4. Create the database

Open phpMyAdmin or your preferred MySQL client. Review schema.sql, then execute it to create the database structure.

Review seed.sql and execute it if you want to load the project's sample data.

5. Configure the database connection

Review config.php and configure the database host, port, database name, username and password for your local environment.

The configuration file supports environment variables for database settings. Use your own local credentials rather than publishing passwords in the repository.

6. Open the application

Use your local web server address to access the project. For example:

http://localhost/ConsumerConnect_RSA/

The exact URL may need adjustment to match the configured site URL and the directory structure of your local environment.

Note: The installation instructions are a starting point based on the repository structure. Verify the database scripts, configuration and local URL before treating the setup as complete.

Database

The repository includes two SQL resources:

schema.sql — Defines the database and its tables.

seed.sql — Provides seed data for development, subject to the contents of the script.

Review the scripts before importing them into an existing database. Do not use development credentials or sample administrator accounts in a production deployment.

Security Considerations

Before deploying the application publicly:

Store database credentials securely.

Remove or replace any default or demonstration administrator credentials.

Validate and sanitise user input on the server.

Use secure password hashing and session handling.

Validate uploaded files and restrict upload permissions.

Apply appropriate authorisation checks to protected pages and API endpoints.

Use HTTPS and review privacy and data-protection requirements.

These are recommended security checks, not a claim that every item has already been implemented.

Current Development Status

This repository contains the application structure, PHP pages, front-end resources and SQL database files for the marketplace project.

The completeness and reliability of individual features should be confirmed through installation and testing.

Potential next development steps include:

Testing registration, login and logout workflows.

Verifying database connectivity and CRUD operations.

Testing product listing and account management workflows.

Validating role-based access to administrative pages.

Testing cart and checkout behaviour.

Improving error handling, input validation and security.

Adding screenshots and documenting verified functionality.

Learning Objectives

This project provides an opportunity to develop practical skills in:

Server-side web development with PHP.

Relational database design and SQL.

Client-side scripting and interface styling.

Application structure and source code organisation.

Debugging and testing web applications.

Version control using Git and GitHub.

Author

Ntsako Maluleke

BSc Information Technology — Software Engineering student at Eduvos Menlyn.

GitHub: NtsakoMaluleke

LinkedIn: Ntsako Maluleke

License

No license has been specified in this README. Add a license file if you intend to define permissions for reuse, modification and distribution.

This project is part of my software development learning journey and demonstrates my engagement with web application development, PHP, JavaScript and relational databases.
