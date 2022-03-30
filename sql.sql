create table students (
	stu_id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	stud_unique_id int(11) NOT NULL,
	stud_fname varchar(50) NOT NULL,
	stud_lname varchar(50) NOT NULL,
	stud_oname varchar(50) NOT NULL,
	stud_tel varchar(15) NOT NULL,
	stud_email varchar(100) NOT NULL,
	stud_regNo varchar(20) NOT NULL,
	stud_dept varchar(50) NOT NULL,
	level varchar(50) NOT NULL default "ND",
	stud_password varchar(255) NOT NULL,
	assigned_supervisor_unid int(11) NULL,
	ind_supervisor_unid int(11) NULL,
	stud_date_joined date NOT NULL,
	stud_last_login date NOT NULL,
	deleted int(2) NOT NULL default 0,
	suspened int(2) NOT NULL default 0

);

create table admin (
	id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	admin_fullname varchar(255) NOT NULL,
	admin_phone_no varchar(11) NOT NULL,
	admin_email varchar(255) NOT NULL,
	admin_username varchar(20) NOT NULL,
	admin_password varchar(255) NOT NULL,
	admin_permissions varchar(150) NOT NULL,
	admin_status varchar(5) NOT NULL default "off",
	admin_email_verified int(2) NOT NULL default 0,
	admin_date_added date NOT NULL,
	admin_last_login date NOT NULL,
	suspened int(2) NOT NULL default 0
);

create table logbook (
	id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	assigned_supervisor_unid int(11) NOT NULL,
	stu_id int(11) NOT NULL,
	week_number int(11)  NOT  NULL,
	log_day date  NOT  NULL,
	log_date date  NOT  NULL,
	comments text NOT  NULL,
	projectORjobDone text NULL,
	section varchar(255)  NULL,
	certifiedBy varchar(255)  NULL,
	certifiedDate date  NULL,
	certifiedSign varchar(255)  NULL,
	log_month date   NULL,
	sketches varchar(255)  NULL,
	com_by_ind_sup text NULL,
	nameOfSup varchar(255) NULL,
	signature varchar(200) NULL,
	designation varchar(100) NULL,
	train_tut_comment text NULL,
	trainTutSignature varchar(255) NULL,
	trainComDate date NULL

);

create table supervisors (
	id int(11) NOT NULL AUTO_INCREMENT PRIMARY,
	admin_id int(11) NOT NULL,
	unique_id int(11) NOT NULL,
	location_address varchar(255) NOT NULL,
	department varchar(100) NOT NULL,
	dateAdded TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	deleted int(2) NOT NULL default 0
);

create table inds_supervisors (
	id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	unique_id int(11) NOT NULL,
	fullname varchar(200) NOT NULL,
	phoneNo varchar(15) NOT NULL,
	comp_email varchar(255) NOT NULL,
	password varchar(200) NOT NULL,
	dateAdded TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	deleted int(2) NOT NULL default 0

);

create table placementInfo (
	id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	stud_unique_id int(11) NOT NULL,
	nameOfEst varchar(255) NOT NULL,
	location varchar(255) NOT NULL,
	yearOpStarted year(4) NULL,
	prinAreaOp varchar(200) NOT NULL,
	prod_undertaken varchar(255) NOT NULL,
	employmentSize varchar(100) NOT NULL,
	deleted int(2) NOT NULL default 0

);

create table notification (
	id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	students varchar(10) NULL,
	swies_cod varchar(10) NULL,
	title varchar(255) NOT NULL,
	message text NOT NULL,
	dateSent TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	deleted int(2) NOT NULL default 0

);

create table secureOtp (
	id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	user_email varchar(255) NOT NULL,
	secure_token int(8) NOT NULL,
	dateSent TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	status varchar(10) NOT NULL default "unused"
);

create table adminOtp (
	id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	admin_email varchar(255) NOT NULL,
	secure_token int(8) NOT NULL,
	dateSent TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	status varchar(10) NOT NULL default "unused"
);

create table secureQuestion (
	id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	user_email varchar(255) NOT NULL,
	secure_question varchar(100) NOT NULL,
	secure_answer varchar(100) NOT NULL,
	dateSent TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	status varchar(10) NOT NULL default 0
);
