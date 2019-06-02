/*****A Server - Key Server ******/
create table
A_User_Control
(
    UserID int unsigned AUTO_INCREMENT,
    User char(16),
    Pass char(60) not null,
    Statu tinyint default 0,
    primary key(User),
    unique(UserID)
);

create table
A_User_Info
(
    UserID int unsigned,
    Sex  char(1),
    Regdate DATETIME DEFAULT CURRENT_TIMESTAMP,
    VIPend DATETIME,
    mail varchar(31),
    Phone varchar(16),
    primary key(UserID)
);


create table
A_User_Key
(
    UserID int unsigned,
    FID int unsigned,
    FShare tinyint default 0,
    Fkey char(44),
    Fiv char(24),
    primary key(UserID,FID)
);

create table
A_User_Hash
(
    UserID int unsigned,
    FID int unsigned,
    FileHash char(64),
    primary key(UserID,FID)
);

/*****B Server - File Server ****/
create table
B_User_Control
(
    UserID int unsigned AUTO_INCREMENT,
    User char(16),
    Pass char(60) not null,
    Statu tinyint default 0,
    primary key(User),
    unique(UserID)
);

create table
B_User_Info
(
    UserID int unsigned,
    VIPend DATETIME,
    mail varchar(31),
    Phone varchar(16),
    StorageTotal bigint default 32768,
    StorageUsed bigint default 0,
    primary key(UserID)
);

create table
B_User_File_Index
(
    UserID int unsigned,
    FID int unsigned AUTO_INCREMENT,
    FName varchar(255) not null,
    Fsize int not null,
    FinDID int unsigned not null,
    FShare tinyint default 0,
    primary key(UserID,FID)
)ENGINE=MyISAM;

create table
B_User_DIR_Index
(
    UserID int unsigned,
    DID int unsigned AUTO_INCREMENT,
    DLevel int unsigned not null,
    DName varchar(255) not null,
    ParentID int unsigned not null,
    primary key(UserID,DID)
)ENGINE=MyISAM;


/***dela all table test ***/
SELECT concat('DROP TABLE ', table_name, ';')
FROM information_schema.tables
WHERE table_schema = 'localsite';

 DROP TABLE A_User_Control;             
 DROP TABLE A_User_DIR;                 
 DROP TABLE A_User_Info;                
 DROP TABLE A_User_Key; 
 DROP TABLE A_User_Hash;                
 DROP TABLE B_User_Control;             
 DROP TABLE B_User_DIR_Index;           
 DROP TABLE B_User_File_Index;          
 DROP TABLE B_User_Control;             
 DROP TABLE B_User_Info;  

 insert into B_User_File_Index(UserID,FName,FinDID) values (1,'1.txt',0);