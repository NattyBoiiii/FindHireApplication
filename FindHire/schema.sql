CREATE TABLE userAccounts (
    userID INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR (255),
    password TEXT,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE userInformation (
    userID INT AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR (255),
    lastName VARCHAR (255),
    age INT,
    currentAddress VARCHAR (300),
    emailAddress VARCHAR (200),
    phoneNumber VARCHAR (50),
    gender ENUM('Male', 'Female')NOT NULL,
    userStatus ENUM('HR', 'Applicant') NOT NULL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE jobDescriptions (
    jobDescID INT AUTO_INCREMENT PRIMARY KEY,
    userID INT,
    jobTitle VARCHAR(300),
    jobDesc VARCHAR (10000),
    date_posted TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE applicationsInfo (
    applicationID INT AUTO_INCREMENT PRIMARY KEY,
    userID INT,
    jobDescID INT,
    selfIntro VARCHAR (4000),
    attachment VARCHAR(100),
    applicationState ENUM('Evaluating', 'Accepted', 'Rejected'),
    date_established TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE inquiries (
    inquiryID INT AUTO_INCREMENT PRIMARY KEY,
    comDescription TEXT,
    userID INT,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE replies (
    replyID INT AUTO_INCREMENT PRIMARY KEY,
    replyDescription TEXT,
    inquiryID INT,
    userID INT,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);